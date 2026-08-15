<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendWeeklyNewsletterJob;
use App\Mail\TestNewsletterMail;
use App\Mail\WeeklyDigestMail;
use App\Models\Blog;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterEmailLog;
use App\Models\NewsletterSetting;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function dashboard()
    {
        $totalSubscribers = NewsletterSubscriber::count();
        $activeSubscribers = NewsletterSubscriber::where('status', 'active')->count();
        $unsubscribedSubscribers = NewsletterSubscriber::where('status', 'unsubscribed')->count();
        $bouncedSubscribers = NewsletterSubscriber::where('status', 'bounced')->count();

        $sentToday = NewsletterEmailLog::where('status', 'sent')
            ->whereDate('sent_at', now())
            ->count();

        $sentThisWeek = NewsletterEmailLog::where('status', 'sent')
            ->whereBetween('sent_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $failedCount = NewsletterEmailLog::where('status', 'failed')->count();

        $lastCampaign = NewsletterCampaign::latest('created_at')->first();

        // Calculate next Monday 5:00 AM IST
        $nextWeekly = now('Asia/Kolkata')->next(\Carbon\Carbon::MONDAY)->setTime(5, 0);

        $recentCampaigns = NewsletterCampaign::latest()->take(5)->get();

        return view('admin.newsletter.dashboard', compact(
            'totalSubscribers',
            'activeSubscribers',
            'unsubscribedSubscribers',
            'bouncedSubscribers',
            'sentToday',
            'sentThisWeek',
            'failedCount',
            'lastCampaign',
            'nextWeekly',
            'recentCampaigns'
        ));
    }

    public function subscribers(Request $request)
    {
        $query = NewsletterSubscriber::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $subscribers = $query->latest('created_at')->paginate(20)->withQueryString();

        $counts = [
            'all' => NewsletterSubscriber::count(),
            'active' => NewsletterSubscriber::where('status', 'active')->count(),
            'unsubscribed' => NewsletterSubscriber::where('status', 'unsubscribed')->count(),
            'bounced' => NewsletterSubscriber::where('status', 'bounced')->count(),
            'inactive' => NewsletterSubscriber::where('status', 'inactive')->count(),
        ];

        return view('admin.newsletter.subscribers.index', compact('subscribers', 'counts'));
    }

    public function storeSubscriber(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email:rfc,dns|unique:newsletter_subscribers,email',
            'name' => 'nullable|string|max:255',
            'status' => 'required|in:active,unsubscribed,bounced,inactive',
        ]);

        NewsletterSubscriber::create([
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'status' => $validated['status'],
            'unsubscribe_token' => Str::random(64),
            'subscribed_at' => now(),
        ]);

        return back()->with('status', 'Subscriber added successfully.');
    }

    public function updateSubscriber(Request $request, NewsletterSubscriber $subscriber)
    {
        $validated = $request->validate([
            'email' => 'required|email:rfc,dns|unique:newsletter_subscribers,email,' . $subscriber->id,
            'name' => 'nullable|string|max:255',
            'status' => 'required|in:active,unsubscribed,bounced,inactive',
            'notify_new_post' => 'nullable|boolean',
            'notify_weekly_digest' => 'nullable|boolean',
        ]);

        $subscriber->update([
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'status' => $validated['status'],
            'notify_new_post' => $request->boolean('notify_new_post'),
            'notify_weekly_digest' => $request->boolean('notify_weekly_digest'),
            'unsubscribed_at' => ($validated['status'] === 'unsubscribed') ? ($subscriber->unsubscribed_at ?: now()) : null,
        ]);

        return back()->with('status', 'Subscriber updated successfully.');
    }

    public function destroySubscriber(NewsletterSubscriber $subscriber)
    {
        $subscriber->delete();

        return back()->with('status', 'Subscriber deleted successfully.');
    }

    public function toggleSubscriberStatus(NewsletterSubscriber $subscriber)
    {
        $newStatus = ($subscriber->status === 'active') ? 'inactive' : 'active';
        $subscriber->update([
            'status' => $newStatus,
            'unsubscribed_at' => ($newStatus === 'unsubscribed') ? now() : null,
        ]);

        return back()->with('status', "Subscriber status set to {$newStatus}.");
    }

    public function logs(Request $request)
    {
        $query = NewsletterEmailLog::with(['subscriber', 'blog', 'campaign']);

        if ($search = $request->query('search')) {
            $query->whereHas('subscriber', function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($type = $request->query('type')) {
            $query->where('campaign_type', $type);
        }

        $logs = $query->latest('created_at')->paginate(25)->withQueryString();

        $stats = [
            'total' => NewsletterEmailLog::count(),
            'sent' => NewsletterEmailLog::where('status', 'sent')->count(),
            'failed' => NewsletterEmailLog::where('status', 'failed')->count(),
            'queued' => NewsletterEmailLog::where('status', 'queued')->count(),
        ];

        return view('admin.newsletter.logs', compact('logs', 'stats'));
    }

    public function settings()
    {
        $settings = NewsletterSetting::getAllSettings();

        return view('admin.newsletter.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'from_name' => 'required|string|max:255',
            'from_email' => 'required|email|max:255',
            'reply_to' => 'required|email|max:255',
            'enable_new_post_notifications' => 'nullable|boolean',
            'enable_weekly_digest' => 'nullable|boolean',
            'weekly_send_day' => 'required|string',
            'weekly_send_time' => 'required|string',
            'timezone' => 'required|string',
            'batch_size' => 'required|integer|min:10|max:1000',
            'default_email_footer' => 'required|string|max:1000',
        ]);

        NewsletterSetting::setValue('from_name', $validated['from_name']);
        NewsletterSetting::setValue('from_email', $validated['from_email']);
        NewsletterSetting::setValue('reply_to', $validated['reply_to']);
        NewsletterSetting::setValue('enable_new_post_notifications', $request->boolean('enable_new_post_notifications') ? '1' : '0');
        NewsletterSetting::setValue('enable_weekly_digest', $request->boolean('enable_weekly_digest') ? '1' : '0');
        NewsletterSetting::setValue('weekly_send_day', $validated['weekly_send_day']);
        NewsletterSetting::setValue('weekly_send_time', $validated['weekly_send_time']);
        NewsletterSetting::setValue('timezone', $validated['timezone']);
        NewsletterSetting::setValue('batch_size', (string) $validated['batch_size']);
        NewsletterSetting::setValue('default_email_footer', $validated['default_email_footer']);

        return back()->with('status', 'Newsletter settings updated successfully.');
    }

    public function sendTest(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email:rfc,dns',
        ]);

        try {
            Mail::to($validated['email'])->send(new TestNewsletterMail());

            $driver = config('mail.default');
            if ($driver === 'log') {
                return back()->with('status', "Test email processed via local 'log' driver (written to storage/logs/laravel.log). Configure MAIL_MAILER=smtp in .env for real SMTP delivery.");
            }

            return back()->with('status', "Test email sent successfully via SMTP to {$validated['email']}.");
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            $smtpPass = (string) config('mail.mailers.smtp.password');
            if ($smtpPass !== '') {
                $msg = str_replace($smtpPass, '********', $msg);
            }

            if (str_contains(strtolower($msg), 'auth') || str_contains(strtolower($msg), '535') || str_contains(strtolower($msg), 'credential')) {
                $errorMsg = 'SMTP authentication failed. Check MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD and MAIL_ENCRYPTION in .env.';
            } elseif (str_contains(strtolower($msg), 'connection') || str_contains(strtolower($msg), 'refused') || str_contains(strtolower($msg), 'timeout')) {
                $errorMsg = 'SMTP connection failed. Verify MAIL_HOST, MAIL_PORT, and network settings.';
            } else {
                $errorMsg = 'Failed to send test email: ' . $msg;
            }

            return back()->withErrors(['email' => $errorMsg]);
        }
    }

    public function previewWeekly()
    {
        $start = now('Asia/Kolkata')->subWeek()->startOfWeek();
        $end = now('Asia/Kolkata')->subWeek()->endOfWeek();

        $blogs = Blog::with('category')
            ->published()
            ->whereBetween('published_at', [$start, $end])
            ->latest('published_at')
            ->get();

        if ($blogs->isEmpty()) {
            $blogs = Blog::with('category')
                ->published()
                ->latest('published_at')
                ->take(6)
                ->get();
        }

        $groupedBlogs = $blogs->groupBy(function ($blog) {
            return $blog->category?->name ?: 'GENERAL NEWS';
        });

        $mailable = new WeeklyDigestMail($groupedBlogs);

        return $mailable->render();
    }

    public function triggerWeeklyNow()
    {
        SendWeeklyNewsletterJob::dispatch();

        return back()->with('status', 'Weekly digest job dispatched to queue successfully.');
    }
}

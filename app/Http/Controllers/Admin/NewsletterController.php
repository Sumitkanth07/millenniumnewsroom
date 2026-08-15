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
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function dashboard()
    {
        $hasSubscribersTable = Schema::hasTable('newsletter_subscribers');
        $hasLogsTable = Schema::hasTable('newsletter_email_logs');
        $hasCampaignsTable = Schema::hasTable('newsletter_campaigns');

        $totalSubscribers = $hasSubscribersTable ? NewsletterSubscriber::count() : 0;
        $activeSubscribers = $hasSubscribersTable ? NewsletterSubscriber::where('status', 'active')->count() : 0;
        $unsubscribedSubscribers = $hasSubscribersTable ? NewsletterSubscriber::where('status', 'unsubscribed')->count() : 0;
        $bouncedSubscribers = $hasSubscribersTable ? NewsletterSubscriber::where('status', 'bounced')->count() : 0;

        $sentToday = $hasLogsTable ? NewsletterEmailLog::where('status', 'sent')->whereDate('sent_at', now())->count() : 0;
        $sentThisWeek = $hasLogsTable ? NewsletterEmailLog::where('status', 'sent')->whereBetween('sent_at', [now()->startOfWeek(), now()->endOfWeek()])->count() : 0;
        $failedCount = $hasLogsTable ? NewsletterEmailLog::where('status', 'failed')->count() : 0;

        $lastCampaign = $hasCampaignsTable ? NewsletterCampaign::latest('created_at')->first() : null;
        $recentCampaigns = $hasCampaignsTable ? NewsletterCampaign::latest()->take(5)->get() : collect();

        try {
            $nextWeekly = now('Asia/Kolkata')->next(\Carbon\Carbon::MONDAY)->setTime(5, 0);
        } catch (\Throwable $e) {
            $nextWeekly = now('Asia/Kolkata')->addWeek()->startOfWeek()->setTime(5, 0);
        }

        if (!$hasSubscribersTable || !$hasCampaignsTable) {
            session()->flash('warning', 'Newsletter database tables are not migrated yet. Please run "php artisan migrate" on the server.');
        }

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
        if (!Schema::hasTable('newsletter_subscribers')) {
            session()->flash('warning', 'Newsletter database tables are not migrated yet. Please run "php artisan migrate" on the server.');
            $subscribers = new LengthAwarePaginator([], 0, 20);
            $counts = ['all' => 0, 'active' => 0, 'unsubscribed' => 0, 'bounced' => 0, 'inactive' => 0];
            return view('admin.newsletter.subscribers.index', compact('subscribers', 'counts'));
        }

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
        if (!Schema::hasTable('newsletter_email_logs')) {
            session()->flash('warning', 'Newsletter database tables are not migrated yet. Please run "php artisan migrate" on the server.');
            $logs = new LengthAwarePaginator([], 0, 25);
            $stats = ['total' => 0, 'sent' => 0, 'failed' => 0, 'queued' => 0];
            return view('admin.newsletter.logs', compact('logs', 'stats'));
        }

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
            'from_email' => ['required', 'email', function ($attribute, $value, $fail) {
                $domain = strtolower(substr(strrchr($value, "@"), 1));
                if ($domain !== 'millenniumnewsroom.com') {
                    $fail('The From Email address must use the verified domain (@millenniumnewsroom.com).');
                }
            }],
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

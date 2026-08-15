<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterSubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email:rfc,dns|max:255',
            'name' => 'nullable|string|max:255',
        ]);

        $subscriber = NewsletterSubscriber::firstOrNew(['email' => strtolower(trim($validated['email']))]);

        $subscriber->name = $validated['name'] ?? $subscriber->name;
        $subscriber->status = 'active';
        $subscriber->notify_new_post = true;
        $subscriber->notify_weekly_digest = true;
        $subscriber->subscribed_at = now();
        $subscriber->unsubscribed_at = null;
        if (empty($subscriber->unsubscribe_token)) {
            $subscriber->unsubscribe_token = Str::random(64);
        }
        $subscriber->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you for subscribing to MILLENNIUM NEWSROOM briefings.',
            ]);
        }

        return back()->with('newsletter_status', 'Thank you for subscribing to MILLENNIUM NEWSROOM briefings.');
    }

    public function unsubscribeToken(string $token)
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->first();

        if (!$subscriber) {
            return view('newsletter.unsubscribe', [
                'subscriber' => null,
                'error' => 'Invalid or expired unsubscribe link.',
            ]);
        }

        return view('newsletter.unsubscribe', [
            'subscriber' => $subscriber,
            'error' => null,
        ]);
    }

    public function unsubscribePost(Request $request, string $token)
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->firstOrFail();

        $subscriber->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ]);

        return view('newsletter.unsubscribe_confirmed', [
            'subscriber' => $subscriber,
        ]);
    }

    public function preferences(string $token)
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->firstOrFail();

        return view('newsletter.preferences', [
            'subscriber' => $subscriber,
        ]);
    }

    public function updatePreferences(Request $request, string $token)
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->firstOrFail();

        if ($request->boolean('unsubscribe_all')) {
            $subscriber->update([
                'status' => 'unsubscribed',
                'unsubscribed_at' => now(),
            ]);

            return back()->with('status', 'You have been unsubscribed from all newsletters.');
        }

        $notifyNew = $request->boolean('notify_new_post');
        $notifyWeekly = $request->boolean('notify_weekly_digest');

        $status = ($notifyNew || $notifyWeekly) ? 'active' : 'unsubscribed';

        $subscriber->update([
            'notify_new_post' => $notifyNew,
            'notify_weekly_digest' => $notifyWeekly,
            'status' => $status,
            'unsubscribed_at' => ($status === 'unsubscribed') ? now() : null,
        ]);

        return back()->with('status', 'Your newsletter preferences have been updated successfully.');
    }
}

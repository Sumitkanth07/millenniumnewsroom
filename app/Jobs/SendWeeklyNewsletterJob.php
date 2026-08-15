<?php

namespace App\Jobs;

use App\Models\Blog;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSetting;
use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWeeklyNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $enabled = NewsletterSetting::getValue('enable_weekly_digest', '1');
        if ($enabled !== '1' && $enabled !== true && $enabled !== 1) {
            return;
        }

        $now = now('Asia/Kolkata');
        $weekYear = $now->format('Y-\WW'); // e.g. 2026-W33
        $campaignKey = 'weekly-' . $weekYear;

        // Check duplicate campaign for current week
        $existing = NewsletterCampaign::where('campaign_key', $campaignKey)->first();
        if ($existing && in_array($existing->status, ['completed', 'processing', 'queued'])) {
            return;
        }

        $subscribers = NewsletterSubscriber::active()
            ->where('notify_weekly_digest', true)
            ->get();

        if ($subscribers->isEmpty()) {
            return;
        }

        $campaign = NewsletterCampaign::create([
            'title' => 'Weekly Digest (' . $now->startOfWeek()->format('M d') . ' - ' . $now->endOfWeek()->format('M d, Y') . ')',
            'campaign_type' => 'weekly_digest',
            'campaign_key' => $campaignKey,
            'status' => 'processing',
            'total_subscribers' => $subscribers->count(),
            'sent_count' => 0,
            'failed_count' => 0,
            'started_at' => now(),
        ]);

        $batchSize = (int) NewsletterSetting::getValue('batch_size', 100);
        $batchSize = max(10, min($batchSize, 1000));

        $chunks = $subscribers->pluck('id')->chunk($batchSize);

        foreach ($chunks as $subscriberIds) {
            SendBatchNewsletterJob::dispatch($campaign, $subscriberIds->toArray());
        }
    }
}

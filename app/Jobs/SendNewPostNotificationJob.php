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

class SendNewPostNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Blog $blog;

    public function __construct(Blog $blog)
    {
        $this->blog = $blog;
    }

    public function handle(): void
    {
        $enabled = NewsletterSetting::getValue('enable_new_post_notifications', '1');
        if ($enabled !== '1' && $enabled !== true && $enabled !== 1) {
            return;
        }

        $campaignKey = 'post-' . $this->blog->id;

        // Check if campaign already exists
        $existing = NewsletterCampaign::where('campaign_key', $campaignKey)->first();
        if ($existing && in_array($existing->status, ['completed', 'processing', 'queued'])) {
            return;
        }

        $subscribers = NewsletterSubscriber::active()
            ->where('notify_new_post', true)
            ->get();

        if ($subscribers->isEmpty()) {
            return;
        }

        $campaign = NewsletterCampaign::create([
            'title' => 'New Article: ' . $this->blog->title,
            'campaign_type' => 'new_post',
            'campaign_key' => $campaignKey,
            'post_id' => $this->blog->id,
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

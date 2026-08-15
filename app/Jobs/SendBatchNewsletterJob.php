<?php

namespace App\Jobs;

use App\Mail\NewPostNotificationMail;
use App\Mail\WeeklyDigestMail;
use App\Models\Blog;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterEmailLog;
use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendBatchNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;
    public int $timeout = 300;

    public NewsletterCampaign $campaign;
    public array $subscriberIds;

    public function __construct(NewsletterCampaign $campaign, array $subscriberIds)
    {
        $this->campaign = $campaign;
        $this->subscriberIds = $subscriberIds;
    }

    public function handle(): void
    {
        $campaign = $this->campaign->fresh(['blog']);
        if (!$campaign) {
            return;
        }

        $subscribers = NewsletterSubscriber::whereIn('id', $this->subscriberIds)
            ->where('status', 'active')
            ->get();

        foreach ($subscribers as $subscriber) {
            // Check preference based on campaign type
            if ($campaign->campaign_type === 'new_post' && !$subscriber->notify_new_post) {
                continue;
            }
            if ($campaign->campaign_type === 'weekly_digest' && !$subscriber->notify_weekly_digest) {
                continue;
            }

            // Duplicate check per subscriber for this campaign
            $existingLog = NewsletterEmailLog::where('campaign_id', $campaign->id)
                ->where('subscriber_id', $subscriber->id)
                ->where('status', 'sent')
                ->first();

            if ($existingLog) {
                continue;
            }

            try {
                if ($campaign->campaign_type === 'new_post' && $campaign->blog) {
                    Mail::to($subscriber->email)->send(new NewPostNotificationMail($campaign->blog, $subscriber));
                } elseif ($campaign->campaign_type === 'weekly_digest') {
                    // Reconstruct weekly grouped posts
                    $groupedBlogs = $this->getWeeklyGroupedBlogs();
                    Mail::to($subscriber->email)->send(new WeeklyDigestMail($groupedBlogs, $subscriber));
                }

                NewsletterEmailLog::updateOrCreate(
                    [
                        'campaign_id' => $campaign->id,
                        'subscriber_id' => $subscriber->id,
                    ],
                    [
                        'post_id' => $campaign->post_id,
                        'campaign_type' => $campaign->campaign_type,
                        'status' => 'sent',
                        'sent_at' => now(),
                        'error_message' => null,
                    ]
                );

                $subscriber->update([
                    'last_email_sent_at' => now(),
                    'last_delivery_status' => 'sent',
                ]);

                $campaign->increment('sent_count');
            } catch (Throwable $e) {
                NewsletterEmailLog::updateOrCreate(
                    [
                        'campaign_id' => $campaign->id,
                        'subscriber_id' => $subscriber->id,
                    ],
                    [
                        'post_id' => $campaign->post_id,
                        'campaign_type' => $campaign->campaign_type,
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                    ]
                );

                $subscriber->update([
                    'last_delivery_status' => 'failed',
                ]);

                $campaign->increment('failed_count');
            }
        }

        // Check if campaign is completed
        $totalProcessed = $campaign->sent_count + $campaign->failed_count;
        if ($totalProcessed >= $campaign->total_subscribers) {
            $campaign->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }
    }

    protected function getWeeklyGroupedBlogs()
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

        return $blogs->groupBy(function ($blog) {
            return $blog->category?->name ?: 'GENERAL NEWS';
        });
    }

    public function failed(Throwable $exception): void
    {
        $this->campaign->update([
            'status' => 'failed',
        ]);
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('status')->default('active')->index(); // active, unsubscribed, bounced, inactive
            $table->boolean('notify_new_post')->default(true);
            $table->boolean('notify_weekly_digest')->default(true);
            $table->string('unsubscribe_token', 64)->unique()->index();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamp('last_email_sent_at')->nullable();
            $table->string('last_delivery_status')->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('created_at');
        });

        Schema::create('newsletter_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('campaign_type')->index(); // new_post, weekly_digest, test
            $table->string('campaign_key')->nullable()->unique(); // post-123, weekly-2026-33
            $table->foreignId('post_id')->nullable()->constrained('blogs')->nullOnDelete();
            $table->string('status')->default('queued')->index(); // draft, queued, processing, completed, failed
            $table->integer('total_subscribers')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });

        Schema::create('newsletter_email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained('newsletter_campaigns')->cascadeOnDelete();
            $table->foreignId('subscriber_id')->constrained('newsletter_subscribers')->cascadeOnDelete();
            $table->foreignId('post_id')->nullable()->constrained('blogs')->nullOnDelete();
            $table->string('campaign_type')->index(); // new_post, weekly_digest, test
            $table->string('status')->default('queued')->index(); // queued, sent, failed
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->string('message_id')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });

        Schema::create('newsletter_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_email_logs');
        Schema::dropIfExists('newsletter_campaigns');
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('newsletter_settings');
    }
};

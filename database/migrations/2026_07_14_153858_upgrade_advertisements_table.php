<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->string('device')->default('all')->index();
            $table->string('type')->default('google_adsense')->index();
            $table->integer('priority')->default(0)->index();
            $table->longText('code_desktop')->nullable();
            $table->longText('code_tablet')->nullable();
            $table->longText('code_mobile')->nullable();
            $table->string('image')->nullable();
            $table->string('image_tablet')->nullable();
            $table->string('image_mobile')->nullable();
            $table->string('target_url')->nullable();
            $table->dateTime('start_date')->nullable()->index();
            $table->dateTime('end_date')->nullable()->index();
            $table->integer('max_views')->nullable();
            $table->integer('max_clicks')->nullable();
            $table->integer('current_views')->default(0)->index();
            $table->integer('current_clicks')->default(0)->index();
            $table->dateTime('last_viewed_at')->nullable();
            $table->dateTime('last_clicked_at')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->json('target_pages')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'device',
                'type',
                'priority',
                'code_desktop',
                'code_tablet',
                'code_mobile',
                'image',
                'image_tablet',
                'image_mobile',
                'target_url',
                'start_date',
                'end_date',
                'max_views',
                'max_clicks',
                'current_views',
                'current_clicks',
                'last_viewed_at',
                'last_clicked_at',
                'width',
                'height',
                'target_pages',
                'created_by',
            ]);
        });
    }
};

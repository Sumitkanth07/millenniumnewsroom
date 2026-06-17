<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('id')->constrained('categories')->nullOnDelete();
            }
            if (! Schema::hasColumn('categories', 'description')) {
                $table->text('description')->nullable()->after('image');
            }
        });

        Schema::table('blogs', function (Blueprint $table) {
            if (! Schema::hasColumn('blogs', 'featured_image_alt')) {
                $table->string('featured_image_alt')->nullable();
            }
            if (! Schema::hasColumn('blogs', 'featured_image_title')) {
                $table->string('featured_image_title')->nullable();
            }
            if (! Schema::hasColumn('blogs', 'featured_image_caption')) {
                $table->string('featured_image_caption')->nullable();
            }
            if (! Schema::hasColumn('blogs', 'featured_image_description')) {
                $table->text('featured_image_description')->nullable();
            }
            if (! Schema::hasColumn('blogs', 'robots_meta')) {
                $table->string('robots_meta')->nullable();
            }
            if (! Schema::hasColumn('blogs', 'reading_time')) {
                $table->unsignedInteger('reading_time')->default(1);
            }
            if (! Schema::hasColumn('blogs', 'is_trending')) {
                $table->boolean('is_trending')->default(false);
            }
        });

        Schema::table('footer_settings', function (Blueprint $table) {
            foreach (['footer_menus', 'category_links', 'social_links', 'sitemap_links'] as $column) {
                if (! Schema::hasColumn('footer_settings', $column)) {
                    $table->json($column)->nullable();
                }
            }
        });
    }
};

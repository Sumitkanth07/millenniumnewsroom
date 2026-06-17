<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('image')->nullable();
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('authors')) {
            Schema::create('authors', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('email')->nullable();
                $table->string('image')->nullable();
                $table->text('bio')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('tags')) {
            Schema::create('tags', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('blogs')) {
            Schema::table('blogs', function (Blueprint $table) {
                if (! Schema::hasColumn('blogs', 'category_id')) {
                    $table->foreignId('category_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
                }
                if (! Schema::hasColumn('blogs', 'author_id')) {
                    $table->foreignId('author_id')->nullable()->after('category_id')->constrained()->nullOnDelete();
                }
                if (! Schema::hasColumn('blogs', 'featured_image')) {
                    $table->string('featured_image')->nullable()->after('image');
                }
                if (! Schema::hasColumn('blogs', 'gallery_images')) {
                    $table->json('gallery_images')->nullable()->after('featured_image');
                }
                if (! Schema::hasColumn('blogs', 'tags_cache')) {
                    $table->string('tags_cache')->nullable()->after('gallery_images');
                }
                if (! Schema::hasColumn('blogs', 'meta_keywords')) {
                    $table->string('meta_keywords')->nullable()->after('meta_description');
                }
                if (! Schema::hasColumn('blogs', 'canonical_url')) {
                    $table->string('canonical_url')->nullable()->after('meta_keywords');
                }
                if (! Schema::hasColumn('blogs', 'is_featured')) {
                    $table->boolean('is_featured')->default(false)->after('is_published');
                }
                if (! Schema::hasColumn('blogs', 'is_breaking')) {
                    $table->boolean('is_breaking')->default(false)->after('is_featured');
                }
                if (! Schema::hasColumn('blogs', 'status')) {
                    $table->string('status')->default('draft')->after('is_breaking');
                }
                if (! Schema::hasColumn('blogs', 'scheduled_at')) {
                    $table->timestamp('scheduled_at')->nullable()->after('status');
                }
                if (! Schema::hasColumn('blogs', 'views_count')) {
                    $table->unsignedBigInteger('views_count')->default(0)->after('scheduled_at');
                }
            });
        }

        if (! Schema::hasTable('blog_tag')) {
            Schema::create('blog_tag', function (Blueprint $table) {
                $table->id();
                $table->foreignId('blog_id')->constrained()->cascadeOnDelete();
                $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
                $table->unique(['blog_id', 'tag_id']);
            });
        }

        if (! Schema::hasTable('pages')) {
            Schema::create('pages', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('banner_image')->nullable();
                $table->longText('content')->nullable();
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->boolean('is_published')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('media_items')) {
            Schema::create('media_items', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('path');
                $table->string('folder')->nullable();
                $table->string('alt_text')->nullable();
                $table->string('mime_type')->nullable();
                $table->unsignedBigInteger('size')->default(0);
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('comments')) {
            Schema::create('comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('blog_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->string('email')->nullable();
                $table->text('body');
                $table->string('status')->default('pending');
                $table->timestamps();
            });
        }

        if (Schema::hasTable('navigation_items')) {
            Schema::table('navigation_items', function (Blueprint $table) {
                if (! Schema::hasColumn('navigation_items', 'parent_id')) {
                    $table->foreignId('parent_id')->nullable()->after('id')->constrained('navigation_items')->nullOnDelete();
                }
                if (! Schema::hasColumn('navigation_items', 'target')) {
                    $table->string('target')->default('_self')->after('url');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
        Schema::dropIfExists('media_items');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('blog_tag');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('categories');
    }
};

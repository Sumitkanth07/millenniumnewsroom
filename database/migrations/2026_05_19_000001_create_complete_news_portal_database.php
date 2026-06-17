<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('label')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('label')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('permission_role')) {
            Schema::create('permission_role', function (Blueprint $table) {
                $table->id();
                $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
                $table->foreignId('role_id')->constrained()->cascadeOnDelete();
                $table->unique(['permission_id', 'role_id']);
            });
        }

        if (! Schema::hasTable('role_user')) {
            Schema::create('role_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('role_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->unique(['role_id', 'user_id']);
            });
        }

        if (! Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
                $table->string('designation')->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('subcategories')) {
            Schema::create('subcategories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('image')->nullable();
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->unsignedInteger('order')->default(0)->index();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
                $table->index(['category_id', 'slug']);
            });
        }

        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                if (! Schema::hasColumn('categories', 'order')) {
                    $table->unsignedInteger('order')->default(0)->index();
                }
            });
        }

        if (Schema::hasTable('authors')) {
            Schema::table('authors', function (Blueprint $table) {
                if (! Schema::hasColumn('authors', 'designation')) {
                    $table->string('designation')->nullable();
                }
                if (! Schema::hasColumn('authors', 'social_links')) {
                    $table->json('social_links')->nullable();
                }
            });
        }

        if (! Schema::hasTable('posts')) {
            Schema::create('posts', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('short_description')->nullable();
                $table->longText('content');
                $table->string('featured_image')->nullable();
                $table->string('image_alt')->nullable();
                $table->json('gallery_images')->nullable();
                $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('subcategory_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('author_id')->nullable()->constrained()->nullOnDelete();
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->string('meta_keywords')->nullable();
                $table->string('canonical_url')->nullable();
                $table->string('schema_type')->default('NewsArticle');
                $table->unsignedBigInteger('views')->default(0)->index();
                $table->unsignedInteger('reading_time')->default(1);
                $table->boolean('featured')->default(false)->index();
                $table->boolean('breaking_news')->default(false)->index();
                $table->boolean('trending')->default(false)->index();
                $table->string('status')->default('draft')->index();
                $table->timestamp('published_at')->nullable()->index();
                $table->timestamps();
                $table->index(['status', 'published_at']);
                $table->index(['category_id', 'status']);
            });
        }

        if (! Schema::hasTable('post_tag')) {
            Schema::create('post_tag', function (Blueprint $table) {
                $table->id();
                $table->foreignId('post_id')->constrained()->cascadeOnDelete();
                $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
                $table->unique(['post_id', 'tag_id']);
            });
        }

        if (! Schema::hasTable('menus')) {
            Schema::create('menus', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('location')->unique();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('menu_items')) {
            Schema::create('menu_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
                $table->foreignId('parent_id')->nullable()->constrained('menu_items')->nullOnDelete();
                $table->string('label');
                $table->string('url');
                $table->string('target')->default('_self');
                $table->unsignedInteger('sort_order')->default(0)->index();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
                $table->index(['menu_id', 'sort_order']);
            });
        }

        if (! Schema::hasTable('media')) {
            Schema::create('media', function (Blueprint $table) {
                $table->id();
                $table->string('file_name');
                $table->string('file_path');
                $table->string('alt_text')->nullable();
                $table->string('caption')->nullable();
                $table->string('file_type')->nullable()->index();
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->index(['uploaded_by', 'file_type']);
            });
        }

        if (! Schema::hasTable('galleries')) {
            Schema::create('galleries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('post_id')->nullable()->constrained()->cascadeOnDelete();
                $table->foreignId('blog_id')->nullable()->constrained('blogs')->cascadeOnDelete();
                $table->foreignId('media_id')->nullable()->constrained('media')->nullOnDelete();
                $table->string('image_path')->nullable();
                $table->string('alt_text')->nullable();
                $table->string('caption')->nullable();
                $table->unsignedInteger('sort_order')->default(0)->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('newsletters')) {
            Schema::create('newsletters', function (Blueprint $table) {
                $table->id();
                $table->string('email')->unique();
                $table->string('name')->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamp('subscribed_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('seo_settings')) {
            Schema::create('seo_settings', function (Blueprint $table) {
                $table->id();
                $table->nullableMorphs('seoable');
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->string('meta_keywords')->nullable();
                $table->string('canonical_url')->nullable();
                $table->string('robots_meta')->default('index,follow');
                $table->string('og_title')->nullable();
                $table->text('og_description')->nullable();
                $table->string('og_image')->nullable();
                $table->string('schema_type')->nullable();
                $table->json('schema_data')->nullable();
                $table->boolean('include_in_sitemap')->default(true)->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('advertisements')) {
            Schema::create('advertisements', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('placement')->index();
                $table->longText('code')->nullable();
                $table->boolean('is_responsive')->default(true);
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('social_accounts')) {
            Schema::create('social_accounts', function (Blueprint $table) {
                $table->id();
                $table->string('platform')->index();
                $table->string('account_name');
                $table->text('api_token')->nullable();
                $table->json('platform_settings')->nullable();
                $table->boolean('auto_post')->default(false);
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('social_post_logs')) {
            Schema::create('social_post_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('social_account_id')->constrained()->cascadeOnDelete();
                $table->foreignId('post_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('blog_id')->nullable()->constrained('blogs')->nullOnDelete();
                $table->string('platform')->index();
                $table->string('status')->default('pending')->index();
                $table->text('response')->nullable();
                $table->timestamp('posted_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('post_views')) {
            Schema::create('post_views', function (Blueprint $table) {
                $table->id();
                $table->foreignId('post_id')->nullable()->constrained()->cascadeOnDelete();
                $table->foreignId('blog_id')->nullable()->constrained('blogs')->cascadeOnDelete();
                $table->string('ip_hash')->nullable()->index();
                $table->string('user_agent')->nullable();
                $table->timestamp('viewed_at')->useCurrent()->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('contact_messages')) {
            Schema::create('contact_messages', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email');
                $table->string('subject')->nullable();
                $table->text('message');
                $table->string('status')->default('new')->index();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        foreach ([
            'contact_messages', 'post_views', 'social_post_logs', 'social_accounts',
            'advertisements', 'seo_settings', 'newsletters', 'galleries', 'media',
            'menu_items', 'menus', 'post_tag', 'posts', 'subcategories', 'admins',
            'role_user', 'permission_role', 'permissions', 'roles',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};

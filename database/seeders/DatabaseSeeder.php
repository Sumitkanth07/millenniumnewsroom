<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\AdPlacement;
use App\Models\Admin;
use App\Models\Advertisement;
use App\Models\Blog;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\FooterSetting;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\NavigationItem;
use App\Models\Newsletter;
use App\Models\Page;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Role;
use App\Models\SeoSetting;
use App\Models\Setting;
use App\Models\SocialAccount;
use App\Models\Subcategory;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::where('email', 'admin@millenniumnewsroom.com')->delete();

        $admin = User::updateOrCreate(
            ['email' => 'Sumitkant7@gmail.com'],
            ['name' => 'MILLENNIUM NEWSROOM Admin', 'password' => Hash::make('Milleniums@2026#'), 'is_admin' => true]
        );
        Admin::updateOrCreate(['user_id' => $admin->id], ['designation' => 'Super Administrator', 'is_active' => true]);

        $adminRole = Role::updateOrCreate(['name' => 'super-admin'], ['label' => 'Super Admin']);
        foreach (['manage-posts', 'manage-media', 'manage-seo', 'manage-ads', 'manage-users'] as $permissionName) {
            $permission = Permission::updateOrCreate(['name' => $permissionName], ['label' => str($permissionName)->headline()]);
            $adminRole->permissions()->syncWithoutDetaching($permission->id);
        }
        $admin->roles()->syncWithoutDetaching($adminRole->id);

        foreach ([
            'site_name' => 'MILLENNIUM NEWSROOM',
            'site_title' => 'MILLENNIUM NEWSROOM | Professional News Portal',
            'tagline' => 'Business, markets and technology journalism',
            'primary_color' => '#1f1a12',
            'secondary_color' => '#c79a2b',
            'logo' => '',
            'meta_description' => 'MILLENNIUM NEWSROOM delivers fast, professional coverage of news, markets, companies, politics, technology, sports, lifestyle and opinion.',
            'robots_txt' => "User-agent: *\nAllow: /",
        ] as $key => $value) {
            Setting::setValue($key, $value);
        }

        foreach ([
            ['name' => 'Header Ad', 'key' => 'header_ad'],
            ['name' => 'Sidebar Ad', 'key' => 'sidebar_ad'],
            ['name' => 'In Content Ad', 'key' => 'in_content_ad'],
            ['name' => 'Footer Ad', 'key' => 'footer_ad'],
        ] as $ad) {
            AdPlacement::updateOrCreate(['key' => $ad['key']], [
                'name' => $ad['name'],
                'code' => null,
                'is_active' => true,
            ]);
        }

        FooterSetting::updateOrCreate(['id' => 1], [
            'company_name' => 'MILLENNIUM NEWSROOM',
            'email' => 'newsroom@millenniumnewsroom.com',
            'phone' => '+91 9876543210',
            'address' => 'New Delhi, India',
            'copyright_text' => '(c) '.date('Y').' MILLENNIUM NEWSROOM. All rights reserved.',
        ]);

        $categories = collect(['News', 'CXO View', 'Technology', 'Companies', 'Politics', 'Opinion', 'Sports', 'Lifestyle'])
            ->mapWithKeys(fn ($name, $index) => [$name => Category::updateOrCreate(
                ['slug' => str($name)->slug()->toString()],
                ['name' => $name, 'sort_order' => $index + 1, 'order' => $index + 1, 'is_active' => true, 'meta_title' => $name.' News | MILLENNIUM NEWSROOM', 'meta_description' => 'Latest '.$name.' coverage from MILLENNIUM NEWSROOM.']
            )]);

        $author = Author::updateOrCreate(
            ['slug' => 'editorial-desk'],
            ['name' => 'Editorial Desk', 'email' => 'newsroom@millenniumnewsroom.com', 'designation' => 'News Desk', 'social_links' => ['x' => 'https://x.com/', 'linkedin' => 'https://linkedin.com/'], 'bio' => 'The MILLENNIUM NEWSROOM editorial desk covers business, policy, technology and culture with context and speed.', 'is_active' => true]
        );

        $aiSubcategory = Subcategory::updateOrCreate(
            ['slug' => 'artificial-intelligence'],
            ['category_id' => $categories['Technology']->id, 'name' => 'Artificial Intelligence', 'order' => 1, 'is_active' => true]
        );

        $post = Blog::updateOrCreate(
            ['slug' => 'ai-is-transforming-the-future-of-digital-journalism'],
            [
                'user_id' => $admin->id,
                'category_id' => $categories['Technology']->id,
                'author_id' => $author->id,
                'title' => 'AI Is Transforming The Future Of Digital Journalism',
                'excerpt' => 'Artificial intelligence is reshaping reporting workflows, newsroom research, audience products and the economics of digital media.',
                'content' => '<h2>Newsrooms are changing fast</h2><p>Artificial intelligence is moving from experiment to daily workflow inside modern newsrooms. Editors are using AI tools to speed up research, summarize documents, monitor live events and package stories for different audience formats.</p><p>The strongest use cases keep human editorial judgment at the center. Reporters still verify facts, add context and make final publishing decisions, while AI helps reduce repetitive production work.</p><h2>What it means for readers</h2><p>Readers can expect faster updates, richer explainers and more personalized news experiences. The challenge for publishers is transparency, accuracy and clear accountability whenever automation is involved.</p>',
                'meta_title' => 'AI Is Transforming The Future Of Digital Journalism | MILLENNIUM NEWSROOM',
                'meta_description' => 'A professional analysis of how AI is changing digital journalism, newsroom workflows and reader experiences.',
                'meta_keywords' => 'AI journalism, digital media, newsroom technology',
                'is_published' => true,
                'is_featured' => true,
                'is_breaking' => true,
                'is_trending' => true,
                'status' => 'published',
                'published_at' => now(),
                'views_count' => 1250,
                'reading_time' => 2,
                'featured_image_alt' => 'Digital newsroom using artificial intelligence tools',
                'robots_meta' => 'index,follow',
            ]
        );

        foreach (['AI', 'Digital Journalism', 'Media'] as $tag) {
            $post->tags()->syncWithoutDetaching(Tag::firstOrCreate(['slug' => str($tag)->slug()->toString()], ['name' => $tag])->id);
        }
        $post->updateQuietly(['tags_cache' => 'AI, Digital Journalism, Media']);



        foreach ([
            ['slug' => 'markets-open-higher-as-investors-track-earnings', 'category' => 'Markets', 'title' => 'Markets Open Higher As Investors Track Earnings Momentum', 'excerpt' => 'Benchmark indices advanced in early trade as investors watched quarterly earnings, global cues and sector rotation.', 'views' => 860],
            ['slug' => 'startup-founders-focus-on-profitability-in-2026', 'category' => 'Companies', 'title' => 'Startup Founders Focus On Profitability In 2026', 'excerpt' => 'Founders are prioritizing cash discipline, sharper products and sustainable expansion after a reset in private funding.', 'views' => 720],
            ['slug' => 'new-data-rules-could-change-digital-advertising', 'category' => 'Technology', 'title' => 'New Data Rules Could Change Digital Advertising', 'excerpt' => 'Privacy regulation and consent frameworks are pushing digital businesses to rethink measurement and customer trust.', 'views' => 640],
            ['slug' => 'policy-watch-what-businesses-need-to-know-this-week', 'category' => 'Politics', 'title' => 'Policy Watch: What Businesses Need To Know This Week', 'excerpt' => 'A concise briefing on policy moves, parliamentary signals and regulatory updates affecting companies and investors.', 'views' => 510],
            ['slug' => 'opinion-why-trust-is-the-next-media-metric', 'category' => 'Opinion', 'title' => 'Opinion: Why Trust Is The Next Media Metric', 'excerpt' => 'Audience loyalty will depend less on speed alone and more on credibility, transparency and useful context.', 'views' => 940],
            ['slug' => 'lifestyle-brands-use-community-to-drive-growth', 'category' => 'Lifestyle', 'title' => 'Lifestyle Brands Use Community To Drive Growth', 'excerpt' => 'Modern consumer brands are building repeat engagement through memberships, creator partnerships and events.', 'views' => 430],
        ] as $sample) {
            $sampleBlog = Blog::updateOrCreate(['slug' => $sample['slug']], [
                'user_id' => $admin->id,
                'category_id' => $categories[$sample['category']]->id,
                'author_id' => $author->id,
                'title' => $sample['title'],
                'excerpt' => $sample['excerpt'],
                'content' => '<p>'.$sample['excerpt'].'</p><p>MILLENNIUM NEWSROOM explains the key context, stakeholder impact and next signals readers should watch. This sample story can be edited from the admin panel with images, SEO metadata, tags and scheduling.</p>',
                'meta_title' => $sample['title'].' | MILLENNIUM NEWSROOM',
                'meta_description' => $sample['excerpt'],
                'meta_keywords' => strtolower($sample['category']).', news, MILLENNIUM NEWSROOM',
                'robots_meta' => 'index,follow',
                'is_published' => true,
                'is_featured' => in_array($sample['category'], ['Markets', 'Opinion']),
                'is_breaking' => $sample['category'] === 'Markets',
                'is_trending' => $sample['views'] > 800,
                'status' => 'published',
                'published_at' => now()->subHours(rand(2, 48)),
                'views_count' => $sample['views'],
                'reading_time' => 2,
                'featured_image_alt' => $sample['title'],
            ]);


        }

        foreach ([
            ['title' => 'About Us', 'slug' => 'about-us'],
            ['title' => 'Privacy Policy', 'slug' => 'privacy-policy'],
            ['title' => 'Terms', 'slug' => 'terms'],
            ['title' => 'Contact', 'slug' => 'contact'],
        ] as $page) {
            Page::updateOrCreate(['slug' => $page['slug']], [
                'title' => $page['title'],
                'content' => '<p>MILLENNIUM NEWSROOM is a professional digital news portal focused on clear, timely and useful journalism.</p>',
                'is_published' => true,
                'meta_title' => $page['title'].' | MILLENNIUM NEWSROOM',
                'meta_description' => $page['title'].' for MILLENNIUM NEWSROOM.',
            ]);
        }

        SeoSetting::updateOrCreate(
            ['seoable_type' => Page::class, 'seoable_id' => Page::where('slug', 'about-us')->value('id')],
            ['meta_title' => 'About Us | MILLENNIUM NEWSROOM', 'meta_description' => 'About MILLENNIUM NEWSROOM digital newsroom.', 'robots_meta' => 'index,follow', 'schema_type' => 'WebPage']
        );

        foreach ([
            ['key' => 'hero', 'title' => 'Lead Story', 'subtitle' => 'Top of homepage', 'content' => 'Controls the premium lead story area.', 'sort_order' => 1],
            ['key' => 'breaking_news', 'title' => 'Breaking News Ticker', 'subtitle' => 'Live updates', 'content' => 'Highlights stories marked as breaking.', 'sort_order' => 2],
            ['key' => 'trending_posts', 'title' => 'Trending Posts', 'subtitle' => 'Most read', 'content' => 'Uses views and forced trending flag.', 'sort_order' => 3],
            ['key' => 'featured_categories', 'title' => 'Featured Categories', 'subtitle' => 'Section blocks', 'content' => 'Homepage category sections are created dynamically.', 'sort_order' => 4],
            ['key' => 'latest_news', 'title' => 'Latest News', 'subtitle' => 'Fresh updates', 'content' => 'Latest published posts feed.', 'sort_order' => 5],
            ['key' => 'sidebar_widgets', 'title' => 'Sidebar Widgets', 'subtitle' => 'Newsletter and popular news', 'content' => 'Controls secondary modules.', 'sort_order' => 6],
            ['key' => 'advertisements', 'title' => 'Advertisement Blocks', 'subtitle' => 'Ad placements', 'content' => 'Reserved blocks for ad tags or static placements.', 'sort_order' => 7],
        ] as $section) {
            \App\Models\HomepageSection::updateOrCreate(['key' => $section['key']], $section + ['is_active' => true]);
        }

        NavigationItem::query()->delete();
        foreach ($categories as $category) {
            NavigationItem::create([
                'label' => $category->name,
                'url' => '/category/'.$category->slug,
                'sort_order' => $category->sort_order,
                'is_active' => true,
            ]);
        }

        $mainMenu = Menu::updateOrCreate(['location' => 'main'], ['name' => 'Main Navigation', 'is_active' => true]);
        foreach ($categories as $category) {
            MenuItem::updateOrCreate(
                ['menu_id' => $mainMenu->id, 'label' => $category->name],
                ['url' => '/category/'.$category->slug, 'sort_order' => $category->sort_order, 'is_active' => true]
            );
        }

        foreach ([
            ['name' => 'Header Advertisement', 'placement' => 'header_ad'],
            ['name' => 'Sidebar Advertisement', 'placement' => 'sidebar_ad'],
            ['name' => 'In Content Advertisement', 'placement' => 'in_content_ad'],
            ['name' => 'Footer Advertisement', 'placement' => 'footer_ad'],
        ] as $ad) {
            Advertisement::updateOrCreate(['placement' => $ad['placement']], $ad + ['code' => null, 'is_responsive' => true, 'is_active' => true]);
        }

        Newsletter::updateOrCreate(['email' => 'reader@example.com'], ['name' => 'Demo Reader', 'is_active' => true, 'subscribed_at' => now()]);
        SocialAccount::updateOrCreate(['platform' => 'x', 'account_name' => 'MILLENNIUM NEWSROOM'], ['platform_settings' => ['auto_hashtags' => true], 'auto_post' => false, 'is_active' => true]);
        ContactMessage::firstOrCreate(['email' => 'reader@example.com', 'subject' => 'Demo enquiry'], ['name' => 'Demo Reader', 'message' => 'This is a sample contact message.', 'status' => 'new']);
    }
}

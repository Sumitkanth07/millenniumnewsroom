<?php

namespace Database\Seeders;

use App\Models\AdPlacement;
use App\Models\Advertisement;
use App\Models\Author;
use App\Models\Blog;
use App\Models\Category;
use App\Models\FooterSetting;
use App\Models\HomepageSection;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\NavigationItem;
use App\Models\Post;
use App\Models\SeoSetting;
use App\Models\Setting;
use App\Models\SocialAccount;
use App\Models\Subcategory;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfessionalContentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'Sumitkant7@gmail.com')->first() ?: User::first();

        foreach ([
            'site_name' => 'MILLENNIUM NEWSROOM',
            'site_title' => 'MILLENNIUM NEWSROOM | Business, Markets, Technology & Policy News',
            'tagline' => 'Premium digital newsroom for business, markets, technology and public affairs',
            'meta_description' => 'MILLENNIUM NEWSROOM publishes premium business news, market updates, technology analysis, policy coverage, startup stories and opinion for modern readers.',
            'robots_txt' => "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /login",
            'analytics_code' => '',
            'adsense_code' => '',
            'social_links' => json_encode([
                'x' => 'https://x.com/millenniumnewsroom',
                'linkedin' => 'https://linkedin.com/company/millenniumnewsroom',
                'youtube' => 'https://youtube.com/@millenniumnewsroom',
                'instagram' => 'https://instagram.com/millenniumnewsroom',
            ]),
        ] as $key => $value) {
            Setting::setValue($key, $value);
        }

        $categories = collect(['News', 'Markets', 'Technology', 'Companies', 'Politics', 'Opinion', 'Sports', 'Lifestyle'])
            ->mapWithKeys(function (string $name, int $index) {
                return [$name => Category::updateOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        'name' => $name,
                        'sort_order' => $index + 1,
                        'order' => $index + 1,
                        'image' => $this->seedImage(Str::slug($name), $name),
                        'meta_title' => $name.' News | MILLENNIUM NEWSROOM',
                        'meta_description' => 'Latest '.$name.' news, updates and analysis from MILLENNIUM NEWSROOM.',
                        'is_active' => true,
                    ]
                )];
            });

        $subcategories = collect([
            'Economy' => 'News',
            'Stock Market' => 'Markets',
            'Artificial Intelligence' => 'Technology',
            'Startups' => 'Companies',
            'Policy' => 'Politics',
            'Editorial' => 'Opinion',
            'Cricket' => 'Sports',
            'Personal Finance' => 'Lifestyle',
        ])->mapWithKeys(fn (string $categoryName, string $name) => [$name => Subcategory::updateOrCreate(
            ['slug' => Str::slug($name)],
            ['category_id' => $categories[$categoryName]->id, 'name' => $name, 'image' => $this->seedImage(Str::slug($name), $name), 'order' => 1, 'is_active' => true]
        )]);

        $authors = collect([
            ['name' => 'Aarav Mehta', 'designation' => 'Markets Editor'],
            ['name' => 'Nisha Rao', 'designation' => 'Technology Correspondent'],
            ['name' => 'Kabir Sethi', 'designation' => 'Policy Analyst'],
            ['name' => 'Editorial Desk', 'designation' => 'News Desk'],
        ])->mapWithKeys(fn ($author) => [$author['name'] => Author::updateOrCreate(
            ['slug' => Str::slug($author['name'])],
            [
                'name' => $author['name'],
                'email' => Str::slug($author['name'], '.').'@millenniumnewsroom.com',
                'designation' => $author['designation'],
                'image' => $this->seedImage(Str::slug($author['name']), $author['name']),
                'bio' => $author['name'].' covers high-impact developments for MILLENNIUM NEWSROOM with a focus on context, clarity and reader value.',
                'social_links' => ['x' => 'https://x.com/'.Str::slug($author['name'], ''), 'linkedin' => 'https://linkedin.com/'],
                'is_active' => true,
            ]
        )]);

        $stories = [
            ['Markets', 'Stock Market', 'Sensex, Nifty Extend Gains As Banking Stocks Lead Rally', 'Benchmark indices closed higher as investors tracked earnings momentum and easing global bond yields.', ['markets','stocks','nifty'], 18420, 2],
            ['Technology', 'Artificial Intelligence', 'AI Tools Are Rewriting The Economics Of Digital Newsrooms', 'Publishers are using automation to accelerate research, packaging and reader personalization while retaining editorial control.', ['AI','media','technology'], 16200, 3],
            ['Companies', 'Startups', 'Startups Shift From Growth At All Costs To Profitable Scale', 'Founders are focusing on unit economics, practical expansion and customer retention as funding becomes more selective.', ['startups','funding','companies'], 12880, 3],
            ['Politics', 'Policy', 'New Data Protection Rules Put Compliance Back On Boardroom Agenda', 'Companies are reviewing consent, retention and governance practices ahead of stricter enforcement timelines.', ['policy','privacy','compliance'], 9400, 4],
            ['Opinion', 'Editorial', 'Opinion: Trust Will Decide The Next Era Of Digital Media', 'The most durable media brands will be those that combine speed with transparency, expertise and accountability.', ['opinion','media','trust'], 15300, 3],
            ['Lifestyle', 'Personal Finance', 'How Young Investors Are Rebalancing Portfolios In 2026', 'A new generation of investors is combining SIPs, ETFs, gold and emergency funds with a sharper eye on risk.', ['finance','investing','lifestyle'], 11750, 4],
            ['Sports', 'Cricket', 'Cricket Sponsorships Enter A Data-Driven Growth Phase', 'Brands are using fan analytics and digital activations to measure the real value of sports sponsorship.', ['sports','cricket','brands'], 7600, 2],
            ['News', 'Economy', 'India Growth Outlook Brightens As Services Demand Remains Strong', 'Economists point to steady services activity, resilient consumption and improving capital expenditure trends.', ['economy','growth','india'], 14200, 3],
            ['Markets', 'Stock Market', 'Rupee Holds Steady As Traders Watch Fed Signals', 'Currency markets remained rangebound as traders balanced dollar strength with domestic inflows.', ['rupee','forex','markets'], 6300, 2],
            ['Technology', 'Artificial Intelligence', 'Cloud Spending Rises As Enterprises Accelerate AI Pilots', 'Large companies are increasing cloud budgets to support data platforms, copilots and automation tools.', ['cloud','AI','enterprise'], 10100, 3],
            ['Companies', 'Startups', 'Consumer Brands Turn To Community-Led Commerce', 'D2C businesses are building loyalty through memberships, live events and creator partnerships.', ['D2C','commerce','brands'], 8500, 3],
            ['Politics', 'Policy', 'Policy Watch: Key Regulatory Updates Businesses Should Track', 'A concise briefing on tax, trade, data and market rules affecting companies this month.', ['policy','regulation','business'], 7200, 3],
            ['Opinion', 'Editorial', 'Editorial: Why India Needs More Patient Capital', 'Long-term investment can help deepen innovation ecosystems and reduce short-term pressure on founders.', ['editorial','capital','innovation'], 11100, 4],
            ['Lifestyle', 'Personal Finance', 'Premium Travel Demand Holds Strong Despite Higher Airfares', 'Consumers continue to prioritize experiences, pushing airlines and hotels to expand premium offerings.', ['travel','lifestyle','consumer'], 6900, 2],
            ['Sports', 'Cricket', 'Sports Tech Platforms Chase Growth Beyond Live Scores', 'Apps are expanding into fantasy, commerce, creator content and personalized fan experiences.', ['sports tech','apps','growth'], 5800, 2],
            ['News', 'Economy', 'Urban Jobs Market Shows Resilience In Latest Hiring Data', 'Hiring in services, technology and retail roles has remained steady despite cautious corporate planning.', ['jobs','economy','hiring'], 9200, 3],
            ['Markets', 'Stock Market', 'Gold Prices Stay Firm As Investors Hedge Volatility', 'Safe-haven buying and central bank demand continue to support gold prices in uncertain markets.', ['gold','commodities','markets'], 8700, 2],
            ['Technology', 'Artificial Intelligence', 'Cybersecurity Teams Prepare For AI-Powered Threats', 'Security leaders are investing in automation, identity controls and employee training to reduce risk.', ['cybersecurity','AI','risk'], 9900, 3],
        ];

        foreach ($stories as $index => [$categoryName, $subcategoryName, $title, $excerpt, $tags, $views, $readingTime]) {
            $slug = Str::slug($title);
            $category = $categories[$categoryName];
            $subcategory = $subcategories[$subcategoryName];
            $author = $authors->values()[$index % $authors->count()];
            $image = $this->seedImage($slug, $categoryName);
            $publishedAt = now()->subHours($index + 2);
            $content = '<p>'.$excerpt.'</p><p>This MILLENNIUM NEWSROOM report explains the context, market impact and next signals readers should watch. The story is built as demo content and can be managed fully from the admin panel.</p><h2>What readers should watch</h2><p>Editors can update this article with richer reporting, image galleries, SEO metadata, social distribution and related links.</p>';

            $blog = Blog::updateOrCreate(['slug' => $slug], [
                'user_id' => $admin?->id,
                'category_id' => $category->id,
                'author_id' => $author->id,
                'title' => $title,
                'excerpt' => $excerpt,
                'content' => $content,
                'image' => $image,
                'featured_image' => $image,
                'featured_image_alt' => $title,
                'meta_title' => $title.' | MILLENNIUM NEWSROOM',
                'meta_description' => $excerpt,
                'meta_keywords' => implode(', ', $tags),
                'robots_meta' => 'index,follow',
                'is_published' => true,
                'is_featured' => $index < 6,
                'is_breaking' => $index < 2,
                'is_trending' => $views >= 10000,
                'status' => 'published',
                'published_at' => $publishedAt,
                'views_count' => $views,
                'reading_time' => $readingTime,
            ]);

            $tagIds = collect($tags)->map(fn ($tag) => Tag::firstOrCreate(['slug' => Str::slug($tag)], ['name' => $tag])->id);
            $blog->tags()->sync($tagIds);
            $blog->updateQuietly(['tags_cache' => implode(', ', $tags)]);

            SeoSetting::updateOrCreate(
                ['seoable_type' => Blog::class, 'seoable_id' => $blog->id],
                ['meta_title' => $title.' | MILLENNIUM NEWSROOM', 'meta_description' => $excerpt, 'meta_keywords' => implode(', ', $tags), 'canonical_url' => url('/blog/'.$slug), 'robots_meta' => 'index,follow', 'og_title' => $title, 'og_description' => $excerpt, 'og_image' => $image, 'schema_type' => 'NewsArticle', 'include_in_sitemap' => true]
            );
        }

        foreach ([
            ['key' => 'hero', 'title' => 'Top Stories Driving The Day', 'subtitle' => 'Hero Slider', 'content' => 'Featured posts power the homepage hero slider.', 'sort_order' => 1],
            ['key' => 'trending_posts', 'title' => 'Trending Now', 'subtitle' => 'Most Watched', 'content' => 'Stories are ranked by views and trending flags.', 'sort_order' => 2],
            ['key' => 'latest_news', 'title' => 'Fresh News Feed', 'subtitle' => 'Latest Updates', 'content' => 'Latest published stories appear automatically.', 'sort_order' => 3],
            ['key' => 'editor_picks', 'title' => 'Editor Picks', 'subtitle' => 'Recommended Reads', 'content' => 'Featured stories selected for premium placement.', 'sort_order' => 4],
            ['key' => 'newsletter', 'title' => 'Morning Briefing', 'subtitle' => 'Newsletter', 'content' => 'Invite readers to subscribe to daily updates.', 'sort_order' => 5],
        ] as $section) {
            HomepageSection::updateOrCreate(['key' => $section['key']], $section + ['is_active' => true]);
        }

        $mainMenu = Menu::updateOrCreate(['location' => 'main'], ['name' => 'Main Navigation', 'is_active' => true]);
        foreach ($categories as $category) {
            MenuItem::updateOrCreate(['menu_id' => $mainMenu->id, 'label' => $category->name], ['url' => '/category/'.$category->slug, 'sort_order' => $category->sort_order, 'is_active' => true]);
            NavigationItem::updateOrCreate(['label' => $category->name], ['url' => '/category/'.$category->slug, 'sort_order' => $category->sort_order, 'is_active' => true]);
        }

        FooterSetting::updateOrCreate(['id' => 1], [
            'company_name' => 'MILLENNIUM NEWSROOM',
            'email' => 'newsroom@millenniumnewsroom.com',
            'phone' => '+91 9876543210',
            'address' => 'New Delhi, India',
            'copyright_text' => '(c) '.date('Y').' MILLENNIUM NEWSROOM. All rights reserved.',
            'footer_menus' => ['About Us|/page/about-us', 'Privacy Policy|/page/privacy-policy', 'Terms|/page/terms'],
            'category_links' => $categories->map(fn ($category) => $category->name.'|/category/'.$category->slug)->values()->all(),
            'social_links' => ['X|https://x.com/millenniumnewsroom', 'LinkedIn|https://linkedin.com/company/millenniumnewsroom', 'YouTube|https://youtube.com/@millenniumnewsroom'],
            'sitemap_links' => ['HTML Sitemap|/sitemap', 'XML Sitemap|/sitemap.xml', 'News Sitemap|/news-sitemap.xml'],
        ]);

        foreach ([
            ['name' => 'Header Ad', 'key' => 'header_ad', 'placement' => 'header_ad'],
            ['name' => 'Sidebar Ad', 'key' => 'sidebar_ad', 'placement' => 'sidebar_ad'],
            ['name' => 'In Content Ad', 'key' => 'in_content_ad', 'placement' => 'in_content_ad'],
            ['name' => 'Footer Ad', 'key' => 'footer_ad', 'placement' => 'footer_ad'],
        ] as $ad) {
            AdPlacement::updateOrCreate(['key' => $ad['key']], ['name' => $ad['name'], 'code' => null, 'is_active' => true]);
            Advertisement::updateOrCreate(['placement' => $ad['placement']], ['name' => $ad['name'], 'code' => null, 'is_responsive' => true, 'is_active' => true]);
        }

        SocialAccount::updateOrCreate(['platform' => 'x', 'account_name' => 'MILLENNIUM NEWSROOM'], ['platform_settings' => ['auto_hashtags' => true, 'default_hashtags' => ['news', 'markets']], 'auto_post' => false, 'is_active' => true]);

        cache()->forget('frontend.home.payload');
        cache()->forget('admin.dashboard.payload');
    }

    private function seedImage(string $slug, string $label): string
    {
        $path = 'uploads/seed/'.$slug.'.svg';
        if (! Storage::disk('public')->exists($path)) {
            $safe = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
            Storage::disk('public')->put($path, <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="760" viewBox="0 0 1200 760">
  <defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#15100a"/><stop offset=".55" stop-color="#6f5018"/><stop offset="1" stop-color="#c79a2b"/></linearGradient></defs>
  <rect width="1200" height="760" fill="url(#g)"/>
  <circle cx="990" cy="120" r="220" fill="#fff" opacity=".08"/>
  <circle cx="160" cy="690" r="260" fill="#000" opacity=".18"/>
  <text x="76" y="116" fill="#f4d26b" font-family="Arial, sans-serif" font-size="34" font-weight="700">MILLENNIUM NEWSROOM</text>
  <text x="76" y="420" fill="#fff8e7" font-family="Georgia, serif" font-size="72" font-weight="700">{$safe}</text>
</svg>
SVG);
        }

        return $path;
    }
}

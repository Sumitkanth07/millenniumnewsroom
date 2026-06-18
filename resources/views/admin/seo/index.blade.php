@extends('admin.layout')

@section('content')
<div class="admin-seo-manager">
    <div class="admin-header" style="margin-bottom: 24px;">
        <span class="kicker">MILLENNIUM NEWSROOM</span>
        <h2>SEO Manager</h2>
        <p>Manage site-wide meta headers, custom JSON-LD schemas, sitemaps, robots.txt directives, and redirects.</p>
    </div>

    <!-- Tabs Navigation -->
    <div class="tabs-nav" style="display: flex; gap: 8px; border-bottom: 2px solid rgba(199,154,43,0.2); margin-bottom: 24px; padding-bottom: 8px;">
        <a href="{{ route('admin.seo.index', ['tab' => 'inventory']) }}" class="btn @if($tab === 'inventory') primary @else secondary @endif" style="text-decoration: none;">URL Inventory</a>
        <a href="{{ route('admin.seo.index', ['tab' => 'audit']) }}" class="btn @if($tab === 'audit') primary @else secondary @endif" style="text-decoration: none;">SEO Audit</a>
        <a href="{{ route('admin.seo.index', ['tab' => 'sitemap']) }}" class="btn @if($tab === 'sitemap') primary @else secondary @endif" style="text-decoration: none;">Sitemaps</a>
        <a href="{{ route('admin.seo.index', ['tab' => 'robots']) }}" class="btn @if($tab === 'robots') primary @else secondary @endif" style="text-decoration: none;">Robots & AI</a>
        <a href="{{ route('admin.seo.index', ['tab' => 'redirects']) }}" class="btn @if($tab === 'redirects') primary @else secondary @endif" style="text-decoration: none;">301 Redirects</a>
        <a href="{{ route('admin.seo.index', ['tab' => 'monitoring']) }}" class="btn @if($tab === 'monitoring') primary @else secondary @endif" style="text-decoration: none;">404 & Broken Links</a>
    </div>

    <!-- Tab Contents -->
    <div class="tab-content">
        @if($tab === 'inventory')
            <!-- URL Inventory Tab -->
            <div class="panel">
                <div class="panel-header" style="margin-bottom: 16px;">
                    <h3>Website URL Inventory</h3>
                    <p style="font-size: 14px; margin-top: 4px;">Dynamic registry of all crawlable paths on millenniumnewsroom.com. New posts, pages, and categories appear here automatically.</p>
                </div>
                <table class="admin-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 2px solid rgba(199,154,43,0.2); padding: 8px;">
                            <th style="padding: 12px 8px;">URL / Title</th>
                            <th style="padding: 12px 8px;">Type</th>
                            <th style="padding: 12px 8px;">SEO Settings</th>
                            <th style="padding: 12px 8px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventory as $item)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td style="padding: 12px 8px;">
                                    <strong>{{ $item['title'] }}</strong><br>
                                    <small><a href="{{ $item['url'] }}" target="_blank" style="color: #c79a2b;">{{ $item['path'] }}</a></small>
                                </td>
                                <td style="padding: 12px 8px;">
                                    <span style="font-size: 12px; background: rgba(199,154,43,0.15); color: #c79a2b; padding: 4px 8px; border-radius: 4px; font-weight: bold; text-transform: uppercase;">
                                        {{ $item['type'] }}
                                    </span>
                                </td>
                                <td style="padding: 12px 8px;">
                                    @if($item['seo'] && ($item['seo']->meta_title || $item['seo']->meta_description))
                                        <span style="color: #4cd137;">✔ Customized</span>
                                    @else
                                        <span style="opacity: 0.5;">Inherited Defaults</span>
                                    @endif
                                </td>
                                <td style="padding: 12px 8px; text-align: right;">
                                    <a href="{{ route('admin.seo.edit', ['type' => $item['type'], 'id' => $item['id'], 'path' => $item['path']]) }}" class="btn primary small" style="text-decoration: none;">Edit SEO</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if($tab === 'audit')
            <!-- SEO Audit Tab -->
            <div class="panel">
                <div class="panel-header" style="margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(199,154,43,0.1); padding-bottom: 16px;">
                    <div>
                        <h3>SEO Audit & Health Dashboard</h3>
                        <p style="font-size: 14px; margin-top: 4px; opacity: 0.85;">Identifies missing metadata, missing schemas, missing canonials, or missing alt attributes that harm Google rankings.</p>
                    </div>
                    <div style="text-align: center; background: rgba(199,154,43,0.1); border: 2px solid #c79a2b; border-radius: 50%; width: 75px; height: 75px; display: flex; flex-direction: column; justify-content: center; align-items: center; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                        <span style="font-size: 22px; font-weight: bold; color: #c79a2b; font-family: system-ui;">{{ $seoScore }}%</span>
                        <span style="font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.7; font-weight: bold; margin-top: 2px;">SEO Score</span>
                    </div>
                </div>
                
                @if(empty($audit))
                    <div style="text-align: center; padding: 40px; background: rgba(76, 209, 55, 0.1); border-radius: 8px; border: 1px solid #4cd137;">
                        <h4 style="color: #4cd137; font-size: 20px; margin-bottom: 8px;">Perfect Score! 🌟</h4>
                        <p>No major SEO errors or missing metadata packages detected across the URL inventory.</p>
                    </div>
                @else
                    <table class="admin-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 2px solid rgba(199,154,43,0.2); padding: 8px;">
                                <th style="padding: 12px 8px;">Page</th>
                                <th style="padding: 12px 8px;">Detected Health Issues</th>
                                <th style="padding: 12px 8px; text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($audit as $item)
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                    <td style="padding: 12px 8px;">
                                        <strong>{{ $item['title'] }}</strong><br>
                                        <small style="opacity: 0.6;">{{ $item['path'] }}</small>
                                    </td>
                                    <td style="padding: 12px 8px;">
                                        @foreach($item['issues'] as $issue)
                                            <span style="font-size: 11px; background: rgba(235, 77, 75, 0.15); color: #eb4d4b; padding: 3px 6px; border-radius: 4px; font-weight: bold; margin-right: 4px; display: inline-block; margin-bottom: 4px;">
                                                {{ $issue }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td style="padding: 12px 8px; text-align: right;">
                                        @if($item['type'] === 'Blog' && in_array('Missing Image Alt Tag', $item['issues']))
                                            <a href="{{ route('admin.blogs.edit', $item['id']) }}" class="btn secondary small" style="text-decoration: none; margin-right: 6px;">Fix Alt</a>
                                        @endif
                                        <a href="{{ route('admin.seo.edit', ['type' => $item['type'], 'id' => $item['id'], 'path' => $item['path']]) }}" class="btn primary small" style="text-decoration: none;">Quick Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @endif

        @if($tab === 'sitemap')
            <!-- Sitemaps Tab -->
            <div class="panel">
                <div class="panel-header" style="margin-bottom: 24px;">
                    <h3>Sitemap Control & Verification</h3>
                    <p style="font-size: 14px; margin-top: 4px;">Millennium Newsroom generates standard XML, News XML, and TXT sitemaps dynamically on request to comply with Google News rules.</p>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 30px;">
                    <div style="background: rgba(255, 255, 255, 0.03); padding: 20px; border-radius: 8px; border: 1px solid rgba(199,154,43,0.15);">
                        <h4 style="color: #c79a2b; margin-bottom: 12px; font-family: Georgia, serif;">Active Sitemap Endpoints</h4>
                        <ul style="list-style: none; padding: 0; margin: 0; display: grid; gap: 10px;">
                            <li>
                                <strong>XML Sitemap:</strong><br>
                                <a href="{{ route('sitemap') }}" target="_blank" style="color: #efe5d1;">{{ route('sitemap') }}</a>
                            </li>
                            <li>
                                <strong>Google News Sitemap:</strong><br>
                                <a href="{{ route('news-sitemap') }}" target="_blank" style="color: #efe5d1;">{{ route('news-sitemap') }}</a>
                            </li>
                            <li>
                                <strong>Plain Text Sitemap:</strong><br>
                                <a href="{{ route('sitemap.txt') }}" target="_blank" style="color: #efe5d1;">{{ route('sitemap.txt') }}</a>
                            </li>
                        </ul>
                    </div>

                    <div style="background: rgba(255, 255, 255, 0.03); padding: 20px; border-radius: 8px; border: 1px solid rgba(199,154,43,0.15); display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <h4 style="color: #c79a2b; margin-bottom: 8px; font-family: Georgia, serif;">Sitemap Actions</h4>
                            <p style="font-size: 13px; line-height: 1.4;">Since sitemaps are loaded dynamically, they never expire. Clear caches to instantly reflect newly configured URLs or meta data.</p>
                        </div>
                        <div style="display: flex; gap: 10px; margin-top: 16px;">
                            <form action="{{ route('admin.seo.clear-cache') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn primary">Clear Payload Cache</button>
                            </form>
                            <a href="https://www.xml-sitemaps.com/validate-xml-sitemap.html" target="_blank" class="btn secondary" style="text-decoration: none; line-height: 2.4;">Validate Sitemap</a>
                        </div>
                    </div>
                </div>

                <div class="panel-header" style="margin-bottom: 12px;">
                    <h4>Indexed URLs Registry ({{ count($sitemapUrls) }} total)</h4>
                </div>
                <div style="max-height: 250px; overflow-y: auto; background: rgba(0,0,0,0.2); padding: 12px; border-radius: 6px; font-family: monospace; font-size: 13px;">
                    @foreach($sitemapUrls as $url)
                        <div style="padding: 4px 0; border-bottom: 1px solid rgba(255,255,255,0.02);">{{ $url }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($tab === 'robots')
            <!-- Robots & AI Tab -->
            <div class="panel">
                <form action="{{ route('admin.seo.update-robots') }}" method="POST">
                    @csrf
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                        <div>
                            <div class="panel-header" style="margin-bottom: 12px;">
                                <h3>robots.txt Configuration</h3>
                                <p style="font-size: 13px;">Manage search engine visibility directives. Dynamic sitemaps declarations are appended automatically.</p>
                            </div>
                            <textarea name="robots_txt" style="width: 100%; height: 260px; font-family: monospace; font-size: 14px; padding: 12px; background: rgba(0,0,0,0.2); border: 1px solid rgba(199,154,43,0.3); border-radius: 6px; color: #fff;">{{ $robots_txt }}</textarea>
                        </div>

                        <div>
                            <div class="panel-header" style="margin-bottom: 12px;">
                                <h3>llms.txt AI Directives</h3>
                                <p style="font-size: 13px;">Declare scraping rules for large language models, ChatGPT, Claude, and AI crawlers to manage content rights.</p>
                            </div>
                            <textarea name="llms_txt" style="width: 100%; height: 260px; font-family: monospace; font-size: 14px; padding: 12px; background: rgba(0,0,0,0.2); border: 1px solid rgba(199,154,43,0.3); border-radius: 6px; color: #fff;">{{ $llms_txt }}</textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn primary">Save Crawler Directives</button>
                </form>
            </div>
        @endif

        @if($tab === 'redirects')
            <!-- 301 Redirects Tab -->
            <div class="panel">
                <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <div>
                        <h3>301 / 302 URL Redirect Manager</h3>
                        <p style="font-size: 14px; margin-top: 4px;">Prevent 404 errors by establishing active redirection records from legacy links to new URLs.</p>
                    </div>
                    <a href="{{ route('admin.redirects.create') }}" class="btn primary" style="text-decoration: none;">Add Redirect Rule</a>
                </div>
                
                @if($redirects->isEmpty())
                    <p style="opacity: 0.5; font-style: italic; text-align: center; padding: 24px;">No custom redirect rules created yet.</p>
                @else
                    <table class="admin-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 2px solid rgba(199,154,43,0.2); padding: 8px;">
                                <th style="padding: 12px 8px;">Old Path (Source)</th>
                                <th style="padding: 12px 8px;">New Path (Destination)</th>
                                <th style="padding: 12px 8px;">Type</th>
                                <th style="padding: 12px 8px;">Status</th>
                                <th style="padding: 12px 8px; text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($redirects as $redirect)
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                    <td style="padding: 12px 8px; font-family: monospace;">{{ $redirect->source }}</td>
                                    <td style="padding: 12px 8px; font-family: monospace;">{{ $redirect->destination }}</td>
                                    <td style="padding: 12px 8px;">
                                        <span style="font-size: 12px; background: rgba(255,255,255,0.1); padding: 3px 6px; border-radius: 4px; font-weight: bold;">
                                            {{ $redirect->status_code }}
                                        </span>
                                    </td>
                                    <td style="padding: 12px 8px;">
                                        @if($redirect->is_active)
                                            <span style="color: #4cd137;">✔ Active</span>
                                        @else
                                            <span style="opacity: 0.5;">Inactive</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 8px; text-align: right; display: flex; justify-content: flex-end; gap: 8px;">
                                        <a href="{{ route('admin.redirects.edit', $redirect->id) }}" class="btn secondary small" style="text-decoration: none;">Edit</a>
                                        <form method="POST" action="{{ route('admin.redirects.destroy', $redirect->id) }}" onsubmit="return confirm('Are you sure you want to delete this redirect?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn danger small">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @endif

        @if($tab === 'monitoring')
            <!-- 404 & Broken Links Tab -->
            <div class="panel">
                <div class="panel-header" style="margin-bottom: 24px; border-bottom: 1px solid rgba(199,154,43,0.1); padding-bottom: 16px;">
                    <h3>404 Monitoring & Broken Link Scanner</h3>
                    <p style="font-size: 14px; margin-top: 4px; opacity: 0.85;">Track missing pages visited by users and audit the system for internal broken links.</p>
                </div>

                <div style="margin-bottom: 30px;">
                    <h4 style="color: #c79a2b; margin-bottom: 12px; font-family: Georgia, serif;">Active 404 Requests Log</h4>
                    @if(empty($logs))
                        <p style="opacity: 0.5; font-style: italic; padding: 12px; background: rgba(255,255,255,0.02); border-radius: 4px;">No user 404 errors logged yet.</p>
                    @else
                        <table class="admin-table" style="width: 100%; border-collapse: collapse; text-align: left; margin-bottom: 16px;">
                            <thead>
                                <tr style="border-bottom: 2px solid rgba(199,154,43,0.2); padding: 8px;">
                                    <th style="padding: 12px 8px;">Path</th>
                                    <th style="padding: 12px 8px; text-align: center;">Hits</th>
                                    <th style="padding: 12px 8px;">Last Logged</th>
                                    <th style="padding: 12px 8px;">AI Recommendation</th>
                                    <th style="padding: 12px 8px; text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                        <td style="padding: 12px 8px; font-family: monospace;">{{ $log['path'] }}</td>
                                        <td style="padding: 12px 8px; text-align: center; font-weight: bold;">{{ $log['hits'] }}</td>
                                        <td style="padding: 12px 8px; font-size: 13px;">{{ $log['last_hit'] }}</td>
                                        <td style="padding: 12px 8px;">
                                            @if($log['recommendation'] && $log['recommendation'] !== '/')
                                                <span style="color: #4cd137;">✔ {{ $log['recommendation'] }}</span>
                                            @else
                                                <span style="opacity: 0.5;">Homepage (/)</span>
                                            @endif
                                        </td>
                                        <td style="padding: 12px 8px; text-align: right;">
                                            <a href="{{ route('admin.redirects.create', ['source' => $log['path'], 'destination' => $log['recommendation']]) }}" class="btn primary small" style="text-decoration: none;">Create Redirect</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <div>
                    <h4 style="color: #c79a2b; margin-bottom: 12px; font-family: Georgia, serif;">Broken Link Scanner (Top 25 Checked URLs)</h4>
                    @if(empty($brokenLinks))
                        <div style="padding: 20px; background: rgba(76, 209, 55, 0.05); border: 1px solid rgba(76, 209, 55, 0.2); border-radius: 6px; color: #4cd137;">
                            ✔ No broken URLs detected from the primary inventory (all returned status 200/300).
                        </div>
                    @else
                        <table class="admin-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                            <thead>
                                <tr style="border-bottom: 2px solid rgba(199,154,43,0.2); padding: 8px;">
                                    <th style="padding: 12px 8px;">Page Title / Path</th>
                                    <th style="padding: 12px 8px; text-align: center;">HTTP Status</th>
                                    <th style="padding: 12px 8px;">AI Recommendation</th>
                                    <th style="padding: 12px 8px; text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($brokenLinks as $broken)
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                        <td style="padding: 12px 8px;">
                                            <strong>{{ $broken['title'] }}</strong><br>
                                            <small style="font-family: monospace;">{{ $broken['path'] }}</small>
                                        </td>
                                        <td style="padding: 12px 8px; text-align: center; color: #eb4d4b; font-weight: bold;">
                                            {{ $broken['status'] }}
                                        </td>
                                        <td style="padding: 12px 8px;">
                                            @if($broken['recommendation'] && $broken['recommendation'] !== '/')
                                                <span style="color: #4cd137;">✔ {{ $broken['recommendation'] }}</span>
                                            @else
                                                <span style="opacity: 0.5;">Homepage (/)</span>
                                            @endif
                                        </td>
                                        <td style="padding: 12px 8px; text-align: right;">
                                            <a href="{{ route('admin.redirects.create', ['source' => $broken['path'], 'destination' => $broken['recommendation']]) }}" class="btn primary small" style="text-decoration: none;">Create Redirect</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

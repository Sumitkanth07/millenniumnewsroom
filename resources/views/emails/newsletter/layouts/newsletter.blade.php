<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'MILLENNIUM NEWSROOM' }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f4f6;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #1f1a12;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table {
            border-spacing: 0;
            border-collapse: collapse;
            width: 100%;
        }
        td {
            padding: 0;
        }
        img {
            border: 0;
            max-width: 100%;
            height: auto;
            display: block;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f4f4f6;
            padding-top: 20px;
            padding-bottom: 40px;
        }
        .container {
            max-width: 620px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e5e5eb;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #1f1a12;
            padding: 24px 30px;
            text-align: center;
            border-bottom: 3px solid #c79a2b;
        }
        .header-logo {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 2px;
            color: #ffffff;
            text-transform: uppercase;
            text-decoration: none;
            display: inline-block;
        }
        .header-logo span {
            color: #c79a2b;
        }
        .header-subtitle {
            font-size: 11px;
            color: #d1d1d1;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 6px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .category-pill {
            display: inline-block;
            background-color: #f7f3e8;
            color: #8c6b16;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 4px 10px;
            border-radius: 4px;
            margin-bottom: 12px;
        }
        .article-title {
            font-size: 22px;
            line-height: 1.35;
            font-weight: 800;
            color: #1f1a12;
            margin: 0 0 14px 0;
            text-decoration: none;
        }
        .article-title a {
            color: #1f1a12;
            text-decoration: none;
        }
        .featured-image-container {
            margin-bottom: 20px;
            border-radius: 6px;
            overflow: hidden;
        }
        .article-excerpt {
            font-size: 15px;
            line-height: 1.6;
            color: #4a4a50;
            margin-bottom: 22px;
        }
        .btn-primary {
            display: inline-block;
            background-color: #1f1a12;
            color: #ffffff !important;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            padding: 12px 26px;
            border-radius: 5px;
            letter-spacing: 0.5px;
            text-align: center;
        }
        .btn-primary:hover {
            background-color: #c79a2b;
        }
        .footer {
            background-color: #fafafa;
            padding: 24px 30px;
            text-align: center;
            border-top: 1px solid #eeeeee;
            font-size: 12px;
            color: #777777;
            line-height: 1.6;
        }
        .footer a {
            color: #1f1a12;
            text-decoration: underline;
        }
        .footer-branding {
            font-weight: 700;
            color: #1f1a12;
            margin-bottom: 8px;
        }
        .divider {
            height: 1px;
            background-color: #eeeeee;
            margin: 24px 0;
        }
        @media only screen and (max-width: 640px) {
            .content {
                padding: 20px !important;
            }
            .header {
                padding: 20px !important;
            }
            .article-title {
                font-size: 19px !important;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <a href="{{ config('app.url') }}" class="header-logo">MILLENNIUM<span>NEWSROOM</span></a>
                <div class="header-subtitle">{{ $headerSubtitle ?? 'Independent Business Journalism' }}</div>
            </div>
            
            <div class="content">
                @yield('content')
            </div>

            <div class="footer">
                <div class="footer-branding">MILLENNIUM NEWSROOM</div>
                <div>{{ $footerSettingText ?? 'Business, Markets, Companies and Policy Journalism' }}</div>
                <div style="margin-top: 12px;">
                    @if(isset($subscriber) && $subscriber->unsubscribe_token)
                        <a href="{{ route('newsletter.preferences', $subscriber->unsubscribe_token) }}">Manage Preferences</a> &nbsp;|&nbsp;
                        <a href="{{ route('newsletter.unsubscribe', $subscriber->unsubscribe_token) }}">Unsubscribe</a> &nbsp;|&nbsp;
                    @endif
                    <a href="{{ config('app.url') }}">Visit Website</a>
                </div>
                <div style="margin-top: 10px; font-size: 11px; color: #a0a0a0;">
                    &copy; {{ date('Y') }} MILLENNIUM NEWSROOM. All rights reserved.
                </div>
            </div>
        </div>
    </div>
</body>
</html>

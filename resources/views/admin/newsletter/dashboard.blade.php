@extends('admin.layout')

@section('content')
<div style="padding: 10px 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1 style="font-size: 24px; font-weight: 800; margin: 0; color: var(--text-color, #fff);">Newsletter Dashboard</h1>
        <div>
            <a href="{{ route('admin.newsletter.weekly.preview') }}" target="_blank" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; margin-right: 8px;">
                Preview Weekly Digest
            </a>
            <a href="{{ route('admin.newsletter.subscribers.index') }}" style="background: var(--accent-color, #c79a2b); color: #1f1a12; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 700;">
                Manage Subscribers
            </a>
        </div>
    </div>

    <!-- Stat Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 30px;">
        <div style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 20px;">
            <div style="font-size: 12px; text-transform: uppercase; color: rgba(255,255,255,0.6); font-weight: 700; letter-spacing: 1px;">Total Subscribers</div>
            <div style="font-size: 32px; font-weight: 800; color: #fff; margin-top: 6px;">{{ number_format($totalSubscribers) }}</div>
        </div>

        <div style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 20px;">
            <div style="font-size: 12px; text-transform: uppercase; color: rgba(255,255,255,0.6); font-weight: 700; letter-spacing: 1px;">Active Subscribers</div>
            <div style="font-size: 32px; font-weight: 800; color: #2ecc71; margin-top: 6px;">{{ number_format($activeSubscribers) }}</div>
        </div>

        <div style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 20px;">
            <div style="font-size: 12px; text-transform: uppercase; color: rgba(255,255,255,0.6); font-weight: 700; letter-spacing: 1px;">Unsubscribed</div>
            <div style="font-size: 32px; font-weight: 800; color: #e74c3c; margin-top: 6px;">{{ number_format($unsubscribedSubscribers) }}</div>
        </div>

        <div style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 20px;">
            <div style="font-size: 12px; text-transform: uppercase; color: rgba(255,255,255,0.6); font-weight: 700; letter-spacing: 1px;">Bounced</div>
            <div style="font-size: 32px; font-weight: 800; color: #f39c12; margin-top: 6px;">{{ number_format($bouncedSubscribers) }}</div>
        </div>

        <div style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 20px;">
            <div style="font-size: 12px; text-transform: uppercase; color: rgba(255,255,255,0.6); font-weight: 700; letter-spacing: 1px;">Sent Today</div>
            <div style="font-size: 32px; font-weight: 800; color: #3498db; margin-top: 6px;">{{ number_format($sentToday) }}</div>
        </div>

        <div style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 20px;">
            <div style="font-size: 12px; text-transform: uppercase; color: rgba(255,255,255,0.6); font-weight: 700; letter-spacing: 1px;">Sent This Week</div>
            <div style="font-size: 32px; font-weight: 800; color: #9b59b6; margin-top: 6px;">{{ number_format($sentThisWeek) }}</div>
        </div>

        <div style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 20px;">
            <div style="font-size: 12px; text-transform: uppercase; color: rgba(255,255,255,0.6); font-weight: 700; letter-spacing: 1px;">Failed Emails</div>
            <div style="font-size: 32px; font-weight: 800; color: {{ $failedCount > 0 ? '#e74c3c' : '#95a5a6' }}; margin-top: 6px;">{{ number_format($failedCount) }}</div>
        </div>
    </div>

    <!-- Quick Info & Trigger Row -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
        <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 20px;">
            <h3 style="font-size: 16px; margin: 0 0 12px 0; color: var(--accent-color, #c79a2b);">Next Scheduled Weekly Digest</h3>
            <div style="font-size: 18px; font-weight: 700; color: #fff;">
                Monday at 05:00 AM IST (Asia/Kolkata)
            </div>
            <div style="font-size: 13px; color: rgba(255,255,255,0.6); margin-top: 4px;">
                Next run: {{ $nextWeekly->format('d M Y, h:i A') }}
            </div>
            <form method="POST" action="{{ route('admin.newsletter.weekly.trigger') }}" style="margin-top: 16px;">
                @csrf
                <button type="submit" onclick="return confirm('Are you sure you want to dispatch the weekly newsletter job to queue now?')" style="background: rgba(199,154,43,0.2); border: 1px solid #c79a2b; color: #c79a2b; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 700;">
                    Dispatch Weekly Digest Now
                </button>
            </form>
        </div>

        <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 20px;">
            <h3 style="font-size: 16px; margin: 0 0 12px 0; color: var(--accent-color, #c79a2b);">Send Test Broadcast</h3>
            <form method="POST" action="{{ route('admin.newsletter.send-test') }}" style="display: flex; gap: 10px;">
                @csrf
                <input type="email" name="email" placeholder="Enter recipient email..." required style="flex: 1; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 8px 12px; border-radius: 6px; font-size: 13px;">
                <button type="submit" style="background: #1f1a12; border: 1px solid rgba(255,255,255,0.3); color: #fff; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 700;">
                    Send Test
                </button>
            </form>
            <div style="font-size: 12px; color: rgba(255,255,255,0.5); margin-top: 8px;">
                Uses exact production layout & SMTP configuration.
            </div>
        </div>
    </div>

    <!-- Recent Campaigns Table -->
    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 20px;">
        <h3 style="font-size: 16px; margin: 0 0 16px 0; color: #fff;">Recent Campaigns</h3>
        @if($recentCampaigns->isEmpty())
            <p style="color: rgba(255,255,255,0.5); font-size: 13px;">No campaigns recorded yet.</p>
        @else
            <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left; color: #fff;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.6);">
                        <th style="padding: 10px;">Campaign</th>
                        <th style="padding: 10px;">Type</th>
                        <th style="padding: 10px;">Status</th>
                        <th style="padding: 10px;">Total</th>
                        <th style="padding: 10px;">Sent</th>
                        <th style="padding: 10px;">Failed</th>
                        <th style="padding: 10px;">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentCampaigns as $c)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 10px; font-weight: 600;">{{ $c->title }}</td>
                            <td style="padding: 10px;"><span style="background: rgba(255,255,255,0.1); padding: 2px 8px; border-radius: 4px; font-size: 11px;">{{ strtoupper($c->campaign_type) }}</span></td>
                            <td style="padding: 10px;"><span style="color: {{ $c->status === 'completed' ? '#2ecc71' : ($c->status === 'failed' ? '#e74c3c' : '#f39c12') }}; font-weight: 700;">{{ strtoupper($c->status) }}</span></td>
                            <td style="padding: 10px;">{{ number_format($c->total_subscribers) }}</td>
                            <td style="padding: 10px; color: #2ecc71;">{{ number_format($c->sent_count) }}</td>
                            <td style="padding: 10px; color: #e74c3c;">{{ number_format($c->failed_count) }}</td>
                            <td style="padding: 10px; color: rgba(255,255,255,0.6);">{{ $c->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection

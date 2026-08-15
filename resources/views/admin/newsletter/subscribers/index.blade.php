@extends('admin.layout')

@section('content')
<div style="padding: 10px 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="font-size: 24px; font-weight: 800; margin: 0; color: #fff;">Subscriber Management</h1>
        <button type="button" onclick="document.getElementById('addSubscriberModal').style.display='flex'" style="background: var(--accent-color, #c79a2b); color: #1f1a12; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 700; cursor: pointer; font-size: 13px;">
            + Add Subscriber
        </button>
    </div>

    <!-- Status Filter Pills -->
    <div style="display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap;">
        @foreach(['all' => 'All', 'active' => 'Active', 'unsubscribed' => 'Unsubscribed', 'bounced' => 'Bounced', 'inactive' => 'Inactive'] as $key => $label)
            @php $isActive = request('status', 'all') === $key || (empty(request('status')) && $key === 'all'); @endphp
            <a href="{{ route('admin.newsletter.subscribers.index', array_merge(request()->query(), ['status' => $key === 'all' ? null : $key])) }}" 
               style="padding: 6px 14px; border-radius: 20px; text-decoration: none; font-size: 12px; font-weight: 700; border: 1px solid rgba(255,255,255,0.2); {{ $isActive ? 'background: #c79a2b; color: #1f1a12; border-color: #c79a2b;' : 'background: rgba(255,255,255,0.05); color: #fff;' }}">
                {{ $label }} ({{ number_format($counts[$key] ?? 0) }})
            </a>
        @endforeach
    </div>

    <!-- Search Form -->
    <form method="GET" action="{{ route('admin.newsletter.subscribers.index') }}" style="display: flex; gap: 10px; margin-bottom: 20px;">
        @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search subscribers by email or name..." style="flex: 1; max-width: 400px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 8px 12px; border-radius: 6px; font-size: 13px;">
        <button type="submit" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 600;">Search</button>
        @if(request('search'))
            <a href="{{ route('admin.newsletter.subscribers.index') }}" style="background: rgba(255,255,255,0.05); color: #ccc; padding: 8px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; display: flex; align-items: center;">Clear</a>
        @endif
    </form>

    <!-- Subscribers Table -->
    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left; color: #fff;">
            <thead>
                <tr style="background: rgba(0,0,0,0.3); border-bottom: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.6);">
                    <th style="padding: 12px;">ID</th>
                    <th style="padding: 12px;">Email & Name</th>
                    <th style="padding: 12px;">Status</th>
                    <th style="padding: 12px;">Preferences</th>
                    <th style="padding: 12px;">Subscribed Date</th>
                    <th style="padding: 12px;">Last Email Sent</th>
                    <th style="padding: 12px;">Delivery Status</th>
                    <th style="padding: 12px; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscribers as $s)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td style="padding: 12px;">#{{ $s->id }}</td>
                        <td style="padding: 12px;">
                            <div style="font-weight: 700; color: #fff;">{{ $s->email }}</div>
                            @if($s->name)<div style="font-size: 11px; color: rgba(255,255,255,0.5);">{{ $s->name }}</div>@endif
                        </td>
                        <td style="padding: 12px;">
                            @php
                                $statusColors = ['active' => '#2ecc71', 'unsubscribed' => '#e74c3c', 'bounced' => '#f39c12', 'inactive' => '#95a5a6'];
                            @endphp
                            <span style="color: {{ $statusColors[$s->status] ?? '#fff' }}; font-weight: 700; text-transform: uppercase; font-size: 11px; padding: 2px 8px; border-radius: 4px; background: rgba(0,0,0,0.3);">
                                {{ $s->status }}
                            </span>
                        </td>
                        <td style="padding: 12px; font-size: 11px; color: rgba(255,255,255,0.7);">
                            <span style="display: block;">New Post: {{ $s->notify_new_post ? '✓' : '✗' }}</span>
                            <span style="display: block;">Weekly: {{ $s->notify_weekly_digest ? '✓' : '✗' }}</span>
                        </td>
                        <td style="padding: 12px; color: rgba(255,255,255,0.7);">
                            {{ $s->subscribed_at ? $s->subscribed_at->format('M d, Y') : '-' }}
                        </td>
                        <td style="padding: 12px; color: rgba(255,255,255,0.7);">
                            {{ $s->last_email_sent_at ? $s->last_email_sent_at->format('M d, Y H:i') : 'Never' }}
                        </td>
                        <td style="padding: 12px;">
                            @if($s->last_delivery_status)
                                <span style="font-size: 11px; font-weight: 600; color: {{ $s->last_delivery_status === 'sent' ? '#2ecc71' : '#e74c3c' }};">
                                    {{ strtoupper($s->last_delivery_status) }}
                                </span>
                            @else
                                <span style="font-size: 11px; color: rgba(255,255,255,0.4);">-</span>
                            @endif
                        </td>
                        <td style="padding: 12px; text-align: right;">
                            <form method="POST" action="{{ route('admin.newsletter.subscribers.toggle', $s->id) }}" style="display: inline-block;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" style="background: rgba(255,255,255,0.1); border: none; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 11px; cursor: pointer; margin-right: 4px;">
                                    {{ $s->status === 'active' ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.newsletter.subscribers.destroy', $s->id) }}" style="display: inline-block;" onsubmit="return confirm('Delete this subscriber permanently?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: rgba(217,48,37,0.2); border: 1px solid #d93025; color: #ff6b6b; padding: 4px 8px; border-radius: 4px; font-size: 11px; cursor: pointer;">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding: 20px; text-align: center; color: rgba(255,255,255,0.5);">No subscribers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $subscribers->links() }}
    </div>
</div>

<!-- Add Subscriber Modal -->
<div id="addSubscriberModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); align-items: center; justify-content: center; z-index: 9999;">
    <div style="background: #1f1a12; border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; padding: 24px; max-width: 450px; width: 100%; color: #fff;">
        <h3 style="margin-top: 0; font-size: 18px; color: #c79a2b;">Add New Subscriber</h3>
        <form method="POST" action="{{ route('admin.newsletter.subscribers.store') }}">
            @csrf
            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 4px;">Email Address *</label>
                <input type="email" name="email" required style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 8px 12px; border-radius: 6px; font-size: 13px;">
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 4px;">Name (Optional)</label>
                <input type="text" name="name" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 8px 12px; border-radius: 6px; font-size: 13px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 4px;">Status *</label>
                <select name="status" style="width: 100%; background: #000; border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 8px 12px; border-radius: 6px; font-size: 13px;">
                    <option value="active">Active</option>
                    <option value="unsubscribed">Unsubscribed</option>
                    <option value="bounced">Bounced</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="document.getElementById('addSubscriberModal').style.display='none'" style="background: rgba(255,255,255,0.1); border: none; color: #fff; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 13px;">Cancel</button>
                <button type="submit" style="background: #c79a2b; color: #1f1a12; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 700; cursor: pointer; font-size: 13px;">Add Subscriber</button>
            </div>
        </form>
    </div>
</div>
@endsection

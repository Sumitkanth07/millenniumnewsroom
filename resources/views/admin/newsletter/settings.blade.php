@extends('admin.layout')

@section('content')
<div style="padding: 10px 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="font-size: 24px; font-weight: 800; margin: 0; color: #fff;">Newsletter Settings & Deliverability</h1>
    </div>

    <!-- Tab Navigation -->
    <div style="display: flex; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 24px;">
        <button type="button" id="tabBtnSettings" onclick="showTab('settings')" style="background: none; border: none; border-bottom: 3px solid #c79a2b; color: #c79a2b; padding: 10px 20px; font-weight: 700; cursor: pointer; font-size: 14px;">
            General Settings
        </button>
        <button type="button" id="tabBtnDeliverability" onclick="showTab('deliverability')" style="background: none; border: none; border-bottom: 3px solid transparent; color: rgba(255,255,255,0.6); padding: 10px 20px; font-weight: 700; cursor: pointer; font-size: 14px;">
            Deliverability Guide (SPF, DKIM, DMARC)
        </button>
    </div>

    <!-- Settings Panel -->
    <div id="tabSettings" style="display: block;">
        <form method="POST" action="{{ route('admin.newsletter.save-settings') }}" style="max-width: 700px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 24px;">
            @csrf

            <h3 style="margin-top: 0; font-size: 16px; color: #c79a2b; margin-bottom: 16px;">Sender Information</h3>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 6px; color: #fff;">From Name *</label>
                <input type="text" name="from_name" value="{{ $settings['from_name'] }}" required style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 10px 14px; border-radius: 6px; font-size: 13px;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 6px; color: #fff;">From Email Address *</label>
                <input type="email" name="from_email" value="{{ $settings['from_email'] }}" required style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 10px 14px; border-radius: 6px; font-size: 13px;">
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 6px; color: #fff;">Reply-To Address *</label>
                <input type="email" name="reply_to" value="{{ $settings['reply_to'] }}" required style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 10px 14px; border-radius: 6px; font-size: 13px;">
            </div>

            <h3 style="font-size: 16px; color: #c79a2b; margin-bottom: 16px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px;">Automation & Schedules</h3>

            <div style="margin-bottom: 16px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; color: #fff; font-size: 14px; font-weight: 600;">
                    <input type="checkbox" name="enable_new_post_notifications" value="1" {{ $settings['enable_new_post_notifications'] == '1' ? 'checked' : '' }} style="width: 18px; height: 18px;">
                    Enable Automatic New Post Email Notifications
                </label>
                <div style="font-size: 12px; color: rgba(255,255,255,0.5); margin-left: 28px; margin-top: 2px;">
                    Queues notification emails to active subscribers immediately when a post transitions into published status for the first time.
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; color: #fff; font-size: 14px; font-weight: 600;">
                    <input type="checkbox" name="enable_weekly_digest" value="1" {{ $settings['enable_weekly_digest'] == '1' ? 'checked' : '' }} style="width: 18px; height: 18px;">
                    Enable Weekly Digest Automation
                </label>
                <div style="font-size: 12px; color: rgba(255,255,255,0.5); margin-left: 28px; margin-top: 2px;">
                    Schedules weekly newsletter broadcast every Monday at 05:00 AM IST.
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 6px; color: #fff;">Weekly Day</label>
                    <select name="weekly_send_day" style="width: 100%; background: #000; border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 10px; border-radius: 6px; font-size: 13px;">
                        <option value="1" {{ $settings['weekly_send_day'] == '1' ? 'selected' : '' }}>Monday</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 6px; color: #fff;">Weekly Time</label>
                    <input type="text" name="weekly_send_time" value="{{ $settings['weekly_send_time'] }}" required style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 10px; border-radius: 6px; font-size: 13px;">
                </div>
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 6px; color: #fff;">Timezone</label>
                    <input type="text" name="timezone" value="{{ $settings['timezone'] }}" required style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 10px; border-radius: 6px; font-size: 13px;">
                </div>
            </div>

            <h3 style="font-size: 16px; color: #c79a2b; margin-bottom: 16px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px;">Queue & Deliverability Settings</h3>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 6px; color: #fff;">Maximum Emails Per Batch (Chunk Size)</label>
                <input type="number" name="batch_size" value="{{ $settings['batch_size'] }}" required min="10" max="1000" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 10px 14px; border-radius: 6px; font-size: 13px;">
                <div style="font-size: 11px; color: rgba(255,255,255,0.5); margin-top: 4px;">Recommended: 100 - 250 subscribers per queue job chunk.</div>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 6px; color: #fff;">Default Email Footer Line</label>
                <textarea name="default_email_footer" rows="2" required style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 10px 14px; border-radius: 6px; font-size: 13px;">{{ $settings['default_email_footer'] }}</textarea>
            </div>

            <button type="submit" style="background: #c79a2b; color: #1f1a12; border: none; padding: 10px 24px; font-size: 14px; font-weight: 700; border-radius: 6px; cursor: pointer;">
                Save Settings
            </button>
        </form>
    </div>

    <!-- Deliverability Panel -->
    <div id="tabDeliverability" style="display: none; max-width: 800px;">
        <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 24px; color: #fff; line-height: 1.6;">
            <h2 style="margin-top: 0; font-size: 20px; color: #c79a2b;">Email Deliverability & Domain Authentication Guide</h2>
            <p style="font-size: 14px; color: rgba(255,255,255,0.8);">
                To ensure newsletter emails consistently reach subscribers' primary inbox and avoid spam folders, configure the following DNS domain authentication records for <strong>millenniumnewsroom.com</strong>.
            </p>

            <div style="margin-top: 20px; padding: 16px; background: rgba(0,0,0,0.3); border-radius: 6px; border-left: 4px solid #3498db;">
                <h4 style="margin: 0 0 6px 0; color: #3498db; font-size: 15px;">1. SPF (Sender Policy Framework)</h4>
                <p style="margin: 0 0 10px 0; font-size: 13px; color: rgba(255,255,255,0.8);">
                    Specifies which mail servers are authorized to send email on behalf of millenniumnewsroom.com.
                </p>
                <div style="background: #000; padding: 10px; font-family: monospace; font-size: 12px; color: #2ecc71; border-radius: 4px;">
                    TXT @ "v=spf1 mx include:_spf.google.com include:sendgrid.net ~all"
                </div>
            </div>

            <div style="margin-top: 16px; padding: 16px; background: rgba(0,0,0,0.3); border-radius: 6px; border-left: 4px solid #9b59b6;">
                <h4 style="margin: 0 0 6px 0; color: #9b59b6; font-size: 15px;">2. DKIM (DomainKeys Identified Mail)</h4>
                <p style="margin: 0 0 10px 0; font-size: 13px; color: rgba(255,255,255,0.8);">
                    Cryptographically signs emails to prove authenticity and prevent tampering in transit.
                </p>
                <div style="background: #000; padding: 10px; font-family: monospace; font-size: 12px; color: #2ecc71; border-radius: 4px;">
                    CNAME s1._domainkey.millenniumnewsroom.com &rarr; s1.domainkey.u123.sendgrid.net
                </div>
            </div>

            <div style="margin-top: 16px; padding: 16px; background: rgba(0,0,0,0.3); border-radius: 6px; border-left: 4px solid #e74c3c;">
                <h4 style="margin: 0 0 6px 0; color: #e74c3c; font-size: 15px;">3. DMARC (Domain-based Message Authentication, Reporting & Conformance)</h4>
                <p style="margin: 0 0 10px 0; font-size: 13px; color: rgba(255,255,255,0.8);">
                    Instructs receiving mail servers how to handle emails that fail SPF or DKIM checks.
                </p>
                <div style="background: #000; padding: 10px; font-family: monospace; font-size: 12px; color: #2ecc71; border-radius: 4px;">
                    TXT _dmarc.millenniumnewsroom.com "v=DMARC1; p=quarantine; rua=mailto:dmarc@millenniumnewsroom.com; pct=100"
                </div>
            </div>

            <div style="margin-top: 20px; font-size: 13px; color: rgba(255,255,255,0.7);">
                <strong style="color: #fff;">Built-in System Safeguards Active:</strong>
                <ul style="margin-top: 6px; padding-left: 20px;">
                    <li>List-Unsubscribe and List-Unsubscribe-Post RFC-compliant headers injected automatically.</li>
                    <li>Bounce and unsubscribe suppression enforced before dispatching email jobs.</li>
                    <li>Asynchronous queued sending prevents mail server rate-limit blocks.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tab) {
    document.getElementById('tabSettings').style.display = (tab === 'settings') ? 'block' : 'none';
    document.getElementById('tabDeliverability').style.display = (tab === 'deliverability') ? 'block' : 'none';

    document.getElementById('tabBtnSettings').style.color = (tab === 'settings') ? '#c79a2b' : 'rgba(255,255,255,0.6)';
    document.getElementById('tabBtnSettings').style.borderBottomColor = (tab === 'settings') ? '#c79a2b' : 'transparent';

    document.getElementById('tabBtnDeliverability').style.color = (tab === 'deliverability') ? '#c79a2b' : 'rgba(255,255,255,0.6)';
    document.getElementById('tabBtnDeliverability').style.borderBottomColor = (tab === 'deliverability') ? '#c79a2b' : 'transparent';
}
</script>
@endsection

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account &amp; Data Deletion – {{ $appName }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #333; max-width: 720px; margin: 0 auto; padding: 24px; }
        h1 { font-size: 1.75rem; margin-top: 0; border-bottom: 2px solid #333; padding-bottom: 8px; }
        h2 { font-size: 1.25rem; margin-top: 28px; }
        .app-name { font-weight: 700; }
        ol, ul { margin: 12px 0; padding-left: 24px; }
        li { margin: 6px 0; }
        .steps { background: #f5f5f5; padding: 16px 20px; border-radius: 8px; margin: 16px 0; }
        .section { margin: 24px 0; }
        .highlight { background: #fff3cd; padding: 2px 6px; }
        table { border-collapse: collapse; width: 100%; margin: 12px 0; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f5f5f5; }
        .contact { margin-top: 28px; padding-top: 20px; border-top: 1px solid #ddd; }
        form { margin: 16px 0; }
        input, select, button { padding: 10px; margin: 4px 0; font-size: 1rem; }
        input[type="email"], input[type="text"] { width: 100%; max-width: 320px; display: block; }
        button { background: #333; color: #fff; border: none; border-radius: 6px; cursor: pointer; }
        button:hover { background: #555; }
        .success { color: #0a0; margin-top: 8px; }
        .error { color: #c00; margin-top: 8px; }
    </style>
</head>
<body>
    <h1>Account and Data Deletion – <span class="app-name">{{ $appName }}</span></h1>
    <p>This page explains how to request <strong>account deletion</strong> or <strong>data deletion</strong> for the <span class="app-name">{{ $appName }}</span> app (as shown on the Google Play store listing).</p>

    <div class="section" id="account-deletion">
        <h2>1. How to request account deletion</h2>
        <p>You can request that your <strong>account and all associated data</strong> are permanently deleted in either of these ways:</p>
        <div class="steps">
            <strong>Option A – In the app (recommended)</strong>
            <ol>
                <li>Open the <span class="app-name">{{ $appName }}</span> app on your device.</li>
                <li>Log in to your account.</li>
                <li>Go to <strong>Settings</strong> (or Profile → Settings).</li>
                <li>Tap <strong>Delete account</strong> (or similar) and follow the confirmation steps.</li>
                <li>Your account and associated data will be deleted as described below.</li>
            </ol>
            <strong>Option B – Without opening the app</strong>
            <ol>
                <li>Send an email to <strong>support@nearx.co</strong> from the email address registered with your account (or include your registered phone number).</li>
                <li>State clearly that you want to <strong>delete your account</strong>.</li>
                <li>We will verify your identity and process the deletion within 30 days.</li>
            </ol>
        </div>
        <p>You may also use the form at the bottom of this page to submit a deletion request; we will contact you to confirm.</p>
    </div>

    <div class="section" id="data-deletion">
        <h2>2. How to request deletion of some or all of your data (without deleting your account)</h2>
        <p>If you only want to remove certain data but keep your account, you can:</p>
        <div class="steps">
            <ol>
                <li>Open the <span class="app-name">{{ $appName }}</span> app and go to <strong>Settings</strong>.</li>
                <li>Use any in-app options to delete or clear specific data (e.g. profile info, listings, or chat history), if available.</li>
                <li>To request deletion of data that cannot be removed in the app, email <strong>support@nearx.co</strong> with your registered email or phone and describe which data you want deleted.</li>
            </ol>
        </div>
        <p>You may also use the form below and select “Request data deletion” so we can process your request.</p>
    </div>

    <div class="section">
        <h2>3. What data we delete and what we keep</h2>
        <p>When you request <strong>account deletion</strong>, we permanently delete or anonymise the following:</p>
        <table>
            <thead>
                <tr><th>Data type</th><th>Action</th></tr>
            </thead>
            <tbody>
                <tr><td>Account (name, email, phone, password, profile)</td><td>Deleted</td></tr>
                <tr><td>Your listings/posts</td><td>Deleted or disassociated</td></tr>
                <tr><td>Chat messages and conversations</td><td>Deleted or anonymised</td></tr>
                <tr><td>Follows, likes, reports you submitted</td><td>Deleted or anonymised</td></tr>
                <tr><td>Payment records (screenshots, addresses)</td><td>Deleted or anonymised</td></tr>
                <tr><td>Device tokens (for push notifications)</td><td>Deleted</td></tr>
                <tr><td>Support requests and feedback linked to you</td><td>Deleted or anonymised</td></tr>
            </tbody>
        </table>
        <p><strong>Data we may keep (for legal or operational reasons):</strong></p>
        <ul>
            <li>Anonymised or aggregated statistics that no longer identify you.</li>
            <li>Backups may retain your data for up to <strong>90 days</strong> after deletion; after that, backups are overwritten and your data is no longer present.</li>
            <li>Where the law requires us to retain certain records (e.g. tax, fraud), we keep only what is necessary for the required period.</li>
        </ul>
    </div>

    <div class="section">
        <h2>4. Retention after deletion</h2>
        <p>Once your account and data are deleted, they are not used for any purpose. Any remaining copies in backups are removed when those backups are rotated, within the retention period stated above (e.g. 90 days).</p>
    </div>

    <div class="section contact">
        <h2>Submit a deletion request (optional)</h2>
        <p>If you cannot use the app, you can submit a request here. We will process it and contact you at the email or phone you provide.</p>
        <form id="deletion-form" action="" method="post">
            @csrf
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <p>
                <label>Request type:</label><br>
                <select name="type" required>
                    <option value="account">Delete my account and all associated data</option>
                    <option value="data">Delete some or all of my data (keep account if possible)</option>
                </select>
            </p>
            <p>
                <label>Your registered email (optional if you provide phone):</label><br>
                <input type="email" name="email" placeholder="email@example.com">
            </p>
            <p>
                <label>Your registered phone number (optional if you provide email):</label><br>
                <input type="text" name="phone_no" placeholder="e.g. +91 9876543210">
            </p>
            <p><button type="submit">Submit request</button></p>
            <p class="error" id="form-error" style="display:none;"></p>
            <p class="success" id="form-success" style="display:none;"></p>
        </form>
    </div>

    <p class="contact"><strong>Contact</strong>: For any questions about account or data deletion, contact us at <strong>support@nearx.co</strong>.</p>

    <script>
        document.getElementById('deletion-form').addEventListener('submit', function(e) {
            e.preventDefault();
            var form = this;
            var errEl = document.getElementById('form-error');
            var okEl = document.getElementById('form-success');
            errEl.style.display = 'none';
            okEl.style.display = 'none';
            var email = (form.querySelector('[name="email"]').value || '').trim();
            var phone = (form.querySelector('[name="phone_no"]').value || '').trim();
            if (!email && !phone) {
                errEl.textContent = 'Please provide either your email or phone number.';
                errEl.style.display = 'block';
                return;
            }
            var fd = new FormData(form);
            fetch('{{ url("/api/request-deletion") }}', {
                method: 'POST',
                body: fd,
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function(r) { return r.json().then(function(d) { return { ok: r.ok, data: d }; }); })
            .then(function(result) {
                if (result.ok) {
                    okEl.textContent = result.data.message || 'Request submitted successfully.';
                    okEl.style.display = 'block';
                    form.reset();
                } else {
                    errEl.textContent = result.data.message || 'Something went wrong. Please try again or email support@nearx.co.';
                    errEl.style.display = 'block';
                }
            }).catch(function() {
                errEl.textContent = 'Request failed. Please email support@nearx.co.';
                errEl.style.display = 'block';
            });
        });
    </script>
</body>
</html>

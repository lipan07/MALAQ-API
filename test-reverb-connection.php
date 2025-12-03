<?php

/**
 * Reverb Connection Test Script
 * Run: php test-reverb-connection.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "==========================================\n";
echo "REVERB CONNECTION TEST\n";
echo "==========================================\n\n";

// 1. Check Configuration
echo "1. CHECKING CONFIGURATION...\n";
$broadcastDriver = config('broadcasting.default');
$reverbKey = config('broadcasting.connections.reverb.key');
$reverbSecret = config('broadcasting.connections.reverb.secret');
$reverbAppId = config('broadcasting.connections.reverb.app_id');
$reverbHost = config('broadcasting.connections.reverb.options.host');
$reverbPort = config('broadcasting.connections.reverb.options.port');
$reverbScheme = config('broadcasting.connections.reverb.options.scheme');

echo "   Broadcasting Driver: " . ($broadcastDriver ?: 'NULL') . "\n";
echo "   REVERB_APP_KEY: " . ($reverbKey ? 'SET (' . substr($reverbKey, 0, 10) . '...)' : 'MISSING') . "\n";
echo "   REVERB_APP_SECRET: " . ($reverbSecret ? 'SET' : 'MISSING') . "\n";
echo "   REVERB_APP_ID: " . ($reverbAppId ?: 'MISSING') . "\n";
echo "   REVERB_HOST: " . ($reverbHost ?: 'MISSING') . "\n";
echo "   REVERB_PORT: " . ($reverbPort ?: 'MISSING') . "\n";
echo "   REVERB_SCHEME: " . ($reverbScheme ?: 'MISSING') . "\n\n";

if (!$reverbHost || !$reverbKey) {
    echo "   ❌ ERROR: Reverb configuration is incomplete!\n\n";
    exit(1);
}

// 2. Build Reverb URLs
$wsUrl = ($reverbScheme === 'https' ? 'wss' : 'ws') . '://' . $reverbHost . ':' . $reverbPort . '/app/' . $reverbKey;

// Build HTTP URL - only add port if it's not standard (80 for http, 443 for https)
if (($reverbScheme === 'https' && $reverbPort == 443) || ($reverbScheme === 'http' && $reverbPort == 80)) {
    $httpUrl = $reverbScheme . '://' . $reverbHost;
} else {
    $httpUrl = $reverbScheme . '://' . $reverbHost . ':' . $reverbPort;
}
$authUrl = $httpUrl . '/broadcasting/auth';

echo "2. REVERB URLS:\n";
echo "   WebSocket URL: {$wsUrl}\n";
echo "   HTTP URL: {$httpUrl}\n";
echo "   Auth Endpoint: {$authUrl}\n\n";

// 3. Check Reverb Server Status
echo "3. CHECKING REVERB SERVER...\n";
$reverbProcess = shell_exec("ps aux | grep '[r]everb:start'");
if ($reverbProcess) {
    echo "   ✅ Reverb server is RUNNING\n";
    echo "   " . trim($reverbProcess) . "\n";
} else {
    echo "   ❌ Reverb server is NOT running!\n";
}
echo "\n";

// 4. Check Port 8080
echo "4. CHECKING PORT 8080...\n";
$portCheck = shell_exec("sudo lsof -i -P -n | grep ':8080.*LISTEN'");
if ($portCheck) {
    echo "   ✅ Port 8080 is listening\n";
    echo "   " . trim($portCheck) . "\n";
} else {
    echo "   ❌ Port 8080 is NOT listening!\n";
}
echo "\n";

// 5. Test HTTP Connection to Reverb Host
echo "5. TESTING HTTP CONNECTION...\n";
$ch = curl_init($httpUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    echo "   ❌ Connection failed: {$curlError}\n";
} else {
    echo "   ✅ HTTP connection successful (Code: {$httpCode})\n";
}
echo "\n";

// 6. Test Broadcasting Auth Endpoint
echo "6. TESTING BROADCASTING AUTH ENDPOINT...\n";
$authCh = curl_init($authUrl);
curl_setopt($authCh, CURLOPT_RETURNTRANSFER, true);
curl_setopt($authCh, CURLOPT_TIMEOUT, 5);
curl_setopt($authCh, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($authCh, CURLOPT_POST, true);
curl_setopt($authCh, CURLOPT_POSTFIELDS, json_encode([
    'socket_id' => 'test.123',
    'channel_name' => 'test-channel'
]));
curl_setopt($authCh, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
$authResponse = curl_exec($authCh);
$authHttpCode = curl_getinfo($authCh, CURLINFO_HTTP_CODE);
$authCurlError = curl_error($authCh);
curl_close($authCh);

if ($authCurlError) {
    echo "   ❌ Auth endpoint connection failed: {$authCurlError}\n";
} else {
    echo "   ✅ Auth endpoint accessible (Code: {$authHttpCode})\n";
    if ($authHttpCode == 401 || $authHttpCode == 403) {
        echo "   ℹ️  Expected: Auth requires valid token\n";
    }
}
echo "\n";

// 7. Test WebSocket Connection (using wscat if available)
echo "7. TESTING WEBSOCKET CONNECTION...\n";
$wscatCheck = shell_exec("which wscat 2>/dev/null");
if ($wscatCheck) {
    echo "   ℹ️  wscat is available. You can test manually:\n";
    echo "   Command: wscat -c {$wsUrl}\n";
} else {
    echo "   ℹ️  wscat not installed. Install with: npm install -g wscat\n";
    echo "   Or test WebSocket connection from your app\n";
}
echo "\n";

// 8. Test Broadcast
echo "8. TESTING BROADCAST...\n";
try {
    $testEvent = new \App\Events\UserStatusChanged('test-user-' . time(), 'online');
    \Log::info("🧪 TEST: Attempting to broadcast UserStatusChanged");
    broadcast($testEvent);
    \Log::info("✅ TEST: Broadcast call completed");
    echo "   ✅ Broadcast call executed successfully\n";
} catch (\Exception $e) {
    echo "   ❌ Broadcast failed: " . $e->getMessage() . "\n";
    \Log::error("❌ TEST: Broadcast failed - " . $e->getMessage());
}
echo "\n";

// 9. Check Reverb Logs
echo "9. RECENT REVERB LOGS (last 5 lines)...\n";
$logPaths = [
    '/var/log/reverb.log',
    '/var/log/supervisor/reverb-stdout.log',
    '/var/log/supervisor/reverb-stderr.log',
    storage_path('logs/reverb.log')
];

$logsFound = false;
foreach ($logPaths as $logPath) {
    if (file_exists($logPath)) {
        $logs = shell_exec("sudo tail -5 {$logPath} 2>/dev/null");
        if ($logs) {
            echo "   Log file: {$logPath}\n";
            echo "   " . str_replace("\n", "\n   ", trim($logs)) . "\n";
            $logsFound = true;
            break;
        }
    }
}

if (!$logsFound) {
    echo "   ℹ️  Reverb log files not found in common locations\n";
    echo "   Check supervisor logs: sudo tail -f /var/log/supervisor/reverb-*.log\n";
}
echo "\n";

// 10. Summary
echo "==========================================\n";
echo "SUMMARY\n";
echo "==========================================\n";
echo "WebSocket URL to use in your app:\n";
echo "  {$wsUrl}\n\n";
echo "Auth Endpoint:\n";
echo "  {$authUrl}\n\n";

if ($broadcastDriver !== 'reverb') {
    echo "⚠️  WARNING: Broadcasting driver is '{$broadcastDriver}', not 'reverb'\n";
    echo "   Fix: Set BROADCAST_CONNECTION=reverb in .env\n\n";
}

// Configuration Recommendations
echo "CONFIGURATION RECOMMENDATIONS:\n";
$issues = [];

if (filter_var($reverbHost, FILTER_VALIDATE_IP)) {
    $issues[] = "REVERB_HOST is set to IP address ({$reverbHost}). Consider using domain name for production.";
}

if ($reverbPort == 8080 && $reverbScheme == 'http') {
    $issues[] = "REVERB_PORT is 8080 with http. For production, use port 443 with https scheme.";
    $issues[] = "Clients should connect via HTTPS (port 443), not directly to port 8080.";
}

if ($reverbScheme == 'http' && $reverbPort != 80) {
    $issues[] = "Using HTTP on non-standard port. For production, use HTTPS (port 443).";
}

if (empty($issues)) {
    echo "   ✅ Configuration looks good!\n";
} else {
    echo "   ⚠️  Issues found:\n";
    foreach ($issues as $issue) {
        echo "   - {$issue}\n";
    }
    echo "\n   Recommended .env settings for production:\n";
    echo "   REVERB_HOST=your-domain.com  (not IP address)\n";
    echo "   REVERB_PORT=443\n";
    echo "   REVERB_SCHEME=https\n";
    echo "   REVERB_SERVER_PORT=8080  (server internal port)\n";
}

echo "\n==========================================\n";
echo "TEST COMPLETE\n";
echo "==========================================\n";

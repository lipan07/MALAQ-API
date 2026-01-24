<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You're invited to nearX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --text: #0f172a;
            --muted: #64748b;
            --border: #e2e8f0;
            --bg: #ffffff;
            --chip: #f8fafc;
            --success: #16a34a;
            --danger: #dc2626;
            --warning: #f59e0b;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .wrap {
            max-width: 520px;
            margin: 0 auto;
            padding: 28px 18px 40px;
        }

        .cardx {
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        }

        .hero {
            padding: 22px 22px 18px;
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.10), rgba(79, 70, 229, 0.04));
            border-bottom: 1px solid var(--border);
        }

        .brand {
            font-weight: 800;
            letter-spacing: 0.2px;
            margin: 0;
            font-size: 22px;
        }

        .subtitle {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 14px;
        }

        .content {
            padding: 18px 22px 22px;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border-radius: 999px;
            background: var(--chip);
            border: 1px solid var(--border);
            font-weight: 600;
            font-size: 13px;
        }

        .chip.success {
            color: var(--success);
            border-color: rgba(22, 163, 74, 0.25);
            background: rgba(22, 163, 74, 0.06);
        }

        .chip.warning {
            color: #92400e;
            border-color: rgba(245, 158, 11, 0.25);
            background: rgba(245, 158, 11, 0.10);
        }

        .chip.danger {
            color: var(--danger);
            border-color: rgba(220, 38, 38, 0.25);
            background: rgba(220, 38, 38, 0.06);
        }

        .invite-code {
            margin-top: 14px;
            border: 1px dashed var(--border);
            border-radius: 14px;
            padding: 14px;
            background: #fff;
        }

        .invite-code .label {
            color: var(--muted);
            font-size: 12px;
            margin-bottom: 6px;
        }

        .invite-code .code {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 4px;
        }

        .btns {
            display: grid;
            gap: 10px;
            margin-top: 18px;
        }

        .btn-primaryx {
            background: var(--primary);
            border: none;
            padding: 14px 14px;
            border-radius: 14px;
            font-weight: 700;
        }

        .btn-secondaryx {
            background: #fff;
            border: 1px solid var(--border);
            padding: 14px 14px;
            border-radius: 14px;
            font-weight: 700;
            color: var(--text);
        }

        .small {
            margin-top: 14px;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.4;
        }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="cardx">
            <div class="hero">
                <p class="brand">nearX</p>
                <p class="subtitle">
                    @if(!empty($inviterName))
                        You were invited by <strong>{{ $inviterName }}</strong>
                    @else
                        You’ve received an invite to join nearX
                    @endif
                </p>
            </div>

            <div class="content">
                @php
                    $chipClass = 'danger';
                    $chipText = 'Invalid invite';
                    if ($status === 'active') { $chipClass = 'success'; $chipText = 'Invite active'; }
                    if ($status === 'expired') { $chipClass = 'warning'; $chipText = 'Invite expired'; }
                    if ($status === 'used') { $chipClass = 'danger'; $chipText = 'Invite already used'; }
                @endphp

                <span class="chip {{ $chipClass }}">{{ $chipText }}</span>

                <div class="invite-code">
                    <div class="label">Invite code</div>
                    <div class="code">{{ $token }}</div>
                </div>

                @if($status === 'active' || $status === 'used')
                    <div class="btns">
                        <a class="btn btn-primaryx text-white" href="{{ $deepLink }}">Open nearX App</a>
                        <a class="btn btn-secondaryx" href="{{ $installUrl }}" target="_blank" rel="noopener">Install nearX (Play Store)</a>
                        <a class="btn btn-secondaryx" href="{{ $registerUrl }}">Register on Website</a>
                    </div>
                @endif

                <div class="small">
                    @if($status === 'active' && $expiresAt)
                        This invite is valid until <strong>{{ $expiresAt->format('d M Y, h:i A') }}</strong>.
                    @elseif($status === 'expired')
                        This invite link is expired. Ask your friend to share a new invite code.
                    @elseif($status === 'used')
                        This invite code was already used. Ask your friend to share a new invite code.
                    @else
                        This invite code is not valid. Please check the code and try again.
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>

</html>


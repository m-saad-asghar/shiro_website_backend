<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Website Notification</title>
</head>
<body style="margin:0;padding:0;background:#f6f7fb;font-family:Arial,Helvetica,sans-serif;">
<div style="width:100%;background:#f6f7fb;padding:0;margin:0;">
    <div style="max-width:720px;margin:0 auto;padding:24px;">
        <div style="background:#ffffff;border-radius:12px;padding:20px;border:1px solid #e9ecf3;">

          {{-- HEADER (logo left, text right) --}}
<div style="margin:0 0 14px 0;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="width:100%;border-collapse:collapse;">
        <tr>
            {{-- Logo (left) --}}
            <td style="width:160px;vertical-align:middle;padding:0;">
                <img src="https://www.shiroestate.ae/assets/logo-CebArR-m.png"
                     alt="Shiro Estate"
                     style="height:38px;width:auto;display:block;max-width:160px;">
            </td>

            {{-- Text (right) --}}
            <td style="vertical-align:middle;padding:0;">
                <div style="text-align:left;">
                    <div style="font-size:18px;line-height:1.2;color:#111827;font-weight:700;">
                        New Website Notification
                    </div>

                    <div style="margin-top:6px;font-size:13px;line-height:1.4;color:#6b7280;">
                        Received at: {{ now()->format('Y-m-d H:i:s') }}
                        @if(!empty($payload['timezone']))
                            ({{ $payload['timezone'] }})
                        @endif
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>


            {{-- Helpers to format labels/values --}}
            @php
                $formatLabel = function ($key) {
                    $key = (string) $key;
                    $key = str_replace(['-', '_'], ' ', $key);
                    $key = preg_replace('/\s+/', ' ', trim($key));
                    return mb_convert_case($key, MB_CASE_TITLE, "UTF-8");
                };

                $formatValue = function ($key, $value) {
                    // Keep message exactly as-is (no title-casing)
                    if ($key === 'message') {
                        return is_scalar($value) ? (string) $value : json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    }

                    // If complex, show JSON pretty (do not title-case)
                    if (is_array($value) || is_object($value)) {
                        return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    }

                    $str = trim((string) $value);
                    if ($str === '') return 'N/A';

                    // Don't touch URLs, emails, phone-like, or codes
                    if (filter_var($str, FILTER_VALIDATE_EMAIL)) return $str;
                    if (preg_match('/^https?:\/\//i', $str)) return $str;
                    if (preg_match('/^\+?[0-9()\-\s]{6,}$/', $str)) return $str;

                    // Convert slug/underscores/hyphens to spaced Title Case
                    $str = str_replace(['-', '_'], ' ', $str);
                    $str = preg_replace('/\s+/', ' ', trim($str));
                    return mb_convert_case($str, MB_CASE_TITLE, "UTF-8");
                };

                $safe = function ($text) {
                    return e($text);
                };

                $get = function ($k) use ($payload) {
                    return $payload[$k] ?? null;
                };
            @endphp

            {{-- Quick highlights --}}
            <div style="margin-top:14px;margin-bottom:16px;">
                <div style="background:#f9fafb;border:1px solid #e9ecf3;border-radius:12px;padding:14px;">
                    <div style="font-size:14px;color:#111827;line-height:1.6;word-break:break-word;">
                        <div><strong>Name:</strong> {{ $safe($formatValue('name', $get('name') ?? 'N/A')) }}</div>
                        <div><strong>Email:</strong> {{ $safe($formatValue('email', $get('email') ?? 'N/A')) }}</div>
                        <div><strong>Phone:</strong> {{ $safe($formatValue('phone_e164', $get('phone_e164') ?? ($get('phone') ?? 'N/A'))) }}</div>
                        <div><strong>Origin:</strong> {{ $safe($formatValue('origin', $get('origin') ?? 'N/A')) }}</div>
                    </div>
                </div>
            </div>

            {{-- Full payload table --}}
            <h3 style="margin:18px 0 10px 0;color:#111827;font-size:14px;">
                Full Data
            </h3>

            <div style="width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch;border-radius:12px;border:1px solid #e9ecf3;">
                <table cellpadding="0" cellspacing="0" style="width:100%;min-width:560px;border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th align="left" style="padding:10px;background:#f9fafb;font-size:12px;color:#111827;width:34%;border-bottom:1px solid #e9ecf3;">
                                Field
                            </th>
                            <th align="left" style="padding:10px;background:#f9fafb;font-size:12px;color:#111827;border-bottom:1px solid #e9ecf3;">
                                Value
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payload as $key => $value)
                            @if($key !== 'message')
                                @php
                                    $label = $formatLabel($key);
                                    $val = $formatValue($key, $value);
                                    $isUrl = is_string($val) && preg_match('/^https?:\/\//i', $val);
                                @endphp
                                <tr>
                                    <td style="padding:10px;font-size:12px;color:#111827;border-top:1px solid #e9ecf3;vertical-align:top;">
                                        {{ $safe($label) }}
                                    </td>
                                    <td style="padding:10px;font-size:12px;color:#111827;border-top:1px solid #e9ecf3;word-break:break-word;vertical-align:top;">
                                        @if(is_array($value) || is_object($value))
                                            <pre style="margin:0;white-space:pre-wrap;font-family:Consolas,Menlo,monospace;font-size:12px;line-height:1.4;">{{ $safe($val) }}</pre>
                                        @elseif($isUrl)
                                            <a href="{{ $val }}" style="color:#2563eb;text-decoration:underline;" target="_blank" rel="noopener">
                                                {{ $safe($val) }}
                                            </a>
                                        @else
                                            {{ $safe($val) }}
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Message section (kept as-is) --}}
            @if(!empty($payload['message']))
                <div style="margin-top:16px;padding:12px;border:1px solid #e9ecf3;border-radius:12px;background:#fafafa;">
                    <h4 style="margin:0 0 6px 0;font-size:13px;color:#111827;">Message</h4>
                    <p style="margin:0;font-size:13px;color:#111827;white-space:pre-wrap;line-height:1.55;word-break:break-word;">
                        {{ $payload['message'] }}
                    </p>
                </div>
            @endif
        </div>

        <p style="margin:12px 0 0 0;color:#9ca3af;font-size:12px;text-align:center;">
            ShiroEstate Notifications
        </p>
    </div>
</div>
</body>
</html>
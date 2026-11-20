<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jauna {{ strtolower($type) }}</title>
</head>
<body style="margin:0;padding:24px;background-color:#f4f1f8;font-family:Arial,Helvetica,sans-serif;color:#30253b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:600px;background:#ffffff;border-radius:16px;">
                    <tr>
                        <td style="padding:28px;">
                            <p style="margin:0 0 8px;color:#684b82;font-size:12px;font-weight:bold;letter-spacing:1px;text-transform:uppercase;">Radu Burziņš</p>
                            <h1 style="margin:0 0 22px;color:#30213c;font-size:24px;">Jauna {{ strtolower($type) }}</h1>
                            <p style="margin:0 0 8px;color:#655d6b;font-size:14px;">
                                No: <strong style="color:#30253b;">{{ $userName }}</strong>
                                (<a href="mailto:{{ $userEmail }}" style="color:#684b82;">{{ $userEmail }}</a>)
                            </p>
                            <div style="margin-top:20px;padding:18px;border-radius:10px;background:#f7f4f9;font-size:15px;line-height:1.7;white-space:pre-wrap;">{{ $feedbackMessage }}</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

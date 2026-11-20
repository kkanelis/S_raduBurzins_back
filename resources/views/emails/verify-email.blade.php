<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Apstiprini savu e-pasta adresi</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f1f8;font-family:Arial,Helvetica,sans-serif;color:#30253b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f4f1f8;padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:600px;background-color:#ffffff;border-radius:20px;overflow:hidden;">
                    <tr>
                        <td align="center" style="padding:34px 24px;background-color:#30213c;background-image:linear-gradient(135deg,#30213c,#6a4d83);">
                            <div style="width:58px;height:58px;line-height:58px;border-radius:18px;background-color:#ffffff;color:#60427b;font-size:23px;font-weight:bold;letter-spacing:-1px;">RB</div>
                            <p style="margin:16px 0 0;color:#ffffff;font-size:19px;font-weight:bold;">Radu Burziņš</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:38px 36px 24px;">
                            <p style="margin:0 0 10px;color:#765a8e;font-size:12px;font-weight:bold;letter-spacing:1.5px;text-transform:uppercase;">E-pasta apstiprināšana</p>
                            <h1 style="margin:0 0 18px;color:#30213c;font-size:27px;line-height:1.25;">Sveiki, {{ $userName }}!</h1>
                            <p style="margin:0 0 26px;color:#655d6b;font-size:16px;line-height:1.65;">
                                Paldies par reģistrēšanos! Lai apstiprinātu, ka šī e-pasta adrese pieder tev, nospied zemāk esošo pogu.
                            </p>
                            <table role="presentation" cellspacing="0" cellpadding="0" style="margin:0 auto 25px;">
                                <tr>
                                    <td align="center" style="border-radius:10px;background-color:#684b82;">
                                        <a href="{{ $verificationUrl }}" style="display:inline-block;padding:15px 26px;border:1px solid #684b82;border-radius:10px;color:#ffffff;font-size:16px;font-weight:bold;text-decoration:none;">Apstiprināt e-pastu</a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:0 0 10px;color:#655d6b;font-size:14px;line-height:1.6;">
                                Saite derīga {{ $expirationMinutes }} minūtes. Ja poga nedarbojas, iekopē šo adresi pārlūkā:
                            </p>
                            <p style="margin:0;word-break:break-all;font-size:13px;line-height:1.6;">
                                <a href="{{ $verificationUrl }}" style="color:#684b82;">{{ $verificationUrl }}</a>
                            </p>
                            <p style="margin:26px 0 0;color:#817987;font-size:13px;line-height:1.6;">
                                Ja nereģistrējies, vari šo ziņojumu ignorēt.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:20px 24px;background-color:#faf9fb;color:#8b8490;font-size:12px;line-height:1.6;">
                            Šis ir automātisks ziņojums. Lūdzu, neatbildi uz šo e-pastu.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

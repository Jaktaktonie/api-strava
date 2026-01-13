<!doctype html>
<html lang="pl">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Reset hasla</title>
  </head>
  <body style="margin:0;padding:0;background:#f6f7fb;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f7fb;padding:24px 0;">
      <tr>
        <td align="center">
          <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.06);">
            <tr>
              <td style="padding:24px 28px;background:#111827;color:#ffffff;font-family:Arial,sans-serif;font-size:18px;font-weight:700;">
                Strava Witelona
              </td>
            </tr>
            <tr>
              <td style="padding:28px;font-family:Arial,sans-serif;color:#111827;font-size:16px;line-height:1.5;">
                <p style="margin:0 0 12px;">Hej!</p>
                <p style="margin:0 0 16px;">Kliknij przycisk ponizej, aby ustawic nowe haslo do konta {{ $email }}.</p>
                <p style="margin:0 0 20px;">
                  <a href="{{ $url }}" style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;padding:12px 18px;border-radius:8px;font-weight:600;">
                    Ustaw nowe haslo
                  </a>
                </p>
                <p style="margin:0 0 12px;color:#6b7280;font-size:14px;">
                  Jesli przycisk nie dziala, skopiuj i wklej link do przegladarki:
                </p>
                <p style="margin:0 0 20px;word-break:break-all;">
                  <a href="{{ $url }}" style="color:#2563eb;text-decoration:underline;">{{ $url }}</a>
                </p>
                <p style="margin:0;color:#6b7280;font-size:14px;">
                  Jesli to nie Ty prosiles o reset hasla, zignoruj te wiadomosc.
                </p>
              </td>
            </tr>
            <tr>
              <td style="padding:16px 28px;background:#f3f4f6;font-family:Arial,sans-serif;color:#6b7280;font-size:12px;">
                Wiadomosc automatyczna, prosimy nie odpowiadac.
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Code de vérification</title>
</head>
<body style="margin:0;padding:0;background:#f0fdf4;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0fdf4;padding:40px 16px;">
    <tr>
      <td align="center">
        <table width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">
          <!-- Header -->
          <tr>
            <td style="background:linear-gradient(135deg,#052e16,#16a34a);padding:32px 24px;text-align:center;">
              <p style="margin:0 0 8px;font-size:32px;">🌿</p>
              <p style="margin:0;font-size:22px;font-weight:900;color:#fff;">AntiGaspiCI</p>
              <p style="margin:4px 0 0;font-size:13px;color:rgba(255,255,255,.7);">Lutte contre le gaspillage alimentaire</p>
            </td>
          </tr>
          <!-- Body -->
          <tr>
            <td style="padding:32px 32px 24px;">
              <p style="margin:0 0 8px;font-size:16px;color:#0f172a;">Bonjour <strong>{{ $prenom }}</strong>,</p>
              <p style="margin:0 0 28px;font-size:14px;color:#64748b;line-height:1.6;">
                Voici votre code de vérification pour activer votre compte AntiGaspiCI.
                Ce code est valable <strong>10 minutes</strong>.
              </p>

              <!-- OTP Box -->
              <div style="background:#f0fdf4;border:2px solid #16a34a;border-radius:16px;padding:24px;text-align:center;margin-bottom:28px;">
                <p style="margin:0 0 4px;font-size:12px;font-weight:700;color:#16a34a;text-transform:uppercase;letter-spacing:1px;">Code de vérification</p>
                <p style="margin:0;font-size:44px;font-weight:900;letter-spacing:12px;color:#052e16;">{{ $code }}</p>
              </div>

              <p style="margin:0 0 8px;font-size:13px;color:#94a3b8;">
                Si vous n'avez pas créé ce compte, ignorez cet email.
              </p>
            </td>
          </tr>
          <!-- Footer -->
          <tr>
            <td style="background:#f8fafc;padding:16px 32px;text-align:center;border-top:1px solid #e2e8f0;">
              <p style="margin:0;font-size:12px;color:#94a3b8;">© 2026 AntiGaspiCI · Côte d'Ivoire</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de votre email</title>
</head>
<body style="margin:0;padding:0;background:#f4f7f6;font-family:'Segoe UI',Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7f6;padding:40px 0;">
        <tr>
            <td align="center">
                <table width="520" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">

                    {{-- Header --}}
                    <tr>
                        <td style="background:#16a34a;padding:32px 40px;text-align:center;">
                            <div style="font-size:2rem;">🌿</div>
                            <div style="color:#ffffff;font-size:1.4rem;font-weight:700;margin-top:8px;">AntiGaspiCI</div>
                            <div style="color:#bbf7d0;font-size:.85rem;margin-top:4px;">Plateforme Anti-Gaspillage Alimentaire</div>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:40px 40px 32px;">
                            <p style="font-size:1rem;color:#374151;margin:0 0 16px;">Bonjour <strong>{{ $prenom }}</strong>,</p>
                            <p style="font-size:.95rem;color:#6b7280;margin:0 0 28px;line-height:1.6;">
                                Merci de rejoindre <strong style="color:#16a34a;">AntiGaspiCI</strong> ! Pour finaliser la création de votre compte, confirmez votre adresse email avec le code ci-dessous. Il est valable <strong>10 minutes</strong>.
                            </p>

                            {{-- OTP Box --}}
                            <div style="text-align:center;margin:0 0 28px;">
                                <div style="display:inline-block;background:#f0fdf4;border:2px dashed #16a34a;border-radius:12px;padding:20px 48px;">
                                    <div style="font-size:2.4rem;font-weight:800;letter-spacing:12px;color:#15803d;">{{ $otp }}</div>
                                    <div style="font-size:.75rem;color:#6b7280;margin-top:6px;">Code de confirmation — valable 10 min</div>
                                </div>
                            </div>

                            <p style="font-size:.85rem;color:#9ca3af;line-height:1.6;margin:0;">
                                Si vous n'avez pas créé de compte sur AntiGaspiCI, ignorez cet email.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background:#f9fafb;padding:20px 40px;border-top:1px solid #f0f0f0;text-align:center;">
                            <p style="font-size:.75rem;color:#9ca3af;margin:0;">© {{ date('Y') }} AntiGaspiCI — Côte d'Ivoire</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bienvenue sur AntiGaspiCI</title>
</head>
<body style="margin:0;padding:0;background:#f4f7f4;font-family:Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7f4;padding:40px 20px;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">

        <!-- HEADER -->
        <tr>
          <td style="background:linear-gradient(135deg,#16a34a,#22c55e);padding:40px 48px;text-align:center;">
            <div style="font-size:2.8rem;margin-bottom:8px;">🌿</div>
            <h1 style="margin:0;color:#fff;font-size:1.7rem;font-weight:800;">AntiGaspiCI</h1>
            <p style="margin:6px 0 0;color:rgba(255,255,255,.85);font-size:0.875rem;">Plateforme Anti-Gaspillage Alimentaire &middot; C&ocirc;te d'Ivoire</p>
          </td>
        </tr>

        <!-- BODY -->
        <tr>
          <td style="padding:44px 48px;">
            <h2 style="margin:0 0 12px;font-size:1.5rem;font-weight:800;color:#0a0a0a;">
              Bienvenue, {{ $user->prenom }} ! &#128075;
            </h2>
            <p style="margin:0 0 24px;color:#555;font-size:0.95rem;line-height:1.7;">
              Ton compte <strong>AntiGaspiCI</strong> a &#233;t&#233; cr&#233;&#233; avec succ&#232;s. Tu fais maintenant partie d'une communaut&#233; engag&#233;e pour r&#233;duire le gaspillage alimentaire en C&#244;te d'Ivoire.
            </p>

            <!-- INFO CARD -->
            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0fdf4;border-radius:14px;border:1.5px solid #bbf7d0;margin-bottom:28px;">
              <tr>
                <td style="padding:20px 24px;">
                  <p style="margin:0 0 12px;font-size:0.78rem;font-weight:700;color:#16a34a;letter-spacing:1px;text-transform:uppercase;">Informations de ton compte</p>
                  <table width="100%">
                    <tr>
                      <td style="padding:5px 0;color:#555;font-size:0.875rem;">&#128100; Nom</td>
                      <td style="padding:5px 0;font-weight:700;color:#111;font-size:0.875rem;text-align:right;">{{ $user->prenom }} {{ $user->nom }}</td>
                    </tr>
                    <tr>
                      <td style="padding:5px 0;color:#555;font-size:0.875rem;">&#128231; Email</td>
                      <td style="padding:5px 0;font-weight:700;color:#111;font-size:0.875rem;text-align:right;">{{ $user->email }}</td>
                    </tr>
                    <tr>
                      <td style="padding:5px 0;color:#555;font-size:0.875rem;">&#127991; R&ocirc;le</td>
                      <td style="padding:5px 0;font-weight:700;color:#16a34a;font-size:0.875rem;text-align:right;text-transform:capitalize;">{{ $user->role }}</td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

            @if($user->role === 'fournisseur')
            <p style="margin:0 0 20px;color:#555;font-size:0.9rem;line-height:1.6;">
              &#127978; En tant que <strong>fournisseur</strong>, tu peux publier tes surplus alimentaires et les proposer &#224; des acheteurs, associations et &#233;leveurs proches de toi.
            </p>
            @else
            <p style="margin:0 0 20px;color:#555;font-size:0.9rem;line-height:1.6;">
              &#128722; En tant qu'<strong>acheteur</strong>, tu peux parcourir les annonces de surplus alimentaires, faire des r&#233;servations et contribuer &#224; r&#233;duire le gaspillage.
            </p>
            @endif

            <!-- CTA BUTTON -->
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td align="center" style="padding:8px 0 32px;">
                  <a href="{{ config('app.url') }}/dashboard"
                     style="display:inline-block;background:#16a34a;color:#fff;text-decoration:none;padding:16px 44px;border-radius:50px;font-size:0.95rem;font-weight:700;">
                    Acc&#233;der &#224; mon espace &rarr;
                  </a>
                </td>
              </tr>
            </table>

            <hr style="border:none;border-top:1px solid #f0f0f0;margin:0 0 24px;">

            <!-- NEXT STEPS -->
            <p style="margin:0 0 14px;font-size:0.875rem;font-weight:700;color:#111;">Pour commencer :</p>
            @if($user->role === 'fournisseur')
            <p style="margin:0 0 8px;font-size:0.875rem;color:#555;">&#128247;&nbsp; Publie ta premi&#232;re annonce de surplus</p>
            <p style="margin:0 0 8px;font-size:0.875rem;color:#555;">&#128205;&nbsp; Ajoute ton adresse de collecte</p>
            <p style="margin:0 0 8px;font-size:0.875rem;color:#555;">&#128172;&nbsp; R&#233;ponds aux demandes de r&#233;servation</p>
            @else
            <p style="margin:0 0 8px;font-size:0.875rem;color:#555;">&#128269;&nbsp; Parcours les annonces disponibles pr&#232;s de toi</p>
            <p style="margin:0 0 8px;font-size:0.875rem;color:#555;">&#129309;&nbsp; Fais une demande de r&#233;servation</p>
            <p style="margin:0 0 8px;font-size:0.875rem;color:#555;">&#11088;&nbsp; Laisse un avis apr&#232;s chaque &#233;change</p>
            @endif
          </td>
        </tr>

        <!-- FOOTER -->
        <tr>
          <td style="background:#f9fafb;padding:24px 48px;text-align:center;border-top:1px solid #f0f0f0;">
            <p style="margin:0 0 4px;font-size:0.75rem;color:#aaa;">
              Tu re&#231;ois cet email car tu viens de cr&#233;er un compte sur AntiGaspiCI.
            </p>
            <p style="margin:0;font-size:0.75rem;color:#aaa;">
              &copy; {{ date('Y') }} AntiGaspiCI &middot; Abidjan, C&ocirc;te d'Ivoire
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>

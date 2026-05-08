<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:0; }
    .wrapper { max-width:600px; margin:30px auto; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,.08); }
    .header { background:#16a34a; padding:28px 32px; text-align:center; }
    .header h1 { color:#fff; margin:0; font-size:1.4rem; }
    .header p { color:#bbf7d0; margin:6px 0 0; font-size:.85rem; }
    .body { padding:32px; }
    .field { margin-bottom:20px; }
    .field label { display:block; font-size:.75rem; font-weight:700; color:#888; text-transform:uppercase; margin-bottom:5px; }
    .field p { margin:0; font-size:.95rem; color:#1a1a2e; background:#f9f9f9; padding:12px 16px; border-radius:8px; border-left:3px solid #16a34a; }
    .message-box { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:16px 20px; margin-top:8px; font-size:.9rem; color:#374151; line-height:1.7; white-space:pre-wrap; }
    .footer { background:#f9f9f9; padding:16px 32px; text-align:center; font-size:.75rem; color:#aaa; border-top:1px solid #f0f0f0; }
    .reply-btn { display:inline-block; margin-top:20px; background:#16a34a; color:#fff; padding:12px 28px; border-radius:30px; text-decoration:none; font-weight:700; font-size:.85rem; }
</style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>🌿 AntiGaspi CI</h1>
        <p>Nouveau message reçu via le formulaire de contact</p>
    </div>
    <div class="body">
        <div class="field">
            <label>Expéditeur</label>
            <p>{{ $nomExpediteur }}</p>
        </div>
        <div class="field">
            <label>Email</label>
            <p>{{ $emailExpediteur }}</p>
        </div>
        <div class="field">
            <label>Sujet</label>
            <p>{{ $sujet }}</p>
        </div>
        <div class="field">
            <label>Message</label>
            <div class="message-box">{{ $contenu }}</div>
        </div>
        <div style="text-align:center;">
            <a href="mailto:{{ $emailExpediteur }}" class="reply-btn">
                ↩ Répondre à {{ $nomExpediteur }}
            </a>
        </div>
    </div>
    <div class="footer">
        AntiGaspi CI — Plateforme anti-gaspillage alimentaire · Abidjan, Côte d'Ivoire<br>
        Ce message a été envoyé depuis le formulaire de contact de antigaspi-ci.com
    </div>
</div>
</body>
</html>

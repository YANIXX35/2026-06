<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:0; }
    .wrapper { max-width:600px; margin:30px auto; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,.08); }
    .header { background:linear-gradient(135deg,#dc2626,#b91c1c); padding:28px 32px; text-align:center; }
    .header h1 { color:#fff; margin:0; font-size:1.4rem; }
    .header p  { color:#fecaca; margin:6px 0 0; font-size:.85rem; }
    .big-icon { font-size:3.5rem; text-align:center; padding:24px 0 8px; }
    .body { padding:16px 32px 32px; }
    .alert-box { background:#fef2f2; border:1px solid #fecaca; border-radius:10px; padding:16px 20px; margin-bottom:24px; }
    .alert-box p { margin:0; font-size:.9rem; color:#991b1b; line-height:1.6; }
    .field { margin-bottom:14px; display:flex; gap:10px; align-items:flex-start; }
    .field .label { font-size:.75rem; font-weight:700; color:#888; text-transform:uppercase; min-width:130px; padding-top:2px; }
    .field .value { font-size:.88rem; color:#1a1a2e; background:#f9f9f9; padding:8px 12px; border-radius:8px; flex:1; border-left:3px solid #ef4444; }
    .suggestion-box { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:16px 20px; margin:24px 0; }
    .suggestion-box h4 { margin:0 0 10px; font-size:.85rem; color:#166534; font-weight:700; }
    .suggestion-box p { margin:0; font-size:.82rem; color:#15803d; line-height:1.7; }
    .btn { display:inline-block; margin-top:24px; background:#16a34a; color:#fff !important; padding:13px 30px; border-radius:30px; text-decoration:none; font-weight:700; font-size:.88rem; }
    .footer { background:#f9f9f9; padding:16px 32px; text-align:center; font-size:.72rem; color:#aaa; border-top:1px solid #f0f0f0; }
</style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>🌿 AntiGaspi CI</h1>
        <p>Mise à jour sur votre réservation</p>
    </div>
    <div class="big-icon">❌</div>
    <div class="body">
        <div class="alert-box">
            <p>
                Bonjour <strong>{{ $reservation->acheteur->prenom ?? 'Acheteur' }}</strong>,<br>
                Malheureusement, le fournisseur <strong>{{ $reservation->annonce->user->prenom ?? '' }} {{ $reservation->annonce->user->nom ?? '' }}</strong>
                n'a pas pu accepter votre demande de réservation pour cette annonce. L'annonce est de nouveau disponible.
            </p>
        </div>

        <div class="field">
            <span class="label">Annonce</span>
            <span class="value">{{ $reservation->annonce->titre }}</span>
        </div>
        <div class="field">
            <span class="label">Fournisseur</span>
            <span class="value">{{ $reservation->annonce->user->prenom ?? '—' }} {{ $reservation->annonce->user->nom ?? '' }}</span>
        </div>
        <div class="field">
            <span class="label">Quantité demandée</span>
            <span class="value">{{ $reservation->quantite_demandee }} {{ $reservation->annonce->unite }}</span>
        </div>

        <div class="suggestion-box">
            <h4>🔍 D'autres surplus vous attendent !</h4>
            <p>
                Ne vous découragez pas — de nombreuses autres annonces sont disponibles sur AntiGaspi CI.
                Explorez la boutique pour trouver d'autres surplus alimentaires près de chez vous.
            </p>
        </div>

        <div style="text-align:center;">
            <a href="{{ route('annonces.index') }}" class="btn">
                🛒 Explorer d'autres annonces
            </a>
        </div>
    </div>
    <div class="footer">
        AntiGaspi CI — Plateforme anti-gaspillage alimentaire · Abidjan, Côte d'Ivoire<br>
        Vous recevez cet email car vous avez effectué une réservation sur antigaspi-ci.com
    </div>
</div>
</body>
</html>

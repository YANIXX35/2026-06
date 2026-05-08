<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:0; }
    .wrapper { max-width:600px; margin:30px auto; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,.08); }
    .header { background:#16a34a; padding:28px 32px; text-align:center; }
    .header h1 { color:#fff; margin:0; font-size:1.4rem; }
    .header p  { color:#bbf7d0; margin:6px 0 0; font-size:.85rem; }
    .body { padding:32px; }
    .alert-box { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:16px 20px; margin-bottom:24px; }
    .alert-box p { margin:0; font-size:.9rem; color:#166534; line-height:1.6; }
    .field { margin-bottom:14px; display:flex; gap:10px; align-items:flex-start; }
    .field .label { font-size:.75rem; font-weight:700; color:#888; text-transform:uppercase; min-width:130px; padding-top:2px; }
    .field .value { font-size:.88rem; color:#1a1a2e; background:#f9f9f9; padding:8px 12px; border-radius:8px; flex:1; border-left:3px solid #16a34a; }
    .btn { display:inline-block; margin-top:24px; background:#16a34a; color:#fff !important; padding:13px 30px; border-radius:30px; text-decoration:none; font-weight:700; font-size:.88rem; }
    .footer { background:#f9f9f9; padding:16px 32px; text-align:center; font-size:.72rem; color:#aaa; border-top:1px solid #f0f0f0; }
</style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>🌿 AntiGaspi CI</h1>
        <p>Vous avez reçu une nouvelle réservation !</p>
    </div>
    <div class="body">
        <div class="alert-box">
            <p>
                Bonjour <strong>{{ $reservation->annonce->user->prenom ?? 'Fournisseur' }}</strong>,<br>
                <strong>{{ $reservation->acheteur->prenom }} {{ $reservation->acheteur->nom }}</strong>
                vient de réserver votre annonce. Connectez-vous pour accepter ou refuser la demande.
            </p>
        </div>

        <div class="field">
            <span class="label">Annonce</span>
            <span class="value">{{ $reservation->annonce->titre }}</span>
        </div>
        <div class="field">
            <span class="label">Acheteur</span>
            <span class="value">{{ $reservation->acheteur->prenom }} {{ $reservation->acheteur->nom }}</span>
        </div>
        <div class="field">
            <span class="label">Email acheteur</span>
            <span class="value">{{ $reservation->acheteur->email }}</span>
        </div>
        <div class="field">
            <span class="label">Quantité demandée</span>
            <span class="value">{{ $reservation->quantite_demandee }} {{ $reservation->annonce->unite }}</span>
        </div>
        @if($reservation->message)
        <div class="field">
            <span class="label">Message</span>
            <span class="value">{{ $reservation->message }}</span>
        </div>
        @endif
        @if($reservation->date_collecte_souhaitee)
        <div class="field">
            <span class="label">Date souhaitée</span>
            <span class="value">{{ \Carbon\Carbon::parse($reservation->date_collecte_souhaitee)->format('d/m/Y') }}</span>
        </div>
        @endif

        <div style="text-align:center;">
            <a href="{{ route('reservations.mes-reservations') }}" class="btn">
                ✅ Voir et gérer la réservation
            </a>
        </div>
    </div>
    <div class="footer">
        AntiGaspi CI — Plateforme anti-gaspillage alimentaire · Abidjan, Côte d'Ivoire<br>
        Vous recevez cet email car vous avez publié une annonce sur antigaspi-ci.com
    </div>
</div>
</body>
</html>

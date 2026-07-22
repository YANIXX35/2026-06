@extends('layouts.front')
@section('title','Conditions Générales d\'Utilisation')
@section('description','Conditions générales d\'utilisation de la plateforme AntiGaspiCI.')

@push('styles')
<style>
.legal-section{padding:56px 48px 80px;background:#fff;}
.legal-inner{max-width:760px;margin:0 auto;}
.legal-inner h1{font-family:'Rubik',sans-serif;font-size:1.7rem;font-weight:900;color:var(--text);margin-bottom:8px;}
.legal-updated{font-size:.78rem;color:var(--muted-2);margin-bottom:32px;}
.legal-inner h2{font-family:'Rubik',sans-serif;font-size:1.05rem;font-weight:800;color:var(--text);margin:28px 0 10px;}
.legal-inner p,.legal-inner li{font-size:.9rem;color:var(--muted);line-height:1.75;}
.legal-inner ul{padding-left:20px;margin-bottom:12px;}
.legal-note{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:16px 20px;margin-bottom:28px;font-size:.85rem;color:var(--muted);}
@media(max-width:768px){.legal-section{padding:36px 16px 60px;}}
</style>
@endpush

@section('content')
<section class="legal-section">
    <div class="legal-inner">
        <h1>Conditions Générales d'Utilisation</h1>
        <div class="legal-updated">Dernière mise à jour : {{ now()->format('d/m/Y') }}</div>

        <div class="legal-note">
            Prototype académique développé dans le cadre d'un mémoire de fin d'études. Ces conditions décrivent
            l'usage réel attendu de la plateforme telle que déployée.
        </div>

        <h2>1. Objet</h2>
        <p>AntiGaspiCI met en relation des fournisseurs (restaurants, marchés, agriculteurs...) disposant de surplus
        alimentaires et des acheteurs (particuliers, associations, éleveurs, unités de transformation) intéressés par
        ces surplus, sous forme de don, de vente à prix réduit, d'alimentation animale ou de transformation.</p>

        <h2>2. Inscription et comptes</h2>
        <p>L'inscription est gratuite. Deux profils sont proposés : acheteur et fournisseur. L'adresse e-mail doit
        être vérifiée par code de confirmation (OTP) avant l'activation complète du compte.</p>

        <h2>3. Annonces</h2>
        <p>Le fournisseur est seul responsable de l'exactitude, de la légalité et de la conformité sanitaire des
        produits qu'il propose. Une annonce reste active 7 jours par défaut et peut être renouvelée, modifiée ou
        clôturée par son auteur.</p>

        <h2>4. Réservation et transaction</h2>
        <p>La réservation est gratuite. Pour une annonce en don, la collecte se fait directement entre les deux
        utilisateurs, sans paiement. Pour une annonce à prix réduit, le paiement s'effectue dans l'application via
        Wave ou Moov Money ; dans cette version de démonstration, ce paiement mobile est simulé et ne débite aucune
        somme réelle.</p>

        <h2>5. Comportement attendu</h2>
        <p>Chaque utilisateur s'engage à fournir des informations exactes, à respecter les rendez-vous de collecte
        convenus, et à ne pas publier de contenu trompeur. Un système de signalement et d'avis permet de modérer les
        comportements abusifs.</p>

        <h2>6. Responsabilité de la plateforme</h2>
        <p>AntiGaspiCI agit uniquement comme intermédiaire technique de mise en relation. La plateforme n'est pas
        partie aux transactions conclues entre fournisseurs et acheteurs et ne peut être tenue responsable des
        litiges relatifs à la qualité des produits échangés.</p>

        <h2>7. Résiliation</h2>
        <p>Tout utilisateur peut demander la suppression de son compte à tout moment via la
        <a href="{{ route('contact') }}">page de contact</a>. L'administrateur peut suspendre un compte en cas de
        signalement fondé ou de non-respect des présentes conditions.</p>

        <h2>8. Sécurité des Échanges et Transactions Hors Plateforme</h2>
        <p>Pour la sécurité de tous les utilisateurs, AntiGaspiCI propose un système de messagerie interne et un Proxy WhatsApp Officiel qui conserve un journal d'audit en base de données. 
        <strong>Toute transaction financière effectuée en dehors de la plateforme ou en liquide sans l'utilisation du Code PIN Séquestre engage la seule responsabilité des participants.</strong> 
        AntiGaspiCI décline toute responsabilité et ne pourra procéder à aucun remboursement en cas de litige financier survenu lors d'un paiement direct non sécurisé hors application.</p>
    </div>
</section>
@endsection

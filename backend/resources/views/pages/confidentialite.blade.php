@extends('layouts.front')
@section('title','Politique de confidentialité')
@section('description','Comment AntiGaspiCI collecte et utilise vos données.')

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
        <h1>Politique de confidentialité</h1>
        <div class="legal-updated">Dernière mise à jour : {{ now()->format('d/m/Y') }}</div>

        <div class="legal-note">
            Prototype académique : cette politique décrit les données réellement traitées par la plateforme telle
            qu'elle est déployée, dans un objectif de transparence, et non un texte juridique générique.
        </div>

        <h2>Données collectées</h2>
        <ul>
            <li>À l'inscription : nom, adresse e-mail (vérifiée par code OTP), mot de passe (haché), rôle choisi (acheteur ou fournisseur).</li>
            <li>Pour les fournisseurs : titre, description, catégorie, prix ou mention « don », quantité, localisation et photos des annonces publiées.</li>
            <li>Pour les réservations/commandes : quantité demandée, message optionnel, et — en cas de vente à prix réduit — le mode de paiement choisi (Wave ou Moov Money) et le numéro de téléphone associé (paiement simulé dans cette version de démonstration, aucune donnée bancaire réelle n'est traitée).</li>
            <li>Connexion via Google ou Facebook (OAuth), si vous choisissez cette option.</li>
            <li>Messages échangés avec le chatbot IA (assistant AntiGaspiCI), pour améliorer les réponses fournies.</li>
        </ul>

        <h2>Finalité</h2>
        <p>Ces données servent uniquement à faire fonctionner la mise en relation entre fournisseurs et acheteurs de
        surplus alimentaires (affichage des annonces, réservation, notifications, avis) et à sécuriser l'accès au compte.</p>

        <h2>Partage des données</h2>
        <p>Les données ne sont pas revendues à des tiers. Elles sont hébergées chez Render/Aiven (base de données) et
        Cloudinary (photos), et transitent le cas échéant par l'API Google Gemini pour le chatbot.</p>

        <h2>Conservation et droits</h2>
        <p>Vos données sont conservées tant que votre compte est actif. Vous pouvez demander l'accès, la correction
        ou la suppression de vos données via la <a href="{{ route('contact') }}">page de contact</a>.</p>
    </div>
</section>
@endsection

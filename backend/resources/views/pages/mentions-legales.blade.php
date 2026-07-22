@extends('layouts.front')
@section('title','Mentions légales')
@section('description','Mentions légales du projet AntiGaspiCI.')

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
        <h1>Mentions légales</h1>
        <div class="legal-updated">Dernière mise à jour : {{ now()->format('d/m/Y') }}</div>

        <div class="legal-note">
            AntiGaspiCI est un projet réalisé dans le cadre d'un mémoire académique de fin d'études (lutte contre le
            gaspillage alimentaire en Côte d'Ivoire). Cette page décrit le fonctionnement réel du prototype déployé ;
            elle n'engage pas d'entité commerciale enregistrée.
        </div>

        <h2>Éditeur du site</h2>
        <p>Ce site est édité à titre personnel et académique par l'auteur du mémoire AntiGaspiCI, dans le cadre de son
        travail de fin d'études. Pour toute question relative à l'éditeur, utiliser le <a href="{{ route('contact') }}">formulaire de contact</a>.</p>

        <h2>Hébergement</h2>
        <p>Application hébergée par Render (render.com). Base de données hébergée par Aiven (PostgreSQL managé).
        Les photos d'annonces sont hébergées par Cloudinary.</p>

        <h2>Propriété intellectuelle</h2>
        <p>Le code source, les contenus et l'identité visuelle d'AntiGaspiCI sont produits dans le cadre du mémoire
        cité ci-dessus. Les articles affichés dans la section Blog proviennent de flux RSS tiers (ADEME, Actu-Environnement)
        et restent la propriété de leurs éditeurs respectifs ; seul un extrait et un lien vers l'article original sont repris ici.</p>

        <h2>Responsabilité</h2>
        <p>AntiGaspiCI est une plateforme de mise en relation entre fournisseurs et acheteurs de surplus alimentaires.
        Le service n'est pas partie aux transactions conclues entre utilisateurs et ne garantit pas la qualité, la
        conformité sanitaire ou l'exactitude des annonces publiées par les fournisseurs.</p>

        <h2>Contact</h2>
        <p>Pour toute question, réclamation ou signalement : <a href="{{ route('contact') }}">page de contact</a>.</p>
    </div>
</section>
@endsection

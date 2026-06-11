@extends('layouts.dashboard')
@section('title','Modifier '.$categorie->nom)
@section('page-title','Modifier la catégorie')

@section('sidebar-nav')
<span class="nav-section">Vue globale</span>
<a href="{{ route('admin.dashboard') }}" class="nav-item"><span class="nav-icon"><i class="fas fa-th-large"></i></span> Tableau de bord</a>
<span class="nav-section">Gestion</span>
<a href="{{ route('admin.utilisateurs') }}" class="nav-item"><span class="nav-icon"><i class="fas fa-users"></i></span> Utilisateurs</a>
<a href="{{ route('admin.annonces') }}" class="nav-item"><span class="nav-icon"><i class="fas fa-bullhorn"></i></span> Annonces</a>
<a href="{{ route('admin.categories.index') }}" class="nav-item active"><span class="nav-icon"><i class="fas fa-tags"></i></span> Catégories</a>
<a href="{{ route('admin.signalements') }}" class="nav-item"><span class="nav-icon"><i class="fas fa-flag"></i></span> Signalements</a>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>Modifier <span style="color:var(--green);">{{ $categorie->icone }} {{ $categorie->nom }}</span></h1>
    </div>
    <a href="{{ route('admin.categories.index') }}"
       style="display:flex;align-items:center;gap:7px;background:#fff;border:1px solid var(--border);
              padding:9px 18px;border-radius:50px;font-size:.8rem;font-weight:700;color:var(--muted);text-decoration:none;">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<div style="max-width:560px;">
    <div class="card" style="padding:28px;">
        @if(session('success'))
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:.82rem;font-weight:600;">
                <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:.82rem;">
                @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
            </div>
        @endif

        <form action="{{ route('admin.categories.update', $categorie) }}" method="POST">
            @csrf @method('PUT')
            @include('admin.categories._form', ['categorie' => $categorie])
            <div style="margin-top:20px;display:flex;gap:10px;">
                <button type="submit"
                    style="background:var(--green);color:#fff;border:none;padding:10px 24px;border-radius:50px;font-weight:700;font-size:.85rem;cursor:pointer;">
                    <i class="fas fa-save me-1"></i> Enregistrer
                </button>
                <a href="{{ route('admin.categories.index') }}"
                   style="background:#fff;border:1px solid var(--border);color:var(--muted);padding:10px 24px;border-radius:50px;font-weight:700;font-size:.85rem;text-decoration:none;">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

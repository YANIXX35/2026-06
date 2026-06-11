@extends('layouts.dashboard')
@section('title','Utilisateurs')
@section('page-title','Utilisateurs')

@section('sidebar-nav')
<span class="nav-section">Vue globale</span>
<a href="{{ route('admin.dashboard') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-th-large"></i></span> Tableau de bord
</a>
<span class="nav-section">Gestion</span>
<a href="{{ route('admin.utilisateurs') }}" class="nav-item active">
    <span class="nav-icon"><i class="fas fa-users"></i></span> Utilisateurs
</a>
<a href="{{ route('admin.annonces') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-bullhorn"></i></span> Annonces
</a>
<a href="{{ route('admin.categories.index') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-tags"></i></span> Catégories
</a>
<a href="{{ route('admin.signalements') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-flag"></i></span> Signalements
</a>
<span class="nav-section">Plateforme</span>
<a href="{{ route('annonces.index') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-globe"></i></span> Voir le site
</a>
@endsection

@section('content')

{{-- FLASH MESSAGES --}}
@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:12px 18px;border-radius:10px;margin-bottom:16px;font-size:.82rem;font-weight:600;display:flex;align-items:center;gap:8px;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px 18px;border-radius:10px;margin-bottom:16px;font-size:.82rem;font-weight:600;display:flex;align-items:center;gap:8px;">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
</div>
@endif
@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px 18px;border-radius:10px;margin-bottom:16px;font-size:.82rem;">
    <div style="font-weight:700;margin-bottom:4px;"><i class="fas fa-exclamation-circle"></i> Erreur de validation</div>
    @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
</div>
@endif

<div class="page-header">
    <div>
        <h1>Utilisateurs <span style="color:var(--green);">inscrits</span></h1>
        <p style="font-size:.82rem;color:var(--muted);margin-top:4px;">{{ $utilisateurs->total() }} membres sur la plateforme.</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        <button onclick="openModal('modal-create')"
            style="display:flex;align-items:center;gap:7px;background:var(--green);color:#fff;border:none;padding:9px 18px;border-radius:50px;font-size:.8rem;font-weight:700;cursor:pointer;transition:all .2s;"
            onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
            <i class="fas fa-user-plus"></i> Ajouter un utilisateur
        </button>
        <a href="{{ route('admin.dashboard') }}" style="display:flex;align-items:center;gap:7px;background:#fff;border:1px solid var(--border);padding:9px 18px;border-radius:50px;font-size:.8rem;font-weight:700;color:var(--muted);text-decoration:none;">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</div>

{{-- STATS RAPIDES --}}
@php
    $total        = $utilisateurs->total();
    $fournisseurs = \App\Models\User::where('role','fournisseur')->count();
    $acheteurs    = \App\Models\User::where('role','acheteur')->count();
    $suspendus    = \App\Models\User::where('statut','suspendu')->count();
@endphp
<div class="stats-row" style="margin-bottom:20px;">
    <div class="stat-card" style="--sc-clr:linear-gradient(90deg,#6366f1,#818cf8);">
        <div class="stat-icon" style="background:#ede9fe;"><i class="fas fa-users" style="color:#6366f1;"></i></div>
        <div><div class="stat-value">{{ $total }}</div><div class="stat-label">Total</div></div>
    </div>
    <div class="stat-card" style="--sc-clr:linear-gradient(90deg,#16a34a,#22c55e);">
        <div class="stat-icon" style="background:#f0fdf4;"><i class="fas fa-store" style="color:#16a34a;"></i></div>
        <div><div class="stat-value">{{ $fournisseurs }}</div><div class="stat-label">Fournisseurs</div></div>
    </div>
    <div class="stat-card" style="--sc-clr:linear-gradient(90deg,#2563eb,#60a5fa);">
        <div class="stat-icon" style="background:#dbeafe;"><i class="fas fa-user-friends" style="color:#2563eb;"></i></div>
        <div><div class="stat-value">{{ $acheteurs }}</div><div class="stat-label">Acheteurs</div></div>
    </div>
    <div class="stat-card" style="--sc-clr:linear-gradient(90deg,#ef4444,#f87171);">
        <div class="stat-icon" style="background:#fee2e2;"><i class="fas fa-ban" style="color:#ef4444;"></i></div>
        <div><div class="stat-value">{{ $suspendus }}</div><div class="stat-label">Suspendus</div></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-users" style="color:#6366f1;font-size:.85rem;"></i> Liste des membres</span>
    </div>
    <table class="dash-table">
        <thead>
            <tr>
                <th>Utilisateur</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Statut</th>
                <th>Inscription</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($utilisateurs as $u)
        <tr>
            <td>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:34px;height:34px;border-radius:50%;flex-shrink:0;
                        background:{{ $u->role==='fournisseur'?'#f0fdf4':($u->role==='admin'?'#1e293b':'#ede9fe') }};
                        display:flex;align-items:center;justify-content:center;
                        font-size:.75rem;font-weight:800;
                        color:{{ $u->role==='fournisseur'?'#16a34a':($u->role==='admin'?'#fff':'#6d28d9') }};
                        border:1px solid {{ $u->role==='fournisseur'?'#bbf7d0':($u->role==='admin'?'rgba(255,255,255,.1)':'#e9d5ff') }};">
                        {{ strtoupper(substr($u->prenom ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-size:.83rem;font-weight:700;color:var(--text);">{{ $u->prenom }} {{ $u->nom }}</div>
                        @if($u->telephone)<div style="font-size:.7rem;color:var(--muted-2);">{{ $u->telephone }}</div>@endif
                    </div>
                </div>
            </td>
            <td style="color:var(--muted);font-size:.79rem;">{{ $u->email }}</td>
            <td>
                @php $rc=['fournisseur'=>'pill-green','acheteur'=>'pill-blue','admin'=>'pill-purple']; @endphp
                <span class="status-pill {{ $rc[$u->role] ?? 'pill-gray' }}">{{ $u->role }}</span>
            </td>
            <td>
                <span class="status-pill {{ $u->statut==='actif'?'pill-green':($u->statut==='suspendu'?'pill-red':'pill-yellow') }}">
                    <span class="pill-dot"></span>{{ $u->statut }}
                </span>
            </td>
            <td style="color:var(--muted);font-size:.74rem;">{{ $u->created_at->format('d/m/Y') }}</td>
            <td>
                <div style="display:flex;gap:5px;flex-wrap:wrap;">
                    {{-- MODIFIER --}}
                    <button onclick="openEditModal({{ $u->id }}, '{{ addslashes($u->nom) }}', '{{ addslashes($u->prenom) }}', '{{ addslashes($u->email) }}', '{{ addslashes($u->telephone ?? '') }}', '{{ $u->role }}', '{{ $u->statut }}')"
                        title="Modifier"
                        style="background:none;border:1px solid #bfdbfe;color:#2563eb;padding:5px 10px;border-radius:8px;font-size:.7rem;cursor:pointer;font-weight:700;transition:all .2s;"
                        onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='none'">
                        <i class="fas fa-pen"></i>
                    </button>

                    {{-- RÉINITIALISER MOT DE PASSE --}}
                    <button onclick="openResetModal({{ $u->id }}, '{{ addslashes($u->prenom) }} {{ addslashes($u->nom) }}')"
                        title="Réinitialiser le mot de passe"
                        style="background:none;border:1px solid #fed7aa;color:#ea580c;padding:5px 10px;border-radius:8px;font-size:.7rem;cursor:pointer;font-weight:700;transition:all .2s;"
                        onmouseover="this.style.background='#fff7ed'" onmouseout="this.style.background='none'">
                        <i class="fas fa-key"></i>
                    </button>

                    {{-- SUSPENDRE / ACTIVER --}}
                    @if($u->statut === 'actif')
                    <form action="{{ route('admin.suspendre', $u) }}" method="POST" style="display:inline;">
                        @csrf
                        <button title="Suspendre"
                            style="background:none;border:1px solid #fecaca;color:#ef4444;padding:5px 10px;border-radius:8px;font-size:.7rem;cursor:pointer;font-weight:700;transition:all .2s;"
                            onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='none'">
                            <i class="fas fa-ban"></i>
                        </button>
                    </form>
                    @else
                    <form action="{{ route('admin.activer', $u) }}" method="POST" style="display:inline;">
                        @csrf
                        <button title="Activer"
                            style="background:none;border:1px solid #bbf7d0;color:#16a34a;padding:5px 10px;border-radius:8px;font-size:.7rem;cursor:pointer;font-weight:700;transition:all .2s;"
                            onmouseover="this.style.background='var(--green-50)'" onmouseout="this.style.background='none'">
                            <i class="fas fa-check"></i>
                        </button>
                    </form>
                    @endif

                    {{-- SUPPRIMER --}}
                    @if($u->id !== Auth::id())
                    <button onclick="openDeleteModal({{ $u->id }}, '{{ addslashes($u->prenom) }} {{ addslashes($u->nom) }}')"
                        title="Supprimer"
                        style="background:none;border:1px solid #fecaca;color:#dc2626;padding:5px 10px;border-radius:8px;font-size:.7rem;cursor:pointer;font-weight:700;transition:all .2s;"
                        onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='none'">
                        <i class="fas fa-trash"></i>
                    </button>
                    @endif
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @if($utilisateurs->hasPages())
    <div style="padding:16px 22px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
        <span style="font-size:.74rem;color:var(--muted);">{{ $utilisateurs->firstItem() }}–{{ $utilisateurs->lastItem() }} sur {{ $utilisateurs->total() }}</span>
        <div style="display:flex;gap:6px;">
            @if($utilisateurs->onFirstPage())
                <span style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:.75rem;color:var(--muted-2);cursor:default;">←</span>
            @else
                <a href="{{ $utilisateurs->previousPageUrl() }}" style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:.75rem;color:var(--text);text-decoration:none;" onmouseover="this.style.borderColor='var(--green)'" onmouseout="this.style.borderColor='var(--border)'">←</a>
            @endif
            @if($utilisateurs->hasMorePages())
                <a href="{{ $utilisateurs->nextPageUrl() }}" style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:.75rem;color:var(--text);text-decoration:none;" onmouseover="this.style.borderColor='var(--green)'" onmouseout="this.style.borderColor='var(--border)'">→</a>
            @else
                <span style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:.75rem;color:var(--muted-2);cursor:default;">→</span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- ═══════════════════════════════════════════
     MODAL — CRÉER UN UTILISATEUR
═══════════════════════════════════════════ --}}
<div id="modal-create" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:28px;width:100%;max-width:480px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,.2);max-height:90vh;overflow-y:auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <div>
                <h3 style="font-size:1rem;font-weight:800;color:var(--text);margin:0;">Ajouter un utilisateur</h3>
                <p style="font-size:.75rem;color:var(--muted);margin:2px 0 0;">Créer un nouveau compte sur la plateforme</p>
            </div>
            <button onclick="closeModal('modal-create')" style="background:#f1f5f9;border:none;width:30px;height:30px;border-radius:50%;cursor:pointer;font-size:.85rem;color:var(--muted);">✕</button>
        </div>
        <form action="{{ route('admin.utilisateurs.store') }}" method="POST">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                <div>
                    <label style="font-size:.75rem;font-weight:700;color:var(--text);display:block;margin-bottom:5px;">Prénom *</label>
                    <input name="prenom" type="text" required placeholder="Prénom"
                        style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:.82rem;box-sizing:border-box;outline:none;font-family:inherit;"
                        onfocus="this.style.borderColor='var(--green)'" onblur="this.style.borderColor='var(--border)'">
                </div>
                <div>
                    <label style="font-size:.75rem;font-weight:700;color:var(--text);display:block;margin-bottom:5px;">Nom *</label>
                    <input name="nom" type="text" required placeholder="Nom"
                        style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:.82rem;box-sizing:border-box;outline:none;font-family:inherit;"
                        onfocus="this.style.borderColor='var(--green)'" onblur="this.style.borderColor='var(--border)'">
                </div>
            </div>
            <div style="margin-bottom:12px;">
                <label style="font-size:.75rem;font-weight:700;color:var(--text);display:block;margin-bottom:5px;">Email *</label>
                <input name="email" type="email" required placeholder="email@exemple.com"
                    style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:.82rem;box-sizing:border-box;outline:none;font-family:inherit;"
                    onfocus="this.style.borderColor='var(--green)'" onblur="this.style.borderColor='var(--border)'">
            </div>
            <div style="margin-bottom:12px;">
                <label style="font-size:.75rem;font-weight:700;color:var(--text);display:block;margin-bottom:5px;">Téléphone</label>
                <input name="telephone" type="text" placeholder="+225 07 00 00 00 00"
                    style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:.82rem;box-sizing:border-box;outline:none;font-family:inherit;"
                    onfocus="this.style.borderColor='var(--green)'" onblur="this.style.borderColor='var(--border)'">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                <div>
                    <label style="font-size:.75rem;font-weight:700;color:var(--text);display:block;margin-bottom:5px;">Rôle *</label>
                    <select name="role" required
                        style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:.82rem;box-sizing:border-box;outline:none;font-family:inherit;background:#fff;">
                        <option value="acheteur">Acheteur</option>
                        <option value="fournisseur">Fournisseur</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:.75rem;font-weight:700;color:var(--text);display:block;margin-bottom:5px;">Mot de passe *</label>
                    <input name="password" type="text" required placeholder="Min. 8 caractères"
                        style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:.82rem;box-sizing:border-box;outline:none;font-family:inherit;"
                        onfocus="this.style.borderColor='var(--green)'" onblur="this.style.borderColor='var(--border)'">
                </div>
            </div>
            <div style="display:flex;gap:10px;margin-top:20px;">
                <button type="submit"
                    style="flex:1;background:var(--green);color:#fff;border:none;padding:11px;border-radius:10px;font-size:.83rem;font-weight:700;cursor:pointer;transition:all .2s;"
                    onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-user-plus"></i> Créer le compte
                </button>
                <button type="button" onclick="closeModal('modal-create')"
                    style="flex:1;background:#f1f5f9;color:var(--muted);border:none;padding:11px;border-radius:10px;font-size:.83rem;font-weight:700;cursor:pointer;">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     MODAL — MODIFIER UN UTILISATEUR
═══════════════════════════════════════════ --}}
<div id="modal-edit" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:28px;width:100%;max-width:480px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,.2);max-height:90vh;overflow-y:auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <div>
                <h3 style="font-size:1rem;font-weight:800;color:var(--text);margin:0;">Modifier l'utilisateur</h3>
                <p style="font-size:.75rem;color:var(--muted);margin:2px 0 0;">Mettre à jour les informations du compte</p>
            </div>
            <button onclick="closeModal('modal-edit')" style="background:#f1f5f9;border:none;width:30px;height:30px;border-radius:50%;cursor:pointer;font-size:.85rem;color:var(--muted);">✕</button>
        </div>
        <form id="edit-form" action="" method="POST">
            @csrf
            @method('PUT')
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                <div>
                    <label style="font-size:.75rem;font-weight:700;color:var(--text);display:block;margin-bottom:5px;">Prénom *</label>
                    <input id="edit-prenom" name="prenom" type="text" required
                        style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:.82rem;box-sizing:border-box;outline:none;font-family:inherit;"
                        onfocus="this.style.borderColor='var(--green)'" onblur="this.style.borderColor='var(--border)'">
                </div>
                <div>
                    <label style="font-size:.75rem;font-weight:700;color:var(--text);display:block;margin-bottom:5px;">Nom *</label>
                    <input id="edit-nom" name="nom" type="text" required
                        style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:.82rem;box-sizing:border-box;outline:none;font-family:inherit;"
                        onfocus="this.style.borderColor='var(--green)'" onblur="this.style.borderColor='var(--border)'">
                </div>
            </div>
            <div style="margin-bottom:12px;">
                <label style="font-size:.75rem;font-weight:700;color:var(--text);display:block;margin-bottom:5px;">Email *</label>
                <input id="edit-email" name="email" type="email" required
                    style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:.82rem;box-sizing:border-box;outline:none;font-family:inherit;"
                    onfocus="this.style.borderColor='var(--green)'" onblur="this.style.borderColor='var(--border)'">
            </div>
            <div style="margin-bottom:12px;">
                <label style="font-size:.75rem;font-weight:700;color:var(--text);display:block;margin-bottom:5px;">Téléphone</label>
                <input id="edit-tel" name="telephone" type="text"
                    style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:.82rem;box-sizing:border-box;outline:none;font-family:inherit;"
                    onfocus="this.style.borderColor='var(--green)'" onblur="this.style.borderColor='var(--border)'">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                <div>
                    <label style="font-size:.75rem;font-weight:700;color:var(--text);display:block;margin-bottom:5px;">Rôle *</label>
                    <select id="edit-role" name="role" required
                        style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:.82rem;box-sizing:border-box;outline:none;font-family:inherit;background:#fff;">
                        <option value="acheteur">Acheteur</option>
                        <option value="fournisseur">Fournisseur</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:.75rem;font-weight:700;color:var(--text);display:block;margin-bottom:5px;">Statut *</label>
                    <select id="edit-statut" name="statut" required
                        style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:.82rem;box-sizing:border-box;outline:none;font-family:inherit;background:#fff;">
                        <option value="actif">Actif</option>
                        <option value="suspendu">Suspendu</option>
                        <option value="en_attente">En attente</option>
                    </select>
                </div>
            </div>
            <div style="display:flex;gap:10px;margin-top:20px;">
                <button type="submit"
                    style="flex:1;background:#2563eb;color:#fff;border:none;padding:11px;border-radius:10px;font-size:.83rem;font-weight:700;cursor:pointer;transition:all .2s;"
                    onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
                <button type="button" onclick="closeModal('modal-edit')"
                    style="flex:1;background:#f1f5f9;color:var(--muted);border:none;padding:11px;border-radius:10px;font-size:.83rem;font-weight:700;cursor:pointer;">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     MODAL — RÉINITIALISER MOT DE PASSE
═══════════════════════════════════════════ --}}
<div id="modal-reset" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:28px;width:100%;max-width:420px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,.2);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <div>
                <h3 style="font-size:1rem;font-weight:800;color:var(--text);margin:0;">Réinitialiser le mot de passe</h3>
                <p id="reset-subtitle" style="font-size:.75rem;color:var(--muted);margin:2px 0 0;"></p>
            </div>
            <button onclick="closeModal('modal-reset')" style="background:#f1f5f9;border:none;width:30px;height:30px;border-radius:50%;cursor:pointer;font-size:.85rem;color:var(--muted);">✕</button>
        </div>
        <form id="reset-form" action="" method="POST">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="font-size:.75rem;font-weight:700;color:var(--text);display:block;margin-bottom:5px;">Nouveau mot de passe *</label>
                <div style="position:relative;">
                    <input id="reset-password" name="password" type="text" required placeholder="Min. 8 caractères"
                        style="width:100%;padding:9px 40px 9px 12px;border:1px solid var(--border);border-radius:8px;font-size:.82rem;box-sizing:border-box;outline:none;font-family:inherit;"
                        onfocus="this.style.borderColor='#ea580c'" onblur="this.style.borderColor='var(--border)'">
                    <button type="button" onclick="generatePassword()"
                        style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#ea580c;font-size:.75rem;font-weight:700;padding:2px 4px;"
                        title="Générer automatiquement">
                        <i class="fas fa-dice"></i>
                    </button>
                </div>
                <p style="font-size:.7rem;color:var(--muted);margin-top:4px;">Cliquez sur <i class="fas fa-dice" style="color:#ea580c;"></i> pour générer un mot de passe automatique</p>
            </div>
            <div style="display:flex;gap:10px;margin-top:20px;">
                <button type="submit"
                    style="flex:1;background:#ea580c;color:#fff;border:none;padding:11px;border-radius:10px;font-size:.83rem;font-weight:700;cursor:pointer;transition:all .2s;"
                    onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-key"></i> Réinitialiser
                </button>
                <button type="button" onclick="closeModal('modal-reset')"
                    style="flex:1;background:#f1f5f9;color:var(--muted);border:none;padding:11px;border-radius:10px;font-size:.83rem;font-weight:700;cursor:pointer;">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     MODAL — SUPPRIMER UN UTILISATEUR
═══════════════════════════════════════════ --}}
<div id="modal-delete" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:28px;width:100%;max-width:400px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,.2);">
        <div style="text-align:center;margin-bottom:20px;">
            <div style="width:56px;height:56px;background:#fef2f2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                <i class="fas fa-trash" style="color:#dc2626;font-size:1.3rem;"></i>
            </div>
            <h3 style="font-size:1rem;font-weight:800;color:var(--text);margin:0 0 6px;">Confirmer la suppression</h3>
            <p style="font-size:.82rem;color:var(--muted);margin:0;">Supprimer définitivement le compte de <strong id="delete-name" style="color:var(--text);"></strong> ?</p>
            <p style="font-size:.75rem;color:#dc2626;margin-top:8px;">Cette action est irréversible.</p>
        </div>
        <form id="delete-form" action="" method="POST">
            @csrf
            @method('DELETE')
            <div style="display:flex;gap:10px;">
                <button type="submit"
                    style="flex:1;background:#dc2626;color:#fff;border:none;padding:11px;border-radius:10px;font-size:.83rem;font-weight:700;cursor:pointer;transition:all .2s;"
                    onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
                <button type="button" onclick="closeModal('modal-delete')"
                    style="flex:1;background:#f1f5f9;color:var(--muted);border:none;padding:11px;border-radius:10px;font-size:.83rem;font-weight:700;cursor:pointer;">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
    document.body.style.overflow = '';
}
// Fermer en cliquant à l'extérieur
['modal-create','modal-edit','modal-reset','modal-delete'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) closeModal(id);
    });
});

function openEditModal(id, nom, prenom, email, tel, role, statut) {
    document.getElementById('edit-nom').value    = nom;
    document.getElementById('edit-prenom').value = prenom;
    document.getElementById('edit-email').value  = email;
    document.getElementById('edit-tel').value    = tel;
    document.getElementById('edit-role').value   = role;
    document.getElementById('edit-statut').value = statut;
    document.getElementById('edit-form').action  = '/admin/utilisateurs/' + id;
    openModal('modal-edit');
}

function openResetModal(id, name) {
    document.getElementById('reset-subtitle').textContent = 'Compte : ' + name;
    document.getElementById('reset-form').action = '/admin/utilisateurs/' + id + '/reset-password';
    document.getElementById('reset-password').value = '';
    openModal('modal-reset');
}

function openDeleteModal(id, name) {
    document.getElementById('delete-name').textContent = name;
    document.getElementById('delete-form').action = '/admin/utilisateurs/' + id;
    openModal('modal-delete');
}

function generatePassword() {
    const chars = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789@#!';
    let pwd = '';
    for (let i = 0; i < 12; i++) pwd += chars[Math.floor(Math.random() * chars.length)];
    document.getElementById('reset-password').value = pwd;
}
</script>

@endsection

@section('right-panel')
<div class="profile-card">
    <div class="profile-avatar" style="background:linear-gradient(135deg,#6366f1,#818cf8);">🛡️</div>
    <div class="profile-name">{{ Auth::user()->prenom ?? 'Admin' }} {{ Auth::user()->nom ?? '' }}</div>
    <div class="profile-role-badge">Administrateur</div>
    <div class="profile-actions">
        <a href="{{ route('admin.utilisateurs') }}" class="profile-btn" title="Utilisateurs"><i class="fas fa-users"></i></a>
        <a href="{{ route('admin.signalements') }}" class="profile-btn" title="Signalements"><i class="fas fa-flag"></i></a>
        <a href="{{ route('admin.annonces') }}" class="profile-btn" title="Annonces"><i class="fas fa-bullhorn"></i></a>
    </div>
</div>
<div class="activity-card">
    <div class="activity-title">Résumé des rôles</div>
    @foreach([['fournisseur','#f0fdf4','#16a34a','#bbf7d0'],['acheteur','#dbeafe','#1d4ed8','#bfdbfe'],['admin','#ede9fe','#6d28d9','#e9d5ff']] as $r)
    <div class="activity-item">
        <div class="activity-avatar" style="background:{{ $r[1] }};color:{{ $r[2] }};border-color:{{ $r[3] }};">
            {{ strtoupper(substr($r[0],0,1)) }}
        </div>
        <div class="activity-text">
            <strong>{{ ucfirst($r[0]).'s' }}</strong>
            <div class="activity-time">{{ \App\Models\User::where('role',$r[0])->count() }} membres</div>
        </div>
    </div>
    @endforeach
</div>
@endsection

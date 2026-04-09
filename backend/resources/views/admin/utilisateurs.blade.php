@extends('layouts.app')
@section('title', 'Gestion des utilisateurs')
@section('content')
<div class="container-fluid bg-dark text-white py-3 mb-4">
    <div class="container">
        <h5 class="mb-0"><i class="fas fa-users me-2 text-warning"></i>Gestion des utilisateurs</h5>
    </div>
</div>
<div class="container pb-5">
    @if(session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white p-4 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold">{{ $utilisateurs->total() }} utilisateurs</h6>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Utilisateur</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Statut</th>
                            <th>Inscription</th>
                            <th class="pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($utilisateurs as $u)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width:36px;height:36px;flex-shrink:0;">
                                        <span class="text-white small fw-bold">{{ strtoupper(substr($u->prenom,0,1)) }}</span>
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">{{ $u->prenom }} {{ $u->nom }}</div>
                                        @if($u->telephone)<div class="text-muted" style="font-size:11px;">{{ $u->telephone }}</div>@endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 small">{{ $u->email }}</td>
                            <td class="py-3">
                                <span class="badge bg-{{ $u->role==='fournisseur'?'success':($u->role==='admin'?'danger':'primary') }} rounded-pill">
                                    {{ $u->role }}
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="badge bg-{{ $u->statut==='actif'?'success':($u->statut==='suspendu'?'danger':'warning') }} rounded-pill">
                                    {{ $u->statut }}
                                </span>
                            </td>
                            <td class="py-3 small text-muted">{{ $u->created_at->format('d/m/Y') }}</td>
                            <td class="py-3 pe-4">
                                @if($u->role !== 'admin')
                                    @if($u->statut === 'actif')
                                        <form action="{{ route('admin.suspendre', $u) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger rounded-pill px-2">Suspendre</button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.activer', $u) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success rounded-pill px-2">Activer</button>
                                        </form>
                                    @endif
                                @else
                                    <span class="text-muted small">Admin</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white p-3">
            {{ $utilisateurs->links() }}
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')
@section('title', 'Gestion des annonces')
@section('content')
<div class="container-fluid bg-dark text-white py-3 mb-4">
    <div class="container">
        <h5 class="mb-0"><i class="fas fa-bullhorn me-2 text-warning"></i>Gestion des annonces</h5>
    </div>
</div>
<div class="container pb-5">
    @if(session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white p-4 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold">{{ $annonces->total() }} annonces</h6>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Annonce</th>
                            <th>Fournisseur</th>
                            <th>Catégorie</th>
                            <th>Type</th>
                            <th>Statut</th>
                            <th>Vues</th>
                            <th class="pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($annonces as $annonce)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="fw-semibold small">{{ Str::limit($annonce->titre, 40) }}</div>
                                <div class="text-muted" style="font-size:11px;">{{ $annonce->created_at->format('d/m/Y') }}</div>
                            </td>
                            <td class="py-3 small">{{ $annonce->user->prenom ?? '—' }} {{ $annonce->user->nom ?? '' }}</td>
                            <td class="py-3 small">{{ $annonce->categorie->nom ?? '—' }}</td>
                            <td class="py-3">
                                <span class="badge bg-info rounded-pill">{{ $annonce->type_offre }}</span>
                            </td>
                            <td class="py-3">
                                @php $sc = ['disponible'=>'success','reservé'=>'warning','expiré'=>'secondary','supprimé'=>'danger']; @endphp
                                <span class="badge bg-{{ $sc[$annonce->statut] ?? 'secondary' }} rounded-pill">{{ $annonce->statut }}</span>
                            </td>
                            <td class="py-3 small text-muted">{{ $annonce->vues }}</td>
                            <td class="py-3 pe-4">
                                <div class="d-flex gap-1">
                                    <a href="{{ route('annonces.show', $annonce) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($annonce->statut !== 'supprimé')
                                    <form action="{{ route('admin.supprimer-annonce', $annonce) }}" method="POST" onsubmit="return confirm('Supprimer cette annonce ?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger rounded-pill px-2">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white p-3">
            {{ $annonces->links() }}
        </div>
    </div>
</div>
@endsection

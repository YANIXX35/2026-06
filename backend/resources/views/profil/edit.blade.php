@extends('layouts.app')
@section('title', 'Modifier mon profil')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-primary text-white rounded-top-3 p-4">
                    <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Modifier mon profil</h5>
                </div>
                <div class="card-body p-5">
                    @if(session('success'))<div class="alert alert-success rounded-3">{{ session('success') }}</div>@endif
                    <form action="{{ route('profil.update') }}" method="POST">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nom</label>
                                <input type="text" name="nom" class="form-control" value="{{ old('nom', $user->nom) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Prénom</label>
                                <input type="text" name="prenom" class="form-control" value="{{ old('prenom', $user->prenom) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Téléphone</label>
                                <input type="tel" name="telephone" class="form-control" value="{{ old('telephone', $user->telephone) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Adresse</label>
                                <input type="text" name="adresse" class="form-control" value="{{ old('adresse', $user->adresse) }}">
                            </div>
                            @if($user->role === 'fournisseur')
                            <div class="col-12">
                                <label class="form-label fw-semibold">Nom de la structure</label>
                                <input type="text" name="nom_structure" class="form-control" value="{{ old('nom_structure', $user->nom_structure) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea name="description_structure" class="form-control" rows="3">{{ old('description_structure', $user->description_structure) }}</textarea>
                            </div>
                            @endif
                            <hr>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nouveau mot de passe <small class="text-muted">(laisser vide pour ne pas changer)</small></label>
                                <input type="password" name="password" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirmer le mot de passe</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        </div>
                        <div class="mt-4 d-flex gap-3">
                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2">
                                <i class="fas fa-save me-2"></i>Enregistrer
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-pill px-5 py-2">Retour</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

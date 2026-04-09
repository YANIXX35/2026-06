@extends('layouts.app')
@section('title', 'Inscription')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="text-primary fw-bold"><i class="fas fa-leaf me-2 text-success"></i>AntiGaspiCI</h2>
                        <h5 class="text-muted">Créer votre compte gratuit</h5>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger rounded-3">
                            @foreach($errors->all() as $err)<div><i class="fas fa-times-circle me-1"></i>{{ $err }}</div>@endforeach
                        </div>
                    @endif

                    <form action="{{ route('inscrire') }}" method="POST">
                        @csrf

                        <!-- Type de compte -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Je suis un(e) *</label>
                            <div class="row g-3">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="role" id="role_acheteur" value="acheteur" {{ old('role','acheteur') === 'acheteur' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary w-100 py-3" for="role_acheteur">
                                        <i class="fas fa-shopping-basket d-block fs-3 mb-2"></i>
                                        <strong>Acheteur / Bénéficiaire</strong>
                                        <small class="d-block text-muted">Particulier, association...</small>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="role" id="role_fournisseur" value="fournisseur" {{ old('role') === 'fournisseur' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-success w-100 py-3" for="role_fournisseur">
                                        <i class="fas fa-store d-block fs-3 mb-2"></i>
                                        <strong>Fournisseur</strong>
                                        <small class="d-block text-muted">Restaurant, marché, artisan...</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nom *</label>
                                <input type="text" name="nom" class="form-control" value="{{ old('nom') }}" placeholder="KONAN" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Prénom *</label>
                                <input type="text" name="prenom" class="form-control" value="{{ old('prenom') }}" placeholder="Yao" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email *</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="email@exemple.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Téléphone</label>
                                <input type="tel" name="telephone" class="form-control" value="{{ old('telephone') }}" placeholder="+225 07 00 00 00 00">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Mot de passe *</label>
                                <input type="password" name="password" class="form-control" placeholder="Min. 8 caractères" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirmer le mot de passe *</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Répétez le mot de passe" required>
                            </div>
                        </div>

                        <!-- Champs fournisseur -->
                        <div id="champs-fournisseur" class="mt-3" style="{{ old('role') === 'fournisseur' ? '' : 'display:none;' }}">
                            <hr>
                            <h6 class="fw-semibold text-success mb-3"><i class="fas fa-store me-2"></i>Informations de votre structure</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Type de structure</label>
                                    <select name="type_structure" class="form-select">
                                        <option value="restaurant">🍽️ Restaurant</option>
                                        <option value="marché">🛒 Marché / Commerce</option>
                                        <option value="transformateur">🏭 Transformateur</option>
                                        <option value="artisan">👨‍🍳 Artisan</option>
                                        <option value="éleveur">🐄 Éleveur</option>
                                        <option value="autre">Autre</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nom de la structure</label>
                                    <input type="text" name="nom_structure" class="form-control" value="{{ old('nom_structure') }}" placeholder="Ex: Restaurant Chez Mamie">
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" required id="cgu">
                                <label class="form-check-label text-muted" for="cgu">
                                    J'accepte les <a href="#" class="text-primary">conditions d'utilisation</a> et la <a href="#" class="text-primary">politique de confidentialité</a>
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-semibold">
                                <i class="fas fa-user-plus me-2"></i>Créer mon compte gratuitement
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">
                    <p class="text-center text-muted mb-0">
                        Déjà un compte ?
                        <a href="{{ route('connexion') }}" class="text-primary fw-semibold">Se connecter</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('input[name="role"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('champs-fournisseur').style.display = this.value === 'fournisseur' ? 'block' : 'none';
    });
});
</script>
@endpush
@endsection

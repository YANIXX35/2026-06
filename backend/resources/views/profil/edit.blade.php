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
                    <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')

                        {{-- Photo de profil --}}
                        <div class="text-center mb-4">
                            @if($user->photo)
                                <img src="{{ asset('storage/'.$user->photo) }}" alt="Photo de profil"
                                     style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #e5e7eb;margin-bottom:10px;display:block;margin-left:auto;margin-right:auto;">
                            @else
                                <div style="width:80px;height:80px;border-radius:50%;background:#f0fdf4;display:flex;align-items:center;justify-content:center;font-size:1.8rem;margin:0 auto 10px;border:3px solid #e5e7eb;">
                                    {{ mb_strtoupper(mb_substr($user->prenom, 0, 1)) }}
                                </div>
                            @endif
                            <label class="btn btn-sm btn-outline-secondary rounded-pill">
                                <i class="fas fa-camera me-1"></i>Changer la photo
                                <input type="file" name="photo" accept="image/*" style="display:none"
                                       onchange="previewPhoto(this)">
                            </label>
                            <div style="font-size:.7rem;color:#9ca3af;margin-top:4px;">JPG, PNG, WEBP — max 2 Mo</div>
                        </div>

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
@push('scripts')
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const el = input.closest('.text-center').querySelector('img, div[style*="border-radius:50%"]');
            if (el && el.tagName === 'IMG') {
                el.src = e.target.result;
            } else if (el) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.cssText = 'width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #e5e7eb;margin-bottom:10px;display:block;margin-left:auto;margin-right:auto;';
                el.replaceWith(img);
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection

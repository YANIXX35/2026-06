@extends('layouts.app')
@section('title', 'Connexion')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="text-primary fw-bold"><i class="fas fa-leaf me-2 text-success"></i>AntiGaspiCI</h2>
                        <h5 class="text-muted">Connexion à votre compte</h5>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger rounded-3">
                            @foreach($errors->all() as $err)<div><i class="fas fa-times-circle me-1"></i>{{ $err }}</div>@endforeach
                        </div>
                    @endif

                    <form action="{{ route('connecter') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Adresse email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-primary"></i></span>
                                <input type="email" name="email" class="form-control border-start-0" value="{{ old('email') }}" placeholder="email@exemple.com" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-primary"></i></span>
                                <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label text-muted" for="remember">Se souvenir de moi</label>
                            </div>
                            <a href="{{ route('password.email.form') }}" class="text-primary small">Mot de passe oublié ?</a>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-semibold">
                            <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                        </button>
                    </form>

                    <hr class="my-4">
                    <p class="text-center text-muted mb-0">
                        Pas encore de compte ?
                        <a href="{{ route('inscription') }}" class="text-primary fw-semibold">S'inscrire gratuitement</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

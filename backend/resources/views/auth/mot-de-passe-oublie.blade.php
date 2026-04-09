@extends('layouts.app')
@section('title', 'Mot de passe oublié')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-5">

                    <div class="text-center mb-4">
                        <div style="width:64px;height:64px;background:#f0fdf4;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                            <i class="fas fa-lock" style="font-size:1.5rem;color:#16a34a;"></i>
                        </div>
                        <h2 class="fw-bold" style="color:#1a1a2e;">Mot de passe oublié</h2>
                        <p class="text-muted" style="font-size:.9rem;">Saisissez votre adresse email. Nous vous enverrons un code à 6 chiffres.</p>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success rounded-3" style="font-size:.85rem;">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger rounded-3" style="font-size:.85rem;">
                            @foreach($errors->all() as $err)
                                <div><i class="fas fa-times-circle me-1"></i>{{ $err }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('password.otp.send') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Adresse email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-envelope text-primary"></i>
                                </span>
                                <input type="email" name="email" class="form-control border-start-0"
                                    value="{{ old('email') }}"
                                    placeholder="email@exemple.com" required autofocus>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-3 rounded-pill fw-semibold">
                            <i class="fas fa-paper-plane me-2"></i>Envoyer le code OTP
                        </button>
                    </form>

                    <hr class="my-4">
                    <p class="text-center text-muted mb-0" style="font-size:.85rem;">
                        <a href="{{ route('connexion') }}" class="text-primary fw-semibold">
                            <i class="fas fa-arrow-left me-1"></i>Retour à la connexion
                        </a>
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')
@section('title', 'Vérification du code OTP')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-5">

                    <div class="text-center mb-4">
                        <div style="width:64px;height:64px;background:#fef9c3;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                            <i class="fas fa-shield-alt" style="font-size:1.5rem;color:#d97706;"></i>
                        </div>
                        <h2 class="fw-bold" style="color:#1a1a2e;">Vérification du code</h2>
                        <p class="text-muted" style="font-size:.9rem;">
                            Un code à 6 chiffres a été envoyé à<br>
                            <strong style="color:#16a34a;">{{ session('otp_email') }}</strong>
                        </p>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success rounded-3" style="font-size:.85rem;">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning rounded-3" style="font-size:.85rem;">
                            {!! session('warning') !!}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger rounded-3" style="font-size:.85rem;">
                            @foreach($errors->all() as $err)
                                <div><i class="fas fa-times-circle me-1"></i>{{ $err }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('password.otp.verify') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Code OTP</label>
                            <input type="text" name="otp"
                                class="form-control text-center fw-bold"
                                style="font-size:2rem;letter-spacing:12px;border-radius:12px;padding:14px;"
                                maxlength="6"
                                placeholder="_ _ _ _ _ _"
                                inputmode="numeric"
                                autocomplete="one-time-code"
                                required autofocus>
                            <div class="text-muted text-center mt-2" style="font-size:.78rem;">
                                <i class="fas fa-clock me-1"></i>Ce code est valable 10 minutes
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 py-3 rounded-pill fw-semibold text-dark">
                            <i class="fas fa-check me-2"></i>Valider le code
                        </button>
                    </form>

                    <hr class="my-4">
                    <p class="text-center text-muted mb-0" style="font-size:.85rem;">
                        Vous n'avez pas reçu de code ?
                        <a href="{{ route('password.email.form') }}" class="text-primary fw-semibold">Réessayer</a>
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Formatage automatique : ne garder que les chiffres
    document.querySelector('input[name="otp"]').addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
    });
</script>
@endpush
@endsection

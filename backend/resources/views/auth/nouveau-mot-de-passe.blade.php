@extends('layouts.app')
@section('title', 'Nouveau mot de passe')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-5">

                    <div class="text-center mb-4">
                        <div style="width:64px;height:64px;background:#f0fdf4;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                            <i class="fas fa-key" style="font-size:1.5rem;color:#16a34a;"></i>
                        </div>
                        <h2 class="fw-bold" style="color:#1a1a2e;">Nouveau mot de passe</h2>
                        <p class="text-muted" style="font-size:.9rem;">Choisissez un mot de passe sécurisé d'au moins 8 caractères.</p>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger rounded-3" style="font-size:.85rem;">
                            @foreach($errors->all() as $err)
                                <div><i class="fas fa-times-circle me-1"></i>{{ $err }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('password.reset') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nouveau mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-lock text-primary"></i>
                                </span>
                                <input type="password" name="password" id="password"
                                    class="form-control border-start-0 border-end-0"
                                    placeholder="••••••••" required>
                                <button type="button" class="input-group-text bg-light border-start-0"
                                    onclick="togglePassword('password', this)">
                                    <i class="fas fa-eye text-muted"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Confirmer le mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-lock text-primary"></i>
                                </span>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control border-start-0 border-end-0"
                                    placeholder="••••••••" required>
                                <button type="button" class="input-group-text bg-light border-start-0"
                                    onclick="togglePassword('password_confirmation', this)">
                                    <i class="fas fa-eye text-muted"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Indicateur de force --}}
                        <div class="mb-4">
                            <div style="height:4px;background:#e5e7eb;border-radius:4px;overflow:hidden;">
                                <div id="strength-bar" style="height:100%;width:0%;transition:width .3s,background .3s;border-radius:4px;"></div>
                            </div>
                            <div id="strength-text" class="text-muted mt-1" style="font-size:.75rem;"></div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-3 rounded-pill fw-semibold">
                            <i class="fas fa-save me-2"></i>Enregistrer le nouveau mot de passe
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePassword(id, btn) {
        const input = document.getElementById(id);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    document.getElementById('password').addEventListener('input', function () {
        const val = this.value;
        const bar = document.getElementById('strength-bar');
        const txt = document.getElementById('strength-text');
        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const levels = [
            { w: '0%',   c: '#e5e7eb', t: '' },
            { w: '25%',  c: '#ef4444', t: 'Très faible' },
            { w: '50%',  c: '#f97316', t: 'Faible' },
            { w: '75%',  c: '#eab308', t: 'Moyen' },
            { w: '100%', c: '#16a34a', t: 'Fort' },
        ];
        bar.style.width      = levels[score].w;
        bar.style.background = levels[score].c;
        txt.textContent      = levels[score].t;
        txt.style.color      = levels[score].c;
    });
</script>
@endpush
@endsection

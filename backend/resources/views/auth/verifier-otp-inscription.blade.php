@extends('layouts.front')
@section('title', 'Confirmez votre email')

@push('styles')
<style>
.auth-page{min-height:100vh;display:flex;align-items:center;justify-content:center;
    background:var(--surface);padding:90px 20px 40px;}
</style>
@endpush

@section('content')
<div class="auth-page">
    <div style="width:100%;max-width:460px;">
        <div style="background:#fff;border-radius:24px;overflow:hidden;
            box-shadow:0 24px 80px rgba(0,0,0,.1);border:1px solid var(--border);padding:44px;">

            <div style="text-align:center;margin-bottom:28px;">
                <div style="width:68px;height:68px;background:#f0fdf4;border-radius:50%;
                    display:flex;align-items:center;justify-content:center;margin:0 auto 16px;
                    border:2px solid #bbf7d0;">
                    <span style="font-size:1.8rem;">📧</span>
                </div>
                <h2 style="font-family:'Rubik',sans-serif;font-size:1.5rem;font-weight:900;
                    color:var(--text);margin:0 0 8px;">Confirmez votre email</h2>
                <p style="font-size:.875rem;color:var(--muted);margin:0;line-height:1.6;">
                    Un code à 6 chiffres a été envoyé à<br>
                    <strong style="color:#16a34a;">{{ session('inscription_email') }}</strong>
                </p>
            </div>

            @if(session('success'))
                <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;
                    border-radius:12px;padding:12px 16px;margin-bottom:16px;font-size:.83rem;font-weight:600;">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div style="background:#fffbeb;border:1px solid #fde68a;color:#92400e;
                    border-radius:12px;padding:12px 16px;margin-bottom:16px;font-size:.83rem;font-weight:600;">
                    {!! session('warning') !!}
                </div>
            @endif

            @if($errors->any())
                <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;
                    border-radius:12px;padding:12px 16px;margin-bottom:16px;font-size:.83rem;font-weight:600;">
                    @foreach($errors->all() as $err)
                        <div><i class="fas fa-times-circle me-1"></i>{{ $err }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('inscription.otp.verify') }}" method="POST">
                @csrf
                <div style="margin-bottom:24px;">
                    <label style="display:block;font-size:.72rem;font-weight:800;color:var(--text);
                        letter-spacing:.2px;margin-bottom:8px;text-transform:uppercase;">
                        Code de confirmation
                    </label>
                    <input type="text" name="otp"
                        style="width:100%;border:2px solid var(--border);border-radius:14px;
                        padding:16px;font-size:2rem;font-weight:800;letter-spacing:14px;
                        text-align:center;font-family:'Rubik',sans-serif;outline:none;
                        transition:border-color .2s;box-sizing:border-box;"
                        onfocus="this.style.borderColor='#16a34a'"
                        onblur="this.style.borderColor='var(--border)'"
                        maxlength="6"
                        placeholder="_ _ _ _ _ _"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        required autofocus>
                    <p style="font-size:.75rem;color:var(--muted);text-align:center;margin:8px 0 0;">
                        <i class="fas fa-clock me-1"></i>Ce code est valable 10 minutes
                    </p>
                </div>

                <button type="submit"
                    style="width:100%;background:#16a34a;color:#fff;border:none;padding:14px;
                    border-radius:50px;font-family:'Rubik',sans-serif;font-weight:800;font-size:.92rem;
                    cursor:pointer;transition:all .25s;display:flex;align-items:center;justify-content:center;
                    gap:8px;box-shadow:0 4px 18px rgba(22,163,74,.35);">
                    <i class="fas fa-check"></i> Confirmer et créer mon compte
                </button>
            </form>

            <hr style="margin:24px 0;border:none;border-top:1px solid var(--border);">
            <p style="text-align:center;font-size:.82rem;color:var(--muted);margin:0;">
                Vous n'avez pas reçu de code ?
                <a href="{{ route('inscription') }}" style="color:#16a34a;font-weight:700;text-decoration:none;">
                    Recommencer l'inscription
                </a>
            </p>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelector('input[name="otp"]').addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
    });
</script>
@endpush

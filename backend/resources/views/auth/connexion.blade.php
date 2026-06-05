@extends('layouts.front')
@section('title','Connexion')
@section('description','Connectez-vous à votre compte AntiGaspiCI.')

@push('styles')
<style>
.auth-page{min-height:100vh;display:flex;align-items:center;justify-content:center;
    background:var(--surface);padding:90px 20px 40px;}
.auth-wrap{width:100%;max-width:960px;display:grid;grid-template-columns:1fr 1fr;
    background:#fff;border-radius:24px;overflow:hidden;
    box-shadow:0 24px 80px rgba(0,0,0,.1);border:1px solid var(--border);}

/* ── LEFT PANEL ── */
.auth-form-panel{padding:48px 44px;display:flex;flex-direction:column;justify-content:center;}
.auth-logo{display:flex;align-items:center;gap:9px;text-decoration:none;margin-bottom:36px;}
.auth-logo-ico{width:32px;height:32px;border-radius:9px;
    background:linear-gradient(135deg,var(--green),var(--green-light));
    display:flex;align-items:center;justify-content:center;font-size:.95rem;}
.auth-logo-txt{font-family:'Rubik',sans-serif;font-weight:800;font-size:1.05rem;color:var(--text);}
.auth-logo-txt span{color:var(--green);}

.auth-form-panel h1{font-family:'Rubik',sans-serif;font-size:1.75rem;font-weight:900;
    color:var(--text);letter-spacing:-.5px;margin-bottom:5px;}
.auth-form-panel .auth-sub{font-size:.875rem;color:var(--muted);margin-bottom:28px;line-height:1.55;}
.auth-form-panel .auth-sub strong{color:var(--green);}

.auth-error{background:#fef2f2;border:1px solid #fecaca;color:#dc2626;
    border-radius:12px;padding:12px 16px;margin-bottom:18px;font-size:.82rem;font-weight:600;}
.auth-error div{display:flex;align-items:center;gap:6px;}

.field{margin-bottom:14px;}
.field label{display:block;font-size:.76rem;font-weight:800;color:var(--text);
    letter-spacing:.2px;margin-bottom:5px;text-transform:uppercase;}
.field-inner{position:relative;}
.field-inner i.ico{position:absolute;left:14px;top:50%;transform:translateY(-50%);
    color:var(--muted-2);font-size:.82rem;pointer-events:none;}
.field-inner input{width:100%;border:1.5px solid var(--border);border-radius:12px;
    padding:12px 14px 12px 38px;font-size:.875rem;font-family:'Nunito Sans',sans-serif;
    color:var(--text);background:#fff;transition:all .2s;outline:none;}
.field-inner input:focus{border-color:var(--green);box-shadow:0 0 0 3px rgba(22,163,74,.1);}
.field-inner input.err{border-color:#ef4444;}
.field-inner .eye-btn{position:absolute;right:13px;top:50%;transform:translateY(-50%);
    background:none;border:none;cursor:pointer;color:var(--muted-2);font-size:.82rem;padding:3px;}
.field-inner .eye-btn:hover{color:var(--text);}

.field-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;}
.remember{display:flex;align-items:center;gap:7px;cursor:pointer;}
.remember input{accent-color:var(--green);width:14px;height:14px;}
.remember span{font-size:.8rem;color:var(--muted);}
.forgot{font-size:.8rem;color:var(--green);text-decoration:none;font-weight:600;}
.forgot:hover{text-decoration:underline;}

.btn-submit{width:100%;background:var(--dark);color:#fff;border:none;padding:14px;
    border-radius:50px;font-family:'Rubik',sans-serif;font-weight:800;font-size:.95rem;
    cursor:pointer;transition:all .25s;display:flex;align-items:center;justify-content:center;gap:8px;
    box-shadow:0 4px 14px rgba(0,0,0,.18);}
.btn-submit:hover{background:#1a1a1a;transform:translateY(-1px);box-shadow:0 6px 22px rgba(0,0,0,.24);}

.auth-divider{display:flex;align-items:center;gap:12px;margin:20px 0;}
.auth-divider::before,.auth-divider::after{content:'';flex:1;height:1px;background:var(--border);}
.auth-divider span{font-size:.73rem;color:var(--muted-2);white-space:nowrap;}

.social-row{display:flex;gap:10px;justify-content:center;margin-bottom:22px;}
.btn-soc{flex:1;display:flex;align-items:center;justify-content:center;gap:8px;
    padding:10px 14px;border-radius:50px;border:1.5px solid var(--border);
    font-size:.78rem;font-weight:700;text-decoration:none;color:var(--text);
    transition:all .2s;background:#fff;cursor:pointer;}
.btn-soc:hover{border-color:var(--text);background:var(--surface);color:var(--text);}
.btn-soc.google:hover{border-color:#db4437;color:#db4437;}
.btn-soc.facebook:hover{border-color:#1877f2;color:#1877f2;}
.btn-soc.disabled-soc{opacity:.4;cursor:not-allowed;pointer-events:none;}

.auth-switch{text-align:center;font-size:.82rem;color:var(--muted);}
.auth-switch a{color:var(--green);font-weight:700;text-decoration:none;}
.auth-switch a:hover{text-decoration:underline;}

/* ── RIGHT DECO PANEL ── */
.auth-deco{
    background:linear-gradient(145deg,#052e16 0%,#0d3d1f 55%,#0f5132 100%);
    padding:48px 40px;display:flex;flex-direction:column;
    align-items:center;justify-content:center;text-align:center;
    position:relative;overflow:hidden;
}
.deco-grid{position:absolute;inset:0;
    background-image:linear-gradient(rgba(255,255,255,.025) 1px,transparent 1px),
        linear-gradient(90deg,rgba(255,255,255,.025) 1px,transparent 1px);
    background-size:44px 44px;pointer-events:none;}
.deco-glow{position:absolute;top:-80px;right:-80px;width:280px;height:280px;border-radius:50%;
    background:radial-gradient(circle,rgba(34,197,94,.14) 0%,transparent 70%);pointer-events:none;}
.deco-glow-2{position:absolute;bottom:-60px;left:-60px;width:200px;height:200px;border-radius:50%;
    background:radial-gradient(circle,rgba(249,115,22,.08) 0%,transparent 70%);pointer-events:none;}
.deco-inner{position:relative;z-index:2;}
.deco-illus{font-size:4.5rem;margin-bottom:18px;display:block;
    animation:floatY 3.5s ease-in-out infinite;}
@keyframes floatY{0%,100%{transform:translateY(0);}50%{transform:translateY(-10px);}}
.deco-title{font-family:'Rubik',sans-serif;font-size:1.4rem;font-weight:900;color:#fff;
    line-height:1.3;margin-bottom:8px;}
.deco-title strong{color:#4ade80;}
.deco-sub{font-size:.82rem;color:rgba(255,255,255,.6);line-height:1.68;margin-bottom:28px;max-width:230px;}

.deco-stats{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:24px;}
.ds-card{background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);
    border-radius:14px;padding:14px;text-align:center;}
.ds-num{font-family:'Rubik',sans-serif;font-size:1.3rem;font-weight:900;color:#4ade80;line-height:1;}
.ds-lbl{font-size:.66rem;color:rgba(255,255,255,.5);margin-top:3px;}

.deco-avs{display:flex;justify-content:center;gap:-6px;}
.deco-av{width:32px;height:32px;border-radius:50%;border:2px solid rgba(255,255,255,.2);
    display:flex;align-items:center;justify-content:center;font-size:.95rem;
    background:rgba(255,255,255,.08);margin-left:-6px;}
.deco-av:first-child{margin-left:0;}
.deco-av-txt{font-size:.72rem;color:rgba(255,255,255,.55);margin-left:10px;text-align:left;line-height:1.4;}
.deco-av-row{display:flex;align-items:center;margin-top:14px;}

@media(max-width:768px){
    .auth-wrap{grid-template-columns:1fr;}
    .auth-deco{display:none;}
    .auth-form-panel{padding:32px 24px;}
    .auth-page{padding:70px 16px 32px;}
}
@media(max-width:480px){
    .auth-form-panel{padding:24px 18px;}
    .auth-form-panel h1{font-size:1.5rem;}
    .social-row{flex-direction:column;}
    .btn-soc{justify-content:center;}
    .auth-wrap{border-radius:16px;}
}
@media(max-width:360px){
    .auth-form-panel{padding:20px 14px;}
    .auth-form-panel h1{font-size:1.3rem;}
}
</style>
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-wrap">

        {{-- FORM --}}
        <div class="auth-form-panel">
            <a href="{{ route('home') }}" class="auth-logo">
                <div class="auth-logo-ico">🌿</div>
                <div class="auth-logo-txt"><span>Anti</span>GaspiCI</div>
            </a>

            <h1>Bon retour !</h1>
            <p class="auth-sub">Connectez-vous pour accéder à vos annonces et votre espace <strong>AntiGaspiCI</strong>.</p>

            @if($errors->any())
            <div class="auth-error">
                @foreach($errors->all() as $err)
                <div><i class="fas fa-times-circle"></i>{{ $err }}</div>
                @endforeach
            </div>
            @endif

            @if(session('error'))
            <div class="auth-error"><div><i class="fas fa-times-circle"></i>{{ session('error') }}</div></div>
            @endif

            <div class="social-row">
                <a href="{{ route('social.redirect','google') }}" class="btn-soc google">
                    <svg width="16" height="16" viewBox="0 0 24 24"><path fill="#db4437" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#4285f4" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#fbbc05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#34a853" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                    Google
                </a>
                <a href="{{ route('social.redirect','facebook') }}" class="btn-soc facebook">
                    <i class="fab fa-facebook-f" style="color:#1877f2;font-size:.9rem;"></i>
                    Facebook
                </a>
            </div>

            <div class="auth-divider"><span>ou avec votre email</span></div>

            <form action="{{ route('connecter') }}" method="POST" novalidate>
                @csrf
                <div class="field">
                    <label>Adresse email</label>
                    <div class="field-inner">
                        <i class="fas fa-envelope ico"></i>
                        <input type="email" name="email" value="{{ old('email') }}"
                            placeholder="vous@email.com" required autocomplete="email"
                            class="{{ $errors->has('email') ? 'err' : '' }}">
                    </div>
                </div>
                <div class="field">
                    <label>Mot de passe</label>
                    <div class="field-inner">
                        <i class="fas fa-lock ico"></i>
                        <input type="password" name="password" id="pwdLogin"
                            placeholder="••••••••" required autocomplete="current-password"
                            class="{{ $errors->has('password') ? 'err' : '' }}">
                        <button type="button" class="eye-btn" onclick="togglePwd('pwdLogin','eyeLogin')">
                            <i class="fas fa-eye" id="eyeLogin"></i>
                        </button>
                    </div>
                </div>
                <div class="field-row">
                    <label class="remember">
                        <input type="checkbox" name="remember">
                        <span>Se souvenir de moi</span>
                    </label>
                    <a href="{{ route('password.email.form') }}" class="forgot">Mot de passe oublié ?</a>
                </div>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>

            <p class="auth-switch" style="margin-top:22px;">
                Pas encore membre ? <a href="{{ route('inscription') }}">Créer un compte gratuit</a>
            </p>
        </div>

        {{-- DECO --}}
        <div class="auth-deco">
            <div class="deco-grid"></div>
            <div class="deco-glow"></div>
            <div class="deco-glow-2"></div>
            <div class="deco-inner">
                <span class="deco-illus">🌿</span>
                <div class="deco-title">Réduis le gaspillage,<br><strong>maximise l'impact</strong></div>
                <p class="deco-sub">Des surplus alimentaires transformés en opportunités chaque jour en Côte d'Ivoire.</p>
                <div class="deco-stats">
                    <div class="ds-card">
                        <div class="ds-num">500+</div>
                        <div class="ds-lbl">Membres actifs</div>
                    </div>
                    <div class="ds-card">
                        <div class="ds-num">8.5t</div>
                        <div class="ds-lbl">Aliments sauvés</div>
                    </div>
                    <div class="ds-card">
                        <div class="ds-num">100%</div>
                        <div class="ds-lbl">Gratuit</div>
                    </div>
                    <div class="ds-card">
                        <div class="ds-num">4</div>
                        <div class="ds-lbl">Types d'offres</div>
                    </div>
                </div>
                <div class="deco-av-row">
                    <div class="deco-avs">
                        <div class="deco-av">👨🏿‍🌾</div>
                        <div class="deco-av">👩🏾‍🍳</div>
                        <div class="deco-av">🧑🏽‍💼</div>
                        <div class="deco-av">👩🏿</div>
                    </div>
                    <div class="deco-av-txt">+500 membres<br>déjà actifs</div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePwd(id, eyeId){
    const input = document.getElementById(id);
    const icon  = document.getElementById(eyeId);
    input.type  = input.type === 'password' ? 'text' : 'password';
    icon.className = input.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
}
</script>
@endpush

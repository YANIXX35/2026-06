@extends('layouts.front')
@section('title','Inscription')
@section('description','Créez votre compte AntiGaspiCI gratuitement.')

@push('styles')
<style>
.auth-page{min-height:100vh;display:flex;align-items:center;justify-content:center;
    background:var(--surface);padding:90px 20px 40px;}
.auth-wrap{width:100%;max-width:1080px;display:grid;grid-template-columns:1.1fr 1fr;
    background:#fff;border-radius:24px;overflow:hidden;
    box-shadow:0 24px 80px rgba(0,0,0,.1);border:1px solid var(--border);}

/* ── FORM PANEL ── */
.auth-form-panel{padding:44px 44px;display:flex;flex-direction:column;justify-content:center;}
.auth-logo{display:flex;align-items:center;gap:9px;text-decoration:none;margin-bottom:28px;}
.auth-logo-ico{width:32px;height:32px;border-radius:9px;
    background:linear-gradient(135deg,var(--green),var(--green-light));
    display:flex;align-items:center;justify-content:center;font-size:.95rem;}
.auth-logo-txt{font-family:'Rubik',sans-serif;font-weight:800;font-size:1.05rem;color:var(--text);}
.auth-logo-txt span{color:var(--green);}
.auth-form-panel h1{font-family:'Rubik',sans-serif;font-size:1.6rem;font-weight:900;
    color:var(--text);letter-spacing:-.5px;margin-bottom:4px;}
.auth-sub{font-size:.875rem;color:var(--muted);margin-bottom:24px;line-height:1.55;}
.auth-sub strong{color:var(--green);}

.auth-error{background:#fef2f2;border:1px solid #fecaca;color:#dc2626;
    border-radius:12px;padding:12px 16px;margin-bottom:16px;font-size:.8rem;font-weight:600;}
.auth-error div{display:flex;align-items:center;gap:6px;}

/* ROLE SELECTOR */
.role-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:20px;}
.role-card{position:relative;cursor:pointer;}
.role-card input[type="radio"]{position:absolute;opacity:0;width:0;height:0;}
.role-label{display:flex;flex-direction:column;align-items:center;gap:7px;padding:16px 12px;
    border:1.5px solid var(--border);border-radius:14px;text-align:center;
    transition:all .2s;background:#fff;cursor:pointer;}
.role-label:hover{border-color:var(--green);background:var(--green-50);}
.role-card input:checked + .role-label{border-color:var(--green);background:var(--green-50);
    box-shadow:0 0 0 3px rgba(22,163,74,.15);}
.role-ico{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;
    justify-content:center;font-size:1.3rem;}
.role-nm{font-family:'Rubik',sans-serif;font-size:.82rem;font-weight:800;color:var(--text);}
.role-desc{font-size:.68rem;color:var(--muted);line-height:1.4;}

/* FIELDS */
.fields-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.field{margin-bottom:0;}
.field.full{grid-column:1/-1;}
.field label{display:block;font-size:.72rem;font-weight:800;color:var(--text);
    letter-spacing:.2px;margin-bottom:4px;text-transform:uppercase;}
.field-inner{position:relative;}
.field-inner i.ico{position:absolute;left:13px;top:50%;transform:translateY(-50%);
    color:var(--muted-2);font-size:.8rem;pointer-events:none;}
.field-inner input,.field-inner select{width:100%;border:1.5px solid var(--border);
    border-radius:11px;padding:10px 12px 10px 34px;font-size:.84rem;
    font-family:'Nunito Sans',sans-serif;color:var(--text);background:#fff;
    transition:all .2s;outline:none;}
.field-inner select{padding-left:34px;cursor:pointer;}
.field-inner input:focus,.field-inner select:focus{border-color:var(--green);box-shadow:0 0 0 3px rgba(22,163,74,.1);}
.field-inner input.err{border-color:#ef4444;}
.field-inner .eye-btn{position:absolute;right:11px;top:50%;transform:translateY(-50%);
    background:none;border:none;cursor:pointer;color:var(--muted-2);font-size:.8rem;padding:3px;}

/* FOURNISSEUR SECTION */
.fournisseur-section{background:var(--surface);border-radius:14px;padding:16px;
    margin-top:14px;border:1px solid var(--green-100);display:none;}
.fournisseur-section.show{display:block;}
.fournisseur-section h4{font-family:'Rubik',sans-serif;font-size:.8rem;font-weight:800;
    color:var(--green);margin-bottom:12px;display:flex;align-items:center;gap:7px;}

/* CGU + SUBMIT */
.cgu-row{display:flex;align-items:flex-start;gap:9px;margin:16px 0 14px;cursor:pointer;}
.cgu-row input{accent-color:var(--green);width:15px;height:15px;margin-top:2px;flex-shrink:0;}
.cgu-row span{font-size:.78rem;color:var(--muted);line-height:1.55;}
.cgu-row a{color:var(--green);text-decoration:none;font-weight:600;}
.cgu-row a:hover{text-decoration:underline;}

.btn-submit{width:100%;background:var(--green);color:#fff;border:none;padding:13px;
    border-radius:50px;font-family:'Rubik',sans-serif;font-weight:800;font-size:.92rem;
    cursor:pointer;transition:all .25s;display:flex;align-items:center;justify-content:center;gap:8px;
    box-shadow:0 4px 18px rgba(22,163,74,.35);}
.btn-submit:hover{background:var(--green-dark);transform:translateY(-1px);box-shadow:0 6px 24px rgba(22,163,74,.45);}

.auth-switch{text-align:center;font-size:.82rem;color:var(--muted);margin-top:16px;}
.auth-switch a{color:var(--green);font-weight:700;text-decoration:none;}
.auth-switch a:hover{text-decoration:underline;}

/* ── DECO ── */
.auth-deco{
    background:linear-gradient(145deg,#052e16 0%,#0d3d1f 55%,#0f5132 100%);
    padding:48px 36px;display:flex;flex-direction:column;
    align-items:center;justify-content:center;text-align:center;
    position:relative;overflow:hidden;
}
.deco-grid{position:absolute;inset:0;
    background-image:linear-gradient(rgba(255,255,255,.025) 1px,transparent 1px),
        linear-gradient(90deg,rgba(255,255,255,.025) 1px,transparent 1px);
    background-size:44px 44px;pointer-events:none;}
.deco-glow{position:absolute;top:-80px;right:-80px;width:260px;height:260px;border-radius:50%;
    background:radial-gradient(circle,rgba(34,197,94,.14) 0%,transparent 70%);pointer-events:none;}
.deco-inner{position:relative;z-index:2;}
.deco-illus{font-size:4rem;margin-bottom:16px;display:block;
    animation:floatY 3.5s ease-in-out infinite;}
@keyframes floatY{0%,100%{transform:translateY(0);}50%{transform:translateY(-10px);}}
.deco-title{font-family:'Rubik',sans-serif;font-size:1.3rem;font-weight:900;color:#fff;
    line-height:1.3;margin-bottom:8px;}
.deco-title strong{color:#4ade80;}
.deco-sub{font-size:.8rem;color:rgba(255,255,255,.6);line-height:1.68;margin-bottom:24px;max-width:220px;}

.deco-steps{display:flex;flex-direction:column;gap:10px;width:100%;max-width:220px;}
.deco-step{display:flex;align-items:center;gap:12px;background:rgba(255,255,255,.07);
    border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:11px 14px;text-align:left;}
.dss-num{width:26px;height:26px;border-radius:50%;background:var(--green);
    color:#fff;font-family:'Rubik',sans-serif;font-size:.72rem;font-weight:900;
    display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.dss-txt{font-size:.75rem;font-weight:700;color:#fff;}
.dss-sub{font-size:.65rem;color:rgba(255,255,255,.5);}

@media(max-width:900px){
    .auth-wrap{grid-template-columns:1fr;}
    .auth-deco{display:none;}
    .auth-form-panel{padding:32px 22px;}
    .fields-grid{grid-template-columns:1fr;}
    .field.full{grid-column:auto;}
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

            <h1>Créer un compte</h1>
            <p class="auth-sub">Rejoignez des centaines de membres sur <strong>AntiGaspiCI</strong>. Gratuit, sans engagement.</p>

            @if($errors->any())
            <div class="auth-error">
                @foreach($errors->all() as $err)
                <div><i class="fas fa-times-circle"></i>{{ $err }}</div>
                @endforeach
            </div>
            @endif

            <form action="{{ route('inscrire') }}" method="POST" novalidate>
                @csrf

                {{-- Rôle --}}
                <div class="role-grid">
                    <label class="role-card">
                        <input type="radio" name="role" value="acheteur" {{ old('role','acheteur')==='acheteur'?'checked':'' }} onchange="toggleFournisseur(this)">
                        <div class="role-label">
                            <div class="role-ico" style="background:#eff6ff;">🛒</div>
                            <div class="role-nm">Acheteur</div>
                            <div class="role-desc">Particulier, ONG, éleveur...</div>
                        </div>
                    </label>
                    <label class="role-card">
                        <input type="radio" name="role" value="fournisseur" {{ old('role')==='fournisseur'?'checked':'' }} onchange="toggleFournisseur(this)">
                        <div class="role-label">
                            <div class="role-ico" style="background:#f0fdf4;">🏪</div>
                            <div class="role-nm">Fournisseur</div>
                            <div class="role-desc">Restaurant, marché, agriculteur...</div>
                        </div>
                    </label>
                </div>

                {{-- Champs principaux --}}
                <div class="fields-grid">
                    <div class="field">
                        <label>Prénom *</label>
                        <div class="field-inner">
                            <i class="fas fa-user ico"></i>
                            <input type="text" name="prenom" value="{{ old('prenom') }}" placeholder="Yao" required>
                        </div>
                    </div>
                    <div class="field">
                        <label>Nom *</label>
                        <div class="field-inner">
                            <i class="fas fa-user ico"></i>
                            <input type="text" name="nom" value="{{ old('nom') }}" placeholder="KONAN" required>
                        </div>
                    </div>
                    <div class="field">
                        <label>Email *</label>
                        <div class="field-inner">
                            <i class="fas fa-envelope ico"></i>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="vous@email.com" required
                                class="{{ $errors->has('email') ? 'err' : '' }}">
                        </div>
                    </div>
                    <div class="field">
                        <label>Téléphone</label>
                        <div class="field-inner">
                            <i class="fas fa-phone ico"></i>
                            <input type="tel" name="telephone" value="{{ old('telephone') }}" placeholder="+225 07 00 00 00 00">
                        </div>
                    </div>
                    <div class="field">
                        <label>Mot de passe *</label>
                        <div class="field-inner">
                            <i class="fas fa-lock ico"></i>
                            <input type="password" name="password" id="pwd1" placeholder="Min. 8 caractères" required
                                class="{{ $errors->has('password') ? 'err' : '' }}">
                            <button type="button" class="eye-btn" onclick="togglePwd('pwd1','eye1')">
                                <i class="fas fa-eye" id="eye1"></i>
                            </button>
                        </div>
                    </div>
                    <div class="field">
                        <label>Confirmer *</label>
                        <div class="field-inner">
                            <i class="fas fa-lock ico"></i>
                            <input type="password" name="password_confirmation" id="pwd2" placeholder="Répétez le mot de passe" required>
                            <button type="button" class="eye-btn" onclick="togglePwd('pwd2','eye2')">
                                <i class="fas fa-eye" id="eye2"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Section fournisseur --}}
                <div id="fournisseurSection" class="fournisseur-section {{ old('role')==='fournisseur'?'show':'' }}">
                    <h4><i class="fas fa-store"></i> Informations de votre structure</h4>
                    <div class="fields-grid">
                        <div class="field">
                            <label>Type de structure</label>
                            <div class="field-inner">
                                <i class="fas fa-building ico"></i>
                                <select name="type_structure">
                                    <option value="restaurant">Restaurant</option>
                                    <option value="marché">Marché / Commerce</option>
                                    <option value="transformateur">Transformateur</option>
                                    <option value="artisan">Artisan</option>
                                    <option value="éleveur">Éleveur</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                        </div>
                        <div class="field">
                            <label>Nom de la structure</label>
                            <div class="field-inner">
                                <i class="fas fa-tag ico"></i>
                                <input type="text" name="nom_structure" value="{{ old('nom_structure') }}" placeholder="Ex: Restaurant Chez Mamie">
                            </div>
                        </div>
                    </div>
                </div>

                <label class="cgu-row">
                    <input type="checkbox" required>
                    <span>J'accepte les <a href="#">conditions d'utilisation</a> et la <a href="#">politique de confidentialité</a> d'AntiGaspiCI.</span>
                </label>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-user-plus"></i> Créer mon compte gratuitement
                </button>
            </form>

            <p class="auth-switch">
                Déjà membre ? <a href="{{ route('connexion') }}">Se connecter</a>
            </p>
        </div>

        {{-- DECO --}}
        <div class="auth-deco">
            <div class="deco-grid"></div>
            <div class="deco-glow"></div>
            <div class="deco-inner">
                <span class="deco-illus">🚀</span>
                <div class="deco-title">En <strong>3 minutes</strong>,<br>vous êtes prêt</div>
                <p class="deco-sub">Rejoignez le réseau anti-gaspillage alimentaire de Côte d'Ivoire.</p>
                <div class="deco-steps">
                    <div class="deco-step">
                        <div class="dss-num">1</div>
                        <div>
                            <div class="dss-txt">Créez votre compte</div>
                            <div class="dss-sub">Gratuit, 2 minutes</div>
                        </div>
                    </div>
                    <div class="deco-step">
                        <div class="dss-num">2</div>
                        <div>
                            <div class="dss-txt">Publiez vos surplus</div>
                            <div class="dss-sub">Photo + description</div>
                        </div>
                    </div>
                    <div class="deco-step">
                        <div class="dss-num">3</div>
                        <div>
                            <div class="dss-txt">Trouvez un preneur</div>
                            <div class="dss-sub">Messagerie intégrée</div>
                        </div>
                    </div>
                    <div class="deco-step">
                        <div class="dss-num">4</div>
                        <div>
                            <div class="dss-txt">Collecte &amp; impact</div>
                            <div class="dss-sub">Zéro gaspillage</div>
                        </div>
                    </div>
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
function toggleFournisseur(radio){
    const sec = document.getElementById('fournisseurSection');
    sec.classList.toggle('show', radio.value === 'fournisseur');
}
</script>
@endpush

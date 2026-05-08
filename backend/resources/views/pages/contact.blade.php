@extends('layouts.front')
@section('title','Contact')
@section('description','Contactez l\'équipe AntiGaspiCI — nous répondons sous 24h.')

@push('styles')
<style>
.contact-section{padding:80px 48px;background:#fff;}
.contact-inner{max-width:1040px;margin:0 auto;}
.contact-grid{display:grid;grid-template-columns:1fr 1.6fr;gap:40px;align-items:start;}

/* ── INFO CARDS ── */
.info-cards{display:flex;flex-direction:column;gap:14px;}
.info-card{
    background:var(--surface);border-radius:16px;padding:24px;
    border:1px solid var(--border);display:flex;align-items:flex-start;gap:16px;
    transition:all .3s;
}
.info-card:hover{border-color:var(--green);box-shadow:0 8px 24px rgba(22,163,74,.1);transform:translateX(4px);}
.info-ico{width:46px;height:46px;flex-shrink:0;border-radius:12px;
    display:flex;align-items:center;justify-content:center;font-size:1.1rem;}
.info-body h4{font-family:'Rubik',sans-serif;font-size:.88rem;font-weight:800;color:var(--text);margin-bottom:3px;}
.info-body p{font-size:.82rem;color:var(--muted);line-height:1.6;}
.info-body a{color:var(--green);text-decoration:none;font-weight:600;}
.info-body a:hover{text-decoration:underline;}

.social-row{margin-top:20px;padding:20px;background:var(--surface);border-radius:16px;border:1px solid var(--border);}
.social-row h4{font-family:'Rubik',sans-serif;font-size:.82rem;font-weight:700;color:var(--text);margin-bottom:12px;}
.social-icons{display:flex;gap:8px;}
.soc-ico{width:38px;height:38px;border-radius:10px;background:#fff;border:1px solid var(--border);
    display:flex;align-items:center;justify-content:center;font-size:.9rem;
    text-decoration:none;color:var(--muted);transition:all .2s;}
.soc-ico:hover{background:var(--green);border-color:var(--green);color:#fff;transform:translateY(-2px);}

/* ── FORM CARD ── */
.form-card{background:#fff;border:1px solid var(--border);border-radius:20px;padding:36px;
    box-shadow:0 4px 24px rgba(0,0,0,.05);}
.form-card h3{font-family:'Rubik',sans-serif;font-size:1.2rem;font-weight:800;color:var(--text);margin-bottom:4px;}
.form-card p{font-size:.84rem;color:var(--muted);margin-bottom:24px;}

.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.form-group{margin-bottom:14px;}
.form-group label{display:block;font-size:.78rem;font-weight:700;color:var(--text);margin-bottom:5px;letter-spacing:.2px;}
.form-ctrl{
    width:100%;border:1.5px solid var(--border);border-radius:12px;
    padding:11px 14px;font-size:.85rem;font-family:'Nunito Sans',sans-serif;
    color:var(--text);background:#fff;transition:all .2s;outline:none;
}
.form-ctrl:focus{border-color:var(--green);box-shadow:0 0 0 3px rgba(22,163,74,.1);}
.form-ctrl.is-invalid{border-color:#ef4444;}
.invalid-feedback{font-size:.75rem;color:#ef4444;margin-top:4px;}
textarea.form-ctrl{resize:vertical;min-height:130px;}
select.form-ctrl{cursor:pointer;}

.btn-submit{
    width:100%;background:var(--green);color:#fff;border:none;padding:14px;
    border-radius:50px;font-family:'Rubik',sans-serif;font-weight:800;font-size:.95rem;
    cursor:pointer;transition:all .25s;display:flex;align-items:center;justify-content:center;gap:8px;
    box-shadow:0 4px 18px rgba(22,163,74,.35);
}
.btn-submit:hover{background:var(--green-dark);transform:translateY(-2px);box-shadow:0 8px 28px rgba(22,163,74,.45);}

.alert-success{
    background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;
    border-radius:12px;padding:14px 18px;margin-bottom:20px;
    display:flex;align-items:center;gap:10px;font-size:.88rem;font-weight:600;
}

/* ── MAP STRIP ── */
.map-strip{padding:0 48px 80px;background:#fff;}
.map-strip-inner{max-width:1040px;margin:0 auto;}
.map-strip h3{font-family:'Rubik',sans-serif;font-size:1rem;font-weight:800;color:var(--text);margin-bottom:12px;}
.map-frame{border-radius:18px;overflow:hidden;border:1px solid var(--border);height:280px;position:relative;}
.map-frame iframe{width:100%;height:100%;border:0;}

@media(max-width:900px){.contact-grid{grid-template-columns:1fr;}.form-row{grid-template-columns:1fr;}}
@media(max-width:768px){.contact-section,.map-strip{padding:60px 20px;}}
</style>
@endpush

@section('content')

{{-- HERO --}}
<div class="page-hero">
    <div class="page-hero-grid"></div>
    <div class="page-hero-glow"></div>
    <div class="page-hero-inner">
        <div class="page-hero-tag">
            <svg width="8" height="8" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4" fill="#4ade80"/></svg>
            Nous sommes là pour vous
        </div>
        <h1>Contactez <span>notre équipe</span></h1>
        <p>Une question, un partenariat, un signalement ? Nous lisons chaque message et répondons sous 24 heures ouvrées.</p>
    </div>
    <svg class="page-hero-wave" viewBox="0 0 1440 56" xmlns="http://www.w3.org/2000/svg">
        <path d="M0,32 C360,56 1080,0 1440,32 L1440,56 L0,56 Z" fill="#ffffff"/>
    </svg>
</div>

{{-- CONTACT --}}
<section class="contact-section">
    <div class="contact-inner">
        <div class="contact-grid">

            {{-- Infos --}}
            <div class="reveal-left">
                <div class="sec-tag" style="margin-bottom:20px;"><div class="sec-line"></div>Coordonnées<div class="sec-line"></div></div>
                <h2 class="sec-title" style="font-size:1.6rem;margin-bottom:24px;">Restons en <span class="hl">contact</span></h2>

                <div class="info-cards">
                    <div class="info-card">
                        <div class="info-ico" style="background:#f0fdf4;color:var(--green);"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="info-body">
                            <h4>Notre adresse</h4>
                            <p>Plateau, Abidjan<br>Côte d'Ivoire</p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-ico" style="background:#fff7ed;color:var(--orange);"><i class="fas fa-phone-alt"></i></div>
                        <div class="info-body">
                            <h4>Téléphone &amp; WhatsApp</h4>
                            <p><a href="tel:+2250700000000">+225 07 00 00 00 00</a></p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-ico" style="background:#eff6ff;color:#2563eb;"><i class="fas fa-envelope"></i></div>
                        <div class="info-body">
                            <h4>Email</h4>
                            <p><a href="mailto:contact@antigaspi.ci">contact@antigaspi.ci</a></p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-ico" style="background:#fdf4ff;color:#7c3aed;"><i class="fas fa-clock"></i></div>
                        <div class="info-body">
                            <h4>Horaires</h4>
                            <p>Lundi – Vendredi : 8h – 18h<br>Samedi : 9h – 13h</p>
                        </div>
                    </div>
                </div>

                <div class="social-row">
                    <h4>Suivez-nous</h4>
                    <div class="social-icons">
                        <a href="#" class="soc-ico" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="soc-ico" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="soc-ico" title="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="soc-ico" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                        <a href="#" class="soc-ico" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>

            {{-- Formulaire --}}
            <div class="reveal-right">
                <div class="form-card">
                    <h3>Envoyez-nous un message</h3>
                    <p>Réponse garantie sous 24h ouvrées.</p>

                    @if(session('success'))
                    <div class="alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                    @endif

                    <form action="{{ route('contact.send') }}" method="POST" novalidate>
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom">Votre nom <span style="color:#ef4444;">*</span></label>
                                <input type="text" id="nom" name="nom" class="form-ctrl @error('nom') is-invalid @enderror"
                                    value="{{ old('nom') }}" placeholder="Ex: Kouassi Aya" required>
                                @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label for="email">Adresse email <span style="color:#ef4444;">*</span></label>
                                <input type="email" id="email" name="email" class="form-ctrl @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" placeholder="vous@email.com" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="sujet">Sujet <span style="color:#ef4444;">*</span></label>
                            <select id="sujet" name="sujet" class="form-ctrl @error('sujet') is-invalid @enderror" required>
                                <option value="">— Choisissez un sujet —</option>
                                <option value="question_generale" {{ old('sujet')=='question_generale'?'selected':'' }}>Question générale</option>
                                <option value="partenariat" {{ old('sujet')=='partenariat'?'selected':'' }}>Partenariat</option>
                                <option value="probleme_technique" {{ old('sujet')=='probleme_technique'?'selected':'' }}>Problème technique</option>
                                <option value="signalement" {{ old('sujet')=='signalement'?'selected':'' }}>Signalement</option>
                                <option value="presse" {{ old('sujet')=='presse'?'selected':'' }}>Presse / Médias</option>
                                <option value="autre" {{ old('sujet')=='autre'?'selected':'' }}>Autre</option>
                            </select>
                            @error('sujet')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label for="message">Votre message <span style="color:#ef4444;">*</span></label>
                            <textarea id="message" name="message" class="form-ctrl @error('message') is-invalid @enderror"
                                placeholder="Décrivez votre demande en détail..." required>{{ old('message') }}</textarea>
                            @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn-submit">
                            <i class="fas fa-paper-plane"></i> Envoyer le message
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- MAP --}}
<div class="map-strip">
    <div class="map-strip-inner reveal">
        <h3><i class="fas fa-map-marker-alt" style="color:var(--green);margin-right:6px;"></i>Nous trouver à Abidjan</h3>
        <div class="map-frame">
            <iframe
                src="https://www.openstreetmap.org/export/embed.html?bbox=-4.0366%2C5.3184%2C-3.9966%2C5.3584&layer=mapnik&marker=5.3384%2C-4.0166"
                allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</div>

@endsection

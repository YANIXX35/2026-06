@extends('layouts.front')
@section('title','Comment ça marche')
@section('description','Découvrez comment AntiGaspiCI fonctionne — de la publication à la collecte en 4 étapes simples.')

@push('styles')
<style>
/* ── STEPS ── */
.steps-section{padding:80px 48px;background:#fff;}
.steps-inner{max-width:1080px;margin:0 auto;}

.steps-track{display:grid;grid-template-columns:repeat(4,1fr);gap:0;position:relative;margin-top:64px;}
.steps-track::before{
    content:'';position:absolute;top:36px;left:12.5%;right:12.5%;height:2px;
    background:linear-gradient(90deg,var(--green),var(--green-light));
    z-index:0;border-radius:2px;
}
.step-card{text-align:center;padding:0 16px;position:relative;z-index:1;}
.step-num{
    width:72px;height:72px;border-radius:50%;
    background:#fff;border:3px solid var(--green);
    display:flex;align-items:center;justify-content:center;
    font-family:'Rubik',sans-serif;font-size:1.5rem;font-weight:900;
    color:var(--green);margin:0 auto 20px;
    box-shadow:0 0 0 6px var(--green-50);position:relative;
    transition:all .3s;
}
.step-card:hover .step-num{background:var(--green);color:#fff;box-shadow:0 8px 24px rgba(22,163,74,.35);}
.step-ico{
    width:52px;height:52px;border-radius:14px;
    display:flex;align-items:center;justify-content:center;
    font-size:1.2rem;margin:0 auto 14px;transition:all .3s;
}
.step-card:hover .step-ico{transform:scale(1.1);}
.step-card h3{font-family:'Rubik',sans-serif;font-size:1rem;font-weight:800;color:var(--text);margin-bottom:8px;}
.step-card p{font-size:.84rem;color:var(--muted);line-height:1.68;}

/* ── ACTEURS ── */
.acteurs-section{padding:80px 48px;background:var(--surface);}
.acteurs-inner{max-width:1080px;margin:0 auto;}
.acteurs-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-top:48px;}
.acteur-card{
    background:#fff;border-radius:18px;padding:32px 26px;
    border:1px solid var(--border);transition:all .3s;
    position:relative;overflow:hidden;
}
.acteur-card::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;
    border-radius:18px 18px 0 0;background:var(--ac-clr,var(--green));}
.acteur-card:hover{transform:translateY(-4px);box-shadow:0 16px 40px rgba(0,0,0,.08);}
.ac-ico{width:52px;height:52px;border-radius:13px;
    background:var(--ac-bg,#f0fdf4);display:flex;align-items:center;
    justify-content:center;font-size:1.5rem;margin-bottom:16px;}
.acteur-card h3{font-family:'Rubik',sans-serif;font-size:.95rem;font-weight:800;color:var(--text);margin-bottom:8px;}
.acteur-card p{font-size:.83rem;color:var(--muted);line-height:1.68;margin-bottom:14px;}
.ac-tags{display:flex;flex-wrap:wrap;gap:5px;}
.ac-tag{font-size:.7rem;font-weight:700;padding:3px 10px;border-radius:50px;
    background:var(--ac-bg,#f0fdf4);color:var(--ac-clr,var(--green));}

/* ── OFFRES ── */
.offres-section{padding:80px 48px;background:#fff;}
.offres-inner{max-width:1080px;margin:0 auto;}
.offres-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:20px;margin-top:48px;}
.offre-card{
    display:flex;align-items:flex-start;gap:18px;padding:28px;
    background:var(--surface);border-radius:18px;border:1px solid var(--border);
    transition:all .3s;
}
.offre-card:hover{border-color:var(--green);box-shadow:0 8px 28px rgba(22,163,74,.1);}
.offre-ico{width:48px;height:48px;flex-shrink:0;border-radius:13px;
    display:flex;align-items:center;justify-content:center;font-size:1.3rem;}
.offre-body h4{font-family:'Rubik',sans-serif;font-size:.95rem;font-weight:800;color:var(--text);margin-bottom:5px;}
.offre-body p{font-size:.82rem;color:var(--muted);line-height:1.65;}
.offre-pill{display:inline-block;margin-top:8px;font-size:.68rem;font-weight:700;
    padding:3px 10px;border-radius:50px;}

/* ── FAQ ── */
.faq-section{padding:80px 48px;background:var(--surface);}
.faq-inner{max-width:760px;margin:0 auto;}
.faq-item{background:#fff;border-radius:14px;border:1px solid var(--border);margin-bottom:10px;overflow:hidden;transition:box-shadow .2s;}
.faq-item:hover{box-shadow:0 4px 16px rgba(0,0,0,.06);}
.faq-q{
    width:100%;background:none;border:none;padding:20px 24px;
    display:flex;justify-content:space-between;align-items:center;
    font-family:'Nunito Sans',sans-serif;font-size:.9rem;font-weight:700;
    color:var(--text);cursor:pointer;text-align:left;
}
.faq-q i{color:var(--green);transition:transform .3s;font-size:.8rem;flex-shrink:0;margin-left:12px;}
.faq-item.open .faq-q i{transform:rotate(180deg);}
.faq-a{max-height:0;overflow:hidden;transition:max-height .35s ease;padding:0 24px;}
.faq-item.open .faq-a{max-height:200px;padding-bottom:18px;}
.faq-a p{font-size:.84rem;color:var(--muted);line-height:1.72;border-top:1px solid var(--border);padding-top:14px;}

/* ── CTA ── */
.cta-section{
    padding:80px 48px;
    background:linear-gradient(135deg,#052e16 0%,#0d3d1f 60%,#0f5132 100%);
    text-align:center;position:relative;overflow:hidden;
}
.cta-glow{position:absolute;top:-60px;left:50%;transform:translateX(-50%);
    width:500px;height:300px;border-radius:50%;
    background:radial-gradient(circle,rgba(34,197,94,.15) 0%,transparent 70%);pointer-events:none;}
.cta-inner{position:relative;z-index:2;max-width:560px;margin:0 auto;}
.cta-section h2{font-family:'Rubik',sans-serif;font-size:clamp(1.8rem,3vw,2.5rem);
    font-weight:900;color:#fff;letter-spacing:-1px;margin-bottom:12px;}
.cta-section h2 span{color:#4ade80;}
.cta-section p{color:rgba(255,255,255,.65);font-size:.95rem;line-height:1.7;margin-bottom:28px;}
.cta-actions{display:flex;justify-content:center;flex-wrap:wrap;gap:12px;}
.btn-cta-white{background:#fff;color:var(--green);padding:14px 32px;border-radius:50px;
    font-family:'Rubik',sans-serif;font-weight:800;font-size:.95rem;
    text-decoration:none;display:inline-flex;align-items:center;gap:7px;
    transition:all .25s;box-shadow:0 4px 18px rgba(0,0,0,.2);}
.btn-cta-white:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(0,0,0,.3);color:var(--green);}
.btn-cta-ghost{background:transparent;color:rgba(255,255,255,.82);padding:14px 28px;
    border-radius:50px;font-weight:700;font-size:.9rem;text-decoration:none;
    display:inline-flex;align-items:center;gap:7px;
    border:1.5px solid rgba(255,255,255,.25);transition:all .25s;}
.btn-cta-ghost:hover{background:rgba(255,255,255,.08);color:#fff;border-color:rgba(255,255,255,.4);}

@media(max-width:900px){.acteurs-grid{grid-template-columns:1fr 1fr;}}
@media(max-width:768px){
    .steps-section,.acteurs-section,.offres-section,.faq-section,.cta-section{padding:60px 20px;}
    .steps-track{grid-template-columns:1fr 1fr;gap:32px;}
    .steps-track::before{display:none;}
    .acteurs-grid{grid-template-columns:1fr;}
    .offres-grid{grid-template-columns:1fr;}
}
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
            Simple &amp; Efficace
        </div>
        <h1>Comment <span>ça marche</span> ?</h1>
        <p>Rejoignez la communauté anti-gaspillage de Côte d'Ivoire. En 4 étapes simples, transformez vos invendus en opportunités concrètes.</p>
    </div>
    <svg class="page-hero-wave" viewBox="0 0 1440 56" xmlns="http://www.w3.org/2000/svg">
        <path d="M0,32 C360,56 1080,0 1440,32 L1440,56 L0,56 Z" fill="#ffffff"/>
    </svg>
</div>

{{-- ÉTAPES --}}
<section class="steps-section">
    <div class="steps-inner">
        <div class="text-center reveal">
            <div class="sec-tag"><div class="sec-line"></div>Processus<div class="sec-line"></div></div>
            <h2 class="sec-title">4 étapes vers <span class="hl">zéro gaspillage</span></h2>
            <p class="sec-sub" style="margin:10px auto 0;text-align:center;">De la création de votre compte à la collecte, tout est conçu pour être rapide et accessible.</p>
        </div>
        <div class="steps-track">
            <div class="step-card reveal" style="transition-delay:.05s">
                <div class="step-num">1</div>
                <div class="step-ico" style="background:#f0fdf4;color:var(--green);"><i class="fas fa-user-plus"></i></div>
                <h3>Créez votre compte</h3>
                <p>Inscription gratuite en 2 minutes. Choisissez votre profil : restaurant, épicerie, agriculteur ou particulier.</p>
            </div>
            <div class="step-card reveal" style="transition-delay:.13s">
                <div class="step-num">2</div>
                <div class="step-ico" style="background:#fff7ed;color:var(--orange);"><i class="fas fa-bullhorn"></i></div>
                <h3>Publiez votre annonce</h3>
                <p>Photos, description, quantité, prix ou gratuit. Votre annonce est visible par toute la communauté en moins de 5 min.</p>
            </div>
            <div class="step-card reveal" style="transition-delay:.21s">
                <div class="step-num">3</div>
                <div class="step-ico" style="background:#eff6ff;color:#2563eb;"><i class="fas fa-handshake"></i></div>
                <h3>Trouvez un preneur</h3>
                <p>Les acheteurs vous contactent via messagerie. Réservation sécurisée et confirmation instantanée.</p>
            </div>
            <div class="step-card reveal" style="transition-delay:.29s">
                <div class="step-num">4</div>
                <div class="step-ico" style="background:#fdf4ff;color:#7c3aed;"><i class="fas fa-box-open"></i></div>
                <h3>Collecte &amp; livraison</h3>
                <p>Convenez d'un point de collecte ou d'une livraison. Validez la transaction et réduisez le gaspillage.</p>
            </div>
        </div>
    </div>
</section>

{{-- ACTEURS --}}
<section class="acteurs-section">
    <div class="acteurs-inner">
        <div class="text-center reveal">
            <div class="sec-tag"><div class="sec-line"></div>Qui peut participer ?<div class="sec-line"></div></div>
            <h2 class="sec-title">Une plateforme pour <span class="hl">tous les acteurs</span></h2>
            <p class="sec-sub" style="margin:10px auto 0;text-align:center;">Restaurants, épiceries, agriculteurs, ONG — chacun a sa place dans l'écosystème AntiGaspiCI.</p>
        </div>
        <div class="acteurs-grid">
            <div class="acteur-card reveal" style="--ac-clr:#16a34a;--ac-bg:#f0fdf4;transition-delay:.05s">
                <div class="ac-ico">🍽️</div>
                <h3>Restaurants &amp; Traiteurs</h3>
                <p>Valorisez vos invendus de fin de journée : plats cuisinés, ingrédients frais, desserts. Réduisez vos coûts et votre empreinte carbone.</p>
                <div class="ac-tags">
                    <span class="ac-tag">Vente flash</span>
                    <span class="ac-tag">Don solidaire</span>
                    <span class="ac-tag">Ingrédients</span>
                </div>
            </div>
            <div class="acteur-card reveal" style="--ac-clr:#f97316;--ac-bg:#fff7ed;transition-delay:.1s">
                <div class="ac-ico">🏪</div>
                <h3>Épiceries &amp; Marchés</h3>
                <p>Produits proches de la DLC, surstocks, lots abîmés. Transformez ces pertes en ventes à prix réduit ou en dons.</p>
                <div class="ac-tags">
                    <span class="ac-tag">Lot promo</span>
                    <span class="ac-tag">DLC proche</span>
                    <span class="ac-tag">Surstock</span>
                </div>
            </div>
            <div class="acteur-card reveal" style="--ac-clr:#0ea5e9;--ac-bg:#f0f9ff;transition-delay:.15s">
                <div class="ac-ico">🌾</div>
                <h3>Agriculteurs &amp; Maraîchers</h3>
                <p>Légumes difformes, surplus de récolte, produits saisonniers — trouvez des acheteurs directs sans intermédiaire.</p>
                <div class="ac-tags">
                    <span class="ac-tag">Récolte directe</span>
                    <span class="ac-tag">Alimentation animale</span>
                </div>
            </div>
            <div class="acteur-card reveal" style="--ac-clr:#7c3aed;--ac-bg:#fdf4ff;transition-delay:.2s">
                <div class="ac-ico">🤝</div>
                <h3>ONG &amp; Associations</h3>
                <p>Accédez à des dons alimentaires qualifiés pour vos bénéficiaires. Simplifiez la logistique et maximisez votre impact social.</p>
                <div class="ac-tags">
                    <span class="ac-tag">Dons</span>
                    <span class="ac-tag">Solidarité</span>
                </div>
            </div>
            <div class="acteur-card reveal" style="--ac-clr:#d97706;--ac-bg:#fffbeb;transition-delay:.25s">
                <div class="ac-ico">🏠</div>
                <h3>Particuliers</h3>
                <p>Vous avez trop acheté ? Un jardin productif ? Partagez vos excédents avec vos voisins gratuitement ou à petit prix.</p>
                <div class="ac-tags">
                    <span class="ac-tag">Partage voisinage</span>
                    <span class="ac-tag">Jardin potager</span>
                </div>
            </div>
            <div class="acteur-card reveal" style="--ac-clr:#dc2626;--ac-bg:#fef2f2;transition-delay:.3s">
                <div class="ac-ico">🏭</div>
                <h3>Unités de transformation</h3>
                <p>Achetez des matières premières à bas coût pour confitures, jus, conserves ou alimentation animale.</p>
                <div class="ac-tags">
                    <span class="ac-tag">Matières premières</span>
                    <span class="ac-tag">Transformation</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- TYPES D'OFFRES --}}
<section class="offres-section">
    <div class="offres-inner">
        <div class="text-center reveal">
            <div class="sec-tag"><div class="sec-line"></div>Types d'offres<div class="sec-line"></div></div>
            <h2 class="sec-title">4 façons de <span class="hl">valoriser</span> vos aliments</h2>
        </div>
        <div class="offres-grid">
            <div class="offre-card reveal" style="transition-delay:.05s">
                <div class="offre-ico" style="background:#eff6ff;border:2px solid #bfdbfe;color:#2563eb;"><i class="fas fa-tag"></i></div>
                <div class="offre-body">
                    <h4>Vente à prix réduit</h4>
                    <p>Proposez vos invendus à un tarif inférieur au marché. Récupérez une partie de votre investissement tout en évitant le gaspillage.</p>
                    <span class="offre-pill" style="background:#eff6ff;color:#2563eb;">Prix négociable</span>
                </div>
            </div>
            <div class="offre-card reveal" style="transition-delay:.1s">
                <div class="offre-ico" style="background:#f0fdf4;border:2px solid #bbf7d0;color:#16a34a;"><i class="fas fa-gift"></i></div>
                <div class="offre-body">
                    <h4>Don gratuit</h4>
                    <p>Offrez vos surplus à ceux qui en ont besoin — ONG, familles, associations. Un geste solidaire à impact immédiat.</p>
                    <span class="offre-pill" style="background:#f0fdf4;color:#16a34a;">Solidarité</span>
                </div>
            </div>
            <div class="offre-card reveal" style="transition-delay:.15s">
                <div class="offre-ico" style="background:#fffbeb;border:2px solid #fde68a;color:#d97706;"><i class="fas fa-horse"></i></div>
                <div class="offre-body">
                    <h4>Alimentation animale</h4>
                    <p>Vos déchets alimentaires peuvent nourrir bovins, volailles et porcs. Cédez-les à des éleveurs qui en font bon usage.</p>
                    <span class="offre-pill" style="background:#fffbeb;color:#d97706;">Économie circulaire</span>
                </div>
            </div>
            <div class="offre-card reveal" style="transition-delay:.2s">
                <div class="offre-ico" style="background:#fdf4ff;border:2px solid #e9d5ff;color:#7c3aed;"><i class="fas fa-industry"></i></div>
                <div class="offre-body">
                    <h4>Transformation</h4>
                    <p>Vendez à des unités de transformation qui produisent confitures, jus, farines et conserves à partir de vos excédents.</p>
                    <span class="offre-pill" style="background:#fdf4ff;color:#7c3aed;">Valeur ajoutée</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- FAQ --}}
<section class="faq-section">
    <div class="faq-inner">
        <div class="text-center reveal" style="margin-bottom:40px;">
            <div class="sec-tag"><div class="sec-line"></div>FAQ<div class="sec-line"></div></div>
            <h2 class="sec-title">Questions <span class="hl">fréquentes</span></h2>
        </div>
        <div class="faq-item reveal">
            <button class="faq-q" onclick="toggleFaq(this)">L'inscription est-elle vraiment gratuite ? <i class="fas fa-chevron-down"></i></button>
            <div class="faq-a"><p>L'inscription et la mise en relation sont totalement gratuites et sans engagement : créez votre compte, publiez des annonces et contactez des vendeurs sans frais cachés. Pour les annonces en don, la transaction elle-même est également gratuite. Pour les annonces à prix réduit, le paiement se fait dans l'app via Wave ou Moov Money au moment du retrait.</p></div>
        </div>
        <div class="faq-item reveal">
            <button class="faq-q" onclick="toggleFaq(this)">Comment fonctionne le paiement ? <i class="fas fa-chevron-down"></i></button>
            <div class="faq-a"><p>Pour une annonce en don, il n'y a rien à payer. Pour une annonce à prix réduit, vous réglez dans le panier via Wave ou Moov Money. Dans cette version de démonstration académique, le paiement mobile est simulé (aucun débit réel n'est effectué) : l'intégration à une passerelle de paiement réelle n'est pas encore branchée.</p></div>
        </div>
        <div class="faq-item reveal">
            <button class="faq-q" onclick="toggleFaq(this)">Combien de temps reste une annonce en ligne ? <i class="fas fa-chevron-down"></i></button>
            <div class="faq-a"><p>Les annonces sont actives pendant 7 jours par défaut. Vous pouvez les renouveler gratuitement, les modifier ou les clôturer dès que vos invendus sont écoulés.</p></div>
        </div>
        <div class="faq-item reveal">
            <button class="faq-q" onclick="toggleFaq(this)">Est-ce disponible partout en Côte d'Ivoire ? <i class="fas fa-chevron-down"></i></button>
            <div class="faq-a"><p>AntiGaspiCI est disponible sur tout le territoire ivoirien. La carte interactive permet de trouver des annonces proches de vous, que vous soyez à Abidjan, Bouaké, Yamoussoukro, San Pedro ou ailleurs.</p></div>
        </div>
        <div class="faq-item reveal">
            <button class="faq-q" onclick="toggleFaq(this)">Puis-je publier sans avoir un smartphone ? <i class="fas fa-chevron-down"></i></button>
            <div class="faq-a"><p>Oui ! AntiGaspiCI est entièrement responsive et fonctionne sur tout appareil via le navigateur web. Aucun téléchargement d'application requis — une connexion internet suffit.</p></div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="cta-section">
    <div class="cta-glow"></div>
    <div class="cta-inner reveal">
        <h2>Prêt à rejoindre le <span>mouvement</span> ?</h2>
        <p>Des centaines de commerçants et associations utilisent déjà AntiGaspiCI pour réduire leurs pertes alimentaires. Rejoignez-les dès aujourd'hui.</p>
        <div class="cta-actions">
            @guest
                <a href="{{ route('inscription') }}" class="btn-cta-white"><i class="fas fa-leaf"></i> Créer mon compte gratuit</a>
                <a href="{{ route('annonces.index') }}" class="btn-cta-ghost"><i class="fas fa-search"></i> Voir les annonces</a>
            @else
                <a href="{{ route('annonces.create') }}" class="btn-cta-white"><i class="fas fa-plus"></i> Publier une annonce</a>
                <a href="{{ route('annonces.index') }}" class="btn-cta-ghost"><i class="fas fa-search"></i> Parcourir les offres</a>
            @endguest
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
function toggleFaq(btn){
    const item = btn.closest('.faq-item');
    const wasOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item.open').forEach(el=>el.classList.remove('open'));
    if(!wasOpen) item.classList.add('open');
}
</script>
@endpush

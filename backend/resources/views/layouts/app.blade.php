<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'AntiGaspiCI') — Plateforme Anti-Gaspillage Alimentaire</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>

    <!-- Topbar -->
    <div class="container-fluid px-5 d-none border-bottom d-lg-block bg-white">
        <div class="row gx-0 align-items-center" style="height:45px;">
            <div class="col-lg-4 text-start">
                <a href="{{ route('comment-ca-marche') }}" class="text-muted me-2 small">Aide</a> /
                <a href="{{ route('contact') }}" class="text-muted mx-2 small">Support</a> /
                <a href="{{ route('contact') }}" class="text-muted ms-2 small">Contact</a>
            </div>
            <div class="col-lg-4 text-center">
                <small class="text-dark"><i class="fas fa-phone-alt me-1 text-primary"></i>(+225) 07 00 00 00 00 (exemple)</small>
            </div>
            <div class="col-lg-4 text-end">
                @auth
                    <span class="text-muted small me-2"><i class="fas fa-user-circle me-1 text-primary"></i>{{ Auth::user()->prenom }}</span>
                    <a href="{{ route('dashboard') }}" class="text-muted small me-2">Dashboard</a>
                    <form action="{{ route('deconnecter') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link text-muted p-0 small">Déconnexion</button>
                    </form>
                @else
                    <a href="{{ route('connexion') }}" class="text-muted small me-3"><i class="fas fa-sign-in-alt me-1"></i>Connexion</a>
                    <a href="{{ route('inscription') }}" class="text-muted small"><i class="fas fa-user-plus me-1"></i>Inscription</a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Logo & Search -->
    <div class="container-fluid px-5 py-3 d-none d-lg-block bg-white border-bottom">
        <div class="row gx-0 align-items-center">
            <div class="col-lg-3">
                <a href="/" class="navbar-brand p-0 text-decoration-none">
                    <h2 class="text-primary m-0"><i class="fas fa-leaf text-success me-2"></i>AntiGaspi<span class="text-secondary">CI</span></h2>
                </a>
            </div>
            <div class="col-lg-6">
                <form action="{{ route('annonces.index') }}" method="GET">
                    <div class="d-flex border rounded-pill overflow-hidden">
                        <input class="form-control border-0 py-2 px-4" type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher des surplus alimentaires...">
                        <select class="form-select text-dark border-0 border-start py-2" style="width:180px;" name="categorie">
                            <option value="">Toutes catégories</option>
                            @foreach(\Illuminate\Support\Facades\Cache::remember('all_categories', 600, fn() => \App\Models\Categorie::all()) as $cat)
                                <option value="{{ $cat->id }}" {{ request('categorie') == $cat->id ? 'selected' : '' }}>{{ $cat->nom }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary px-4"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>
            <div class="col-lg-3 text-end">
                @auth
                    <a href="{{ route('messages.index') }}" class="text-muted me-3 position-relative">
                        <span class="rounded-circle btn-md-square border d-inline-flex align-items-center justify-content-center" style="width:38px;height:38px;"><i class="fas fa-comments"></i></span>
                    </a>
                    {{-- Icône panier --}}
                    @php $cartCount = \App\Models\CartItem::where('user_id', Auth::id())->count(); @endphp
                    <a href="{{ route('cart.index') }}" class="text-muted me-3 position-relative" title="Mon panier">
                        <span class="rounded-circle btn-md-square border d-inline-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                            <i class="fas fa-shopping-cart"></i>
                        </span>
                        @if($cartCount > 0)
                        <span style="position:absolute;top:-4px;right:-4px;background:#ef4444;color:#fff;font-size:.6rem;font-weight:700;border-radius:50%;width:18px;height:18px;display:flex;align-items:center;justify-content:center;border:2px solid #fff;">
                            {{ $cartCount > 9 ? '9+' : $cartCount }}
                        </span>
                        @endif
                    </a>
                    <a href="{{ route('annonces.create') }}" class="btn btn-primary rounded-pill py-2 px-4">
                        <i class="fas fa-plus me-1"></i>Publier
                    </a>
                @else
                    <a href="{{ route('connexion') }}" class="btn btn-outline-primary rounded-pill py-2 px-3 me-2">Connexion</a>
                    <a href="{{ route('annonces.create') }}" class="btn btn-primary rounded-pill py-2 px-4">
                        <i class="fas fa-plus me-1"></i>Publier
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Navbar -->
    <div class="container-fluid nav-bar p-0">
        <div class="row gx-0 bg-primary px-5 align-items-center">
            <div class="col-lg-3 d-none d-lg-block">
                <nav class="navbar navbar-light position-relative" style="width:250px;">
                    <button class="navbar-toggler border-0 w-100 px-0 text-start text-white" type="button" data-bs-toggle="collapse" data-bs-target="#allCat">
                        <h5 class="m-0 text-white py-2"><i class="fa fa-bars me-2"></i>Toutes les catégories</h5>
                    </button>
                    <div class="collapse navbar-collapse rounded-bottom" id="allCat">
                        <ul class="list-unstyled categories-bars w-100">
                            @foreach(\Illuminate\Support\Facades\Cache::remember('all_categories', 600, fn() => \App\Models\Categorie::all()) as $cat)
                            <li>
                                <div class="categories-bars-item">
                                    <a href="{{ route('annonces.index', ['categorie' => $cat->id]) }}">{{ $cat->icone }} {{ $cat->nom }}</a>
                                    <span>({{ $cat->annonces()->where('statut','disponible')->count() }})</span>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </nav>
            </div>
            <div class="col-12 col-lg-9">
                <nav class="navbar navbar-expand-lg navbar-light bg-primary">
                    <a href="/" class="navbar-brand d-block d-lg-none text-white fw-bold">
                        <i class="fas fa-leaf me-1"></i>AntiGaspiCI
                    </a>
                    <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                        <span class="fa fa-bars fa-1x"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarMain">
                        <div class="navbar-nav ms-auto py-0">
                            <a href="/" class="nav-item nav-link {{ request()->is('/') ? 'active' : '' }}">Accueil</a>
                            <a href="{{ route('annonces.index') }}" class="nav-item nav-link {{ request()->is('annonces*') ? 'active' : '' }}">Annonces</a>
                            <a href="{{ route('comment-ca-marche') }}" class="nav-item nav-link">Comment ça marche</a>
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Acteurs</a>
                                <div class="dropdown-menu m-0">
                                    <a href="{{ route('annonces.index', ['type_structure' => 'restaurant']) }}" class="dropdown-item">🍽️ Restaurants</a>
                                    <a href="{{ route('annonces.index', ['type_structure' => 'marché']) }}" class="dropdown-item">🛒 Marchés</a>
                                    <a href="{{ route('annonces.index', ['type_structure' => 'transformateur']) }}" class="dropdown-item">🏭 Transformateurs</a>
                                    <a href="{{ route('annonces.index', ['type_structure' => 'artisan']) }}" class="dropdown-item">👨‍🍳 Artisans</a>
                                </div>
                            </div>
                            <a href="{{ route('contact') }}" class="nav-item nav-link me-2">Contact</a>
                        </div>
                        @auth
                            {{-- Cloche notifications --}}
                            @php $unread = Auth::user()->unreadNotifications()->count(); @endphp
                            <div class="position-relative me-3 d-inline-block" style="cursor:pointer;" id="notifBell">
                                <button class="btn btn-outline-light rounded-circle p-0"
                                    style="width:38px;height:38px;border:1.5px solid rgba(255,255,255,.5);"
                                    onclick="toggleNotifDropdown()" title="Notifications">
                                    <i class="fas fa-bell" style="font-size:.95rem;"></i>
                                </button>
                                @if($unread > 0)
                                <span id="notifBadge"
                                    style="position:absolute;top:-4px;right:-4px;background:#ef4444;color:#fff;font-size:.6rem;font-weight:700;border-radius:50%;width:18px;height:18px;display:flex;align-items:center;justify-content:center;border:2px solid #1a73e8;">
                                    {{ $unread > 9 ? '9+' : $unread }}
                                </span>
                                @endif
                                {{-- Dropdown --}}
                                <div id="notifDropdown"
                                    style="display:none;position:absolute;top:46px;right:0;width:320px;background:#fff;border-radius:14px;box-shadow:0 8px 32px rgba(0,0,0,.18);z-index:9999;border:1px solid #f0f0f0;overflow:hidden;">
                                    <div style="padding:14px 16px;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center;">
                                        <span style="font-size:.85rem;font-weight:700;color:#1a1a2e;">🔔 Notifications</span>
                                        @if($unread > 0)
                                        <form method="POST" action="{{ route('notifications.markAllRead') }}" style="margin:0;">
                                            @csrf
                                            <button type="submit" style="background:none;border:none;font-size:.72rem;color:#16a34a;cursor:pointer;font-weight:600;padding:0;">Tout marquer lu</button>
                                        </form>
                                        @endif
                                    </div>
                                    <div style="max-height:320px;overflow-y:auto;">
                                        @forelse(Auth::user()->notifications->take(8) as $notif)
                                        <a href="{{ $notif->data['url'] ?? '#' }}"
                                            onclick="markRead('{{ $notif->id }}')"
                                            style="display:flex;align-items:flex-start;gap:10px;padding:12px 16px;border-bottom:1px solid #f9f9f9;text-decoration:none;background:{{ $notif->read_at ? '#fff' : '#f0fdf4' }};transition:background .15s;"
                                            onmouseover="this.style.background='#f8f8f8'" onmouseout="this.style.background='{{ $notif->read_at ? '#fff' : '#f0fdf4' }}'">
                                            <span style="font-size:1.2rem;flex-shrink:0;">{{ $notif->data['icone'] ?? '🔔' }}</span>
                                            <div>
                                                <div style="font-size:.78rem;font-weight:600;color:#1a1a2e;">{{ $notif->data['titre'] ?? '' }}</div>
                                                <div style="font-size:.72rem;color:#666;margin-top:2px;line-height:1.4;">{{ $notif->data['message'] ?? '' }}</div>
                                                <div style="font-size:.68rem;color:#aaa;margin-top:3px;">{{ $notif->created_at->diffForHumans() }}</div>
                                            </div>
                                        </a>
                                        @empty
                                        <div style="padding:24px;text-align:center;color:#aaa;font-size:.82rem;">
                                            <i class="fas fa-bell-slash" style="font-size:1.5rem;display:block;margin-bottom:8px;"></i>
                                            Aucune notification
                                        </div>
                                        @endforelse
                                    </div>
                                    <a href="{{ route('notifications.index') }}"
                                        style="display:block;padding:10px;text-align:center;font-size:.75rem;color:#16a34a;font-weight:600;text-decoration:none;border-top:1px solid #f0f0f0;">
                                        Voir toutes les notifications →
                                    </a>
                                </div>
                            </div>

                            <a href="{{ route('annonces.create') }}" class="btn btn-secondary rounded-pill py-2 px-4 mb-3 mb-lg-0">
                                <i class="fas fa-plus-circle me-2"></i>Publier une annonce
                            </a>
                        @else
                            <a href="{{ route('inscription') }}" class="btn btn-secondary rounded-pill py-2 px-4 mb-3 mb-lg-0">
                                <i class="fas fa-user-plus me-2"></i>Rejoindre
                            </a>
                        @endauth
                    </div>
                </nav>
            </div>
        </div>
    </div>

    <!-- Alertes flash -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3 rounded-pill" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-3 rounded-pill" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')

    <!-- Footer -->
    <div class="container-fluid footer py-5 wow fadeIn">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-md-6 col-lg-3">
                    <h4 class="text-primary mb-3"><i class="fas fa-leaf me-2"></i>AntiGaspiCI</h4>
                    <p class="text-light small">Plateforme collaborative dédiée à la réduction du gaspillage alimentaire en Côte d'Ivoire.</p>
                    <div class="d-flex gap-2 mt-3">
                        <a href="#" class="btn btn-primary btn-sm rounded-circle"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-primary btn-sm rounded-circle"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="btn btn-primary btn-sm rounded-circle"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="btn btn-primary btn-sm rounded-circle"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <h4 class="text-primary mb-4">Navigation</h4>
                    <a href="/" class="d-block mb-2 text-light text-decoration-none"><i class="fas fa-angle-right me-2 text-primary"></i>Accueil</a>
                    <a href="{{ route('annonces.index') }}" class="d-block mb-2 text-light text-decoration-none"><i class="fas fa-angle-right me-2 text-primary"></i>Annonces</a>
                    <a href="{{ route('comment-ca-marche') }}" class="d-block mb-2 text-light text-decoration-none"><i class="fas fa-angle-right me-2 text-primary"></i>Comment ça marche</a>
                    <a href="{{ route('inscription') }}" class="d-block mb-2 text-light text-decoration-none"><i class="fas fa-angle-right me-2 text-primary"></i>S'inscrire</a>
                    <a href="{{ route('contact') }}" class="d-block mb-2 text-light text-decoration-none"><i class="fas fa-angle-right me-2 text-primary"></i>Contact</a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <h4 class="text-primary mb-4">Types d'offres</h4>
                    <a href="{{ route('annonces.index', ['type_offre' => 'vente']) }}" class="d-block mb-2 text-light text-decoration-none"><i class="fas fa-angle-right me-2 text-primary"></i>Vente à prix réduit</a>
                    <a href="{{ route('annonces.index', ['type_offre' => 'don']) }}" class="d-block mb-2 text-light text-decoration-none"><i class="fas fa-angle-right me-2 text-primary"></i>Dons gratuits</a>
                    <a href="{{ route('annonces.index', ['type_offre' => 'alimentation_animale']) }}" class="d-block mb-2 text-light text-decoration-none"><i class="fas fa-angle-right me-2 text-primary"></i>Alimentation animale</a>
                    <a href="{{ route('annonces.index', ['type_offre' => 'transformation']) }}" class="d-block mb-2 text-light text-decoration-none"><i class="fas fa-angle-right me-2 text-primary"></i>Transformation</a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <h4 class="text-primary mb-4">Contact</h4>
                    <p class="text-light small"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Abidjan, Plateau, Côte d'Ivoire</p>
                    <p class="text-light small"><i class="fas fa-phone me-2 text-primary"></i>(+225) 07 00 00 00 00 (exemple)</p>
                    <p class="text-light small"><i class="fas fa-envelope me-2 text-primary"></i>contact@antigaspi-ci.com</p>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid copyright py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <span class="text-white small"><i class="fas fa-copyright me-1"></i>{{ date('Y') }} AntiGaspiCI — Tous droits réservés.</span>
                </div>
                <div class="col-md-6 text-center text-md-end text-white small">
                    Projet de mémoire — Côte d'Ivoire
                </div>
            </div>
        </div>
    </div>

    <a href="#" class="btn btn-primary btn-lg-square back-to-top"><i class="fa fa-arrow-up"></i></a>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    @stack('scripts')
    @auth
    <script>
    // ── Cloche notifications ───────────────────────────────────────
    function toggleNotifDropdown(){
        const d = document.getElementById('notifDropdown');
        d.style.display = d.style.display === 'none' ? 'block' : 'none';
    }

    // Fermer si clic hors du dropdown
    document.addEventListener('click', function(e){
        const bell = document.getElementById('notifBell');
        if (bell && !bell.contains(e.target)) {
            const d = document.getElementById('notifDropdown');
            if (d) d.style.display = 'none';
        }
    });

    function markRead(id){
        fetch('{{ route("notifications.markRead", ":id") }}'.replace(':id', id), {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
        }).then(() => {
            const badge = document.getElementById('notifBadge');
            if (badge) {
                let count = parseInt(badge.textContent) - 1;
                if (count <= 0) badge.style.display = 'none';
                else badge.textContent = count > 9 ? '9+' : count;
            }
        });
    }
    </script>
    @endauth
</body>
</html>

<style>
:root {
  --ink: #16191d;
  --paper: #f7f5f0;
  --paper-raised: #ffffff;
  --rule: #d8d3c7;
  --muted: #6b6455;
  --accent: #b5652c;
  --accent-soft: #f0ded0;
  --good: #3f6b4a;
  --good-soft: #e3ede4;
  --bad: #a13f36;
  --bad-soft: #f4e2df;
  --warn: #93701f;
  --warn-soft: #f2e7c9;
  --mono: "IBM Plex Mono", ui-monospace, "SFMono-Regular", Menlo, Consolas, monospace;
  --display: "Fraunces Display", Georgia, serif;
  --body: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
}

@media (prefers-color-scheme: dark) {
  :root {
    --ink: #e8e6e1;
    --paper: #17181a;
    --paper-raised: #1f2124;
    --rule: #34363a;
    --muted: #9b968b;
    --accent: #e08a4f;
    --accent-soft: #3a2a1c;
    --good: #7fbf8e;
    --good-soft: #1c2b20;
    --bad: #e0847a;
    --bad-soft: #2e1e1c;
    --warn: #d9b45c;
    --warn-soft: #2e2811;
  }
}
:root[data-theme="dark"] {
  --ink: #e8e6e1; --paper: #17181a; --paper-raised: #1f2124; --rule: #34363a; --muted: #9b968b;
  --accent: #e08a4f; --accent-soft: #3a2a1c; --good: #7fbf8e; --good-soft: #1c2b20;
  --bad: #e0847a; --bad-soft: #2e1e1c; --warn: #d9b45c; --warn-soft: #2e2811;
}
:root[data-theme="light"] {
  --ink: #16191d; --paper: #f7f5f0; --paper-raised: #ffffff; --rule: #d8d3c7; --muted: #6b6455;
  --accent: #b5652c; --accent-soft: #f0ded0; --good: #3f6b4a; --good-soft: #e3ede4;
  --bad: #a13f36; --bad-soft: #f4e2df; --warn: #93701f; --warn-soft: #f2e7c9;
}

@font-face {
  font-family: "Fraunces Display";
  src: local("Georgia");
}

* { box-sizing: border-box; }
body {
  background: var(--paper);
  color: var(--ink);
  font-family: var(--body);
  line-height: 1.6;
  max-width: 920px;
  margin: 0 auto;
  padding: 4rem 1.5rem 6rem;
}
h1, h2, h3 { font-family: var(--display); text-wrap: balance; font-weight: 600; letter-spacing: -0.01em; }
h1 { font-size: 2.4rem; margin: 0 0 0.3rem; }
h2 { font-size: 1.5rem; margin: 3rem 0 0.2rem; border-top: 1px solid var(--rule); padding-top: 2rem; }
h3 { font-size: 1.1rem; margin: 1.8rem 0 0.6rem; color: var(--ink); }
p { max-width: 68ch; }
.eyebrow {
  font-family: var(--mono); font-size: 0.72rem; letter-spacing: 0.14em; text-transform: uppercase;
  color: var(--accent); margin: 0 0 0.8rem;
}
.subtitle { color: var(--muted); font-size: 1.05rem; margin: 0 0 2.5rem; max-width: 60ch; }
.meta-row { display: flex; gap: 1.5rem; flex-wrap: wrap; font-family: var(--mono); font-size: 0.78rem; color: var(--muted); margin-bottom: 2.5rem; }
.meta-row span b { color: var(--ink); }

.callout {
  background: var(--paper-raised); border: 1px solid var(--rule); border-left: 3px solid var(--accent);
  padding: 1rem 1.2rem; border-radius: 4px; margin: 1.2rem 0;
}
.callout.warn { border-left-color: var(--warn); }
.callout.good { border-left-color: var(--good); }

table { width: 100%; border-collapse: collapse; font-size: 0.88rem; margin: 1rem 0 1.6rem; }
.table-wrap { overflow-x: auto; }
th, td { text-align: left; padding: 0.55rem 0.7rem; border-bottom: 1px solid var(--rule); vertical-align: top; }
th { font-family: var(--mono); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--muted); font-weight: 500; }
td code, td .path { font-family: var(--mono); font-size: 0.82rem; color: var(--ink); background: var(--paper); padding: 0.1rem 0.35rem; border-radius: 3px; }
tbody tr:hover { background: var(--paper-raised); }
.num { font-variant-numeric: tabular-nums; }

.pill { display: inline-block; font-family: var(--mono); font-size: 0.7rem; padding: 0.15rem 0.5rem; border-radius: 999px; letter-spacing: 0.02em; }
.pill.critique { background: var(--bad-soft); color: var(--bad); }
.pill.majeur { background: var(--warn-soft); color: var(--warn); }
.pill.mineur { background: var(--good-soft); color: var(--good); }
.pill.done { background: var(--good-soft); color: var(--good); }
.pill.pending { background: var(--warn-soft); color: var(--warn); }

.gain-pos { color: var(--good); font-weight: 600; }
.gain-unk { color: var(--muted); font-style: italic; font-weight: 400; }

.phase { background: var(--paper-raised); border: 1px solid var(--rule); border-radius: 6px; padding: 1.4rem 1.5rem; margin: 1rem 0; }
.phase-head { display: flex; align-items: baseline; gap: 0.7rem; margin-bottom: 0.6rem; }
.phase-head .n { font-family: var(--mono); color: var(--accent); font-size: 0.85rem; }
.phase ul { margin: 0.4rem 0 0; padding-left: 1.2rem; }
.phase li { margin: 0.3rem 0; }

.fix-card { border: 1px solid var(--rule); border-radius: 6px; padding: 1rem 1.2rem; margin: 0.9rem 0; background: var(--paper-raised); }
.fix-card .file { font-family: var(--mono); font-size: 0.82rem; color: var(--accent); margin-bottom: 0.4rem; }
.fix-card .label { font-family: var(--mono); font-size: 0.68rem; text-transform: uppercase; color: var(--muted); letter-spacing: 0.08em; }

hr.divider { border: none; border-top: 1px solid var(--rule); margin: 2.5rem 0; }
footer { margin-top: 4rem; padding-top: 1.5rem; border-top: 1px solid var(--rule); font-family: var(--mono); font-size: 0.72rem; color: var(--muted); }
</style>

<p class="eyebrow">Audit de performance — AntiGaspiCI (Laravel 12)</p>
<h1>Pourquoi le CRUD des annonces et la connexion sont lents</h1>
<p class="subtitle">Analyse basée exclusivement sur la lecture du code du dépôt officiel <code>2026-06/backend</code>, croisée avec des tests d'exécution réels en local. Aucune cause n'est supposée sans preuve dans le code.</p>

<div class="meta-row">
  <span>Dépôt : <b>github.com/YANIXX35/2026-06</b></span>
  <span>Environnement de prod : <b>Render + PostgreSQL Aiven</b></span>
  <span>État : <b>corrections déployées (commit ba9b5ea)</b></span>
</div>

<div class="callout warn">
<b>Note de cadrage.</b> La demande d'audit mentionne SQLite en développement, un <code>ProductController</code>/<code>ListingController</code>, et Sanctum pour la connexion web. Vérification faite sur le code réel : la connexion par défaut du framework est <code>sqlite</code> (squelette Laravel), mais <code>.env.example</code> déclare <code>pgsql</code> et l'environnement local utilisé ici tourne en <code>mysql</code> ; la production est <b>PostgreSQL (Aiven)</b>. Il n'existe pas de <code>ProductController</code> ni de <code>ListingController</code> — le contrôleur concerné est <code>AnnonceController</code>. La connexion web utilise l'authentification par session Laravel classique (Sanctum n'existe que pour la couche API JSON consommée par l'app mobile). Le reste de ce rapport se base sur le code réel, pas sur le modèle générique de la demande.
</div>

<h2 id="resume">1 — Résumé exécutif</h2>
<p>La lenteur n'a pas une cause unique : c'est l'accumulation de plusieurs facteurs, dont la moitié touchait <i>toutes</i> les pages du site (pas seulement les annonces) et l'autre moitié des chemins précis (création d'annonce, dashboard, login). Trois facteurs dominaient :</p>
<div class="phase">
<ul>
<li><b>Aucun bytecode cache (opcache)</b> sur le serveur de production — chaque requête HTTP recompilait tout Laravel et ses ~1000 fichiers <code>vendor/</code> depuis zéro.</li>
<li><b>Connexion PostgreSQL non persistante</b> vers une base externe (Aiven, hors réseau interne Render) — chaque requête SQL payait une poignée de handshakes TCP/TLS en plus de la requête elle-même.</li>
<li><b>Requêtes redondantes ou non groupées</b> dans les layouts partagés, les dashboards et la création d'annonce (jusqu'à 15+ allers-retours DB par page dans le pire cas), chacune payant individuellement la latence réseau ci-dessus.</li>
</ul>
</div>
<p>19 correctifs ont été appliqués et déployés en production sur trois cycles de travail (voir §6). Les gains ne sont pas encore mesurés en conditions réelles de production (voir §7 pour les limites de mesure et la marche à suivre).</p>

<h2 id="temps">2 — Temps d'exécution mesurés avant optimisation</h2>
<p>Un seul chemin a pu être mesuré directement par exécution réelle (serveur + base de données locaux), lors d'un audit antérieur au 18/07 : la page de détail d'annonce, bloquée par une configuration Telescope défectueuse.</p>
<div class="table-wrap">
<table>
<tr><th>Opération</th><th>Mesure</th><th>Méthode</th></tr>
<tr><td><code class="path">/annonces/&#123;id&#125;</code> (avant fix Telescope)</td><td class="num">20 000 – 25 000 ms</td><td>Serveur Laravel local réel, chronométré</td></tr>
<tr><td><code class="path">/annonces/&#123;id&#125;</code> (après fix Telescope)</td><td class="num">1 200 – 1 800 ms</td><td>Serveur Laravel local réel, chronométré</td></tr>
</table>
</div>
<div class="callout">
<b>Limite de mesure assumée.</b> Je n'ai pas d'accès direct aux métriques Render (CPU, RAM, APM) ni à un shell de production depuis cet environnement. Les temps locaux mesurés pendant ce travail (4 à 11 s par page) ne sont <i>pas</i> représentatifs : Telescope est actif par défaut en local (<code>TELESCOPE_ENABLED</code> non positionné dans <code>.env</code> local, alors qu'il est explicitement désactivé en production par <code>start.sh</code>) et fausse toute mesure de temps locale, comme déjà démontré ci-dessus. Le tableau du §7 présente donc les gains attendus qualitativement plutôt que des millisecondes fabriquées. Si des chiffres précis sont nécessaires, la méthode la plus fiable est d'ajouter un en-tête <code>Server-Timing</code> ou un log de durée temporaire directement en production, le temps de quelques requêtes de test, puis de le retirer.
</div>

<h2 id="analyse">3 — Analyse technique détaillée</h2>
<div class="table-wrap">
<table>
<tr><th>Cause</th><th>Gravité</th><th>Fichier</th><th>Fonction</th></tr>
<tr>
  <td>Opcache absent en production (SAPI CLI, <code>opcache.enable_cli=0</code> par défaut)</td>
  <td><span class="pill critique">Critique</span></td>
  <td><span class="path">start.sh</span></td>
  <td>démarrage serveur</td>
</tr>
<tr>
  <td>Connexion PostgreSQL sans <code>PDO::ATTR_PERSISTENT</code> — handshake TCP/TLS à chaque requête vers Aiven</td>
  <td><span class="pill critique">Critique</span></td>
  <td><span class="path">config/database.php</span></td>
  <td>connexion <code>pgsql</code></td>
</tr>
<tr>
  <td>Migration de colonnes (<code>prix_original</code>, etc.) en échec silencieux sur Postgres (<code>-&gt;after()</code> non portable) + insert sans garde</td>
  <td><span class="pill critique">Critique</span></td>
  <td><span class="path">AnnonceController.php</span></td>
  <td><code>store()</code></td>
</tr>
<tr>
  <td>Dashboard fournisseur : boucle de 7 requêtes (1 par jour) au lieu d'une requête groupée</td>
  <td><span class="pill majeur">Majeur</span></td>
  <td><span class="path">AuthController.php</span></td>
  <td><code>dashboard()</code></td>
</tr>
<tr>
  <td>Login : double lecture <code>User::where(...)</code> (vérif statut) + relecture interne par <code>Auth::attempt()</code></td>
  <td><span class="pill majeur">Majeur</span></td>
  <td><span class="path">AuthController.php</span></td>
  <td><code>connecter()</code></td>
</tr>
<tr>
  <td>Accessor <code>getImpactMetricsAttribute()</code> : agrégation de commandes + réservations recalculée à chaque accès, sans cache</td>
  <td><span class="pill majeur">Majeur</span></td>
  <td><span class="path">app/Models/User.php</span></td>
  <td><code>getImpactMetricsAttribute()</code></td>
</tr>
<tr>
  <td>Upload photo : fallback Base64 (image brute) écrit directement en base distante quand Cloudinary indisponible/non configuré</td>
  <td><span class="pill majeur">Majeur</span></td>
  <td><span class="path">AnnonceController.php</span></td>
  <td><code>uploadImageCloud()</code></td>
</tr>
<tr>
  <td>Layouts partagés : <code>Categorie::all()</code> appelé 2× sans cache, <code>unreadNotifications-&gt;count()</code> charge toute la collection au lieu d'un COUNT</td>
  <td><span class="pill majeur">Majeur</span></td>
  <td><span class="path">layouts/app.blade.php</span>, <span class="path">front.blade.php</span>, <span class="path">dashboard.blade.php</span></td>
  <td>rendu de chaque page</td>
</tr>
<tr>
  <td>Absence d'index sur <code>annonces.statut</code>, <code>categorie_id</code>, <code>date_expiration</code>, <code>type_offre</code>, <code>photos.annonce_id</code></td>
  <td><span class="pill majeur">Majeur</span></td>
  <td><span class="path">database/migrations</span></td>
  <td>listing/filtre annonces</td>
</tr>
<tr>
  <td><code>Schema::hasColumn()</code> interrogé 3× à chaque création d'annonce (introspection SQL non cachée)</td>
  <td><span class="pill mineur">Mineur</span></td>
  <td><span class="path">AnnonceController.php</span></td>
  <td><code>store()</code></td>
</tr>
<tr>
  <td>Dashboard admin : 7 requêtes <code>count()</code> séparées, non cachées</td>
  <td><span class="pill mineur">Mineur</span></td>
  <td><span class="path">Admin/AdminController.php</span></td>
  <td><code>dashboard()</code></td>
</tr>
<tr>
  <td>Notification <code>NouvelleAnnonceCategorie</code> envoyée en boucle synchrone à chaque abonné de catégorie</td>
  <td><span class="pill mineur">Mineur</span></td>
  <td><span class="path">app/Notifications/NouvelleAnnonceCategorie.php</span></td>
  <td>déclenchée par <code>store()</code></td>
</tr>
<tr>
  <td>Scheduler (<code>schedule:work</code>) en boucle infinie consommant du CPU en continu, concurrent des workers web</td>
  <td><span class="pill mineur">Mineur</span></td>
  <td><span class="path">start.sh</span></td>
  <td>tâche de fond</td>
</tr>
</table>
</div>

<h2 id="racines">4 — Causes racines</h2>
<div class="phase">
<ul>
<li><b>Architecture serveur.</b> <code>php artisan serve</code> (serveur de développement PHP) fait tourner l'application en production, sans opcache ni gestion de concurrence avancée — un choix d'infrastructure risqué avant une soutenance, assumé et documenté plutôt que changé (migration vers php-fpm+nginx/Octane jugée trop risquée à ce stade par un audit antérieur).</li>
<li><b>Base de données distante hors réseau Render.</b> Chaque requête SQL vers Aiven paie une latence réseau réelle ; le code n'avait pas été écrit en tenant compte de ce coût (pas de connexion persistante, pas de regroupement de requêtes).</li>
<li><b>Fonctionnalités ajoutées rapidement sans revue de perf.</b> Plusieurs fonctionnalités récentes (paniers surprise, ventes flash, commission, impact écologique) ont chacune ajouté leur propre requête ou boucle sans réutiliser les mécanismes de cache déjà en place ailleurs dans le code.</li>
</ul>
</div>

<h2 id="plan">5 — Plan d'optimisation par priorité</h2>

<div class="phase">
<div class="phase-head"><span class="n">Phase 1</span><h3 style="margin:0">Corrections critiques</h3></div>
<ul>
<li><span class="pill done">Fait</span> Connexion PostgreSQL persistante (<code>config/database.php</code>)</li>
<li><span class="pill done">Fait</span> Opcache activé en CLI (<code>start.sh</code>, <code>nixpacks.toml</code>)</li>
<li><span class="pill done">Fait</span> Migration des colonnes réparée + garde <code>Schema::hasColumn</code> sécurisée</li>
<li><span class="pill done">Fait</span> Index DB sur <code>annonces</code>/<code>photos</code></li>
</ul>
</div>

<div class="phase">
<div class="phase-head"><span class="n">Phase 2</span><h3 style="margin:0">Optimisations importantes</h3></div>
<ul>
<li><span class="pill done">Fait</span> Dashboards (fournisseur, acheteur, admin) : requêtes groupées + mise en cache</li>
<li><span class="pill done">Fait</span> Login : requête unique au lieu de deux lectures utilisateur</li>
<li><span class="pill done">Fait</span> Accessor <code>getImpactMetricsAttribute()</code> mis en cache (10 min)</li>
<li><span class="pill done">Fait</span> Layouts : catégories en cache, comptage de notifications en COUNT direct</li>
<li><span class="pill done">Fait</span> Compression d'image avant stockage Base64 de secours</li>
<li><span class="pill pending">Restant</span> Confirmer si Cloudinary est réellement configuré en production (variables d'environnement absentes du <code>render.yaml</code> versionné) — sinon chaque upload emprunte systématiquement le chemin de secours</li>
</ul>
</div>

<div class="phase">
<div class="phase-head"><span class="n">Phase 3</span><h3 style="margin:0">Optimisations avancées</h3></div>
<ul>
<li><span class="pill pending">Restant</span> Notification <code>NouvelleAnnonceCategorie</code> déjà marquée <code>ShouldQueue</code>, mais <code>QUEUE_CONNECTION=sync</code> en production — sans passer à un driver asynchrone (<code>database</code>) et sans worker de queue actif (<code>php artisan queue:work</code> en tâche de fond, sur le même principe que le scheduler), la mise en file n'a aujourd'hui aucun effet réel sur la latence.</li>
<li><span class="pill pending">Restant</span> Migration du serveur d'application (<code>php artisan serve</code> → php-fpm+nginx ou FrankenPHP/Octane) — gain potentiel important mais changement d'infrastructure jugé trop risqué avant la soutenance.</li>
<li><span class="pill pending">Restant</span> Batch insert des lignes de commande dans <code>CommandeController</code>/<code>PaymentController</code> (actuellement une insertion par article, sous verrou <code>lockForUpdate</code>) — impact limité au panier multi-articles, non prioritaire.</li>
</ul>
</div>

<h2 id="corrections">6 — Corrections réalisées</h2>
<p>Déployées en 4 commits sur <code>origin/main</code> (<code>998f708</code> → <code>ba9b5ea</code>), chacun testé localement (lint PHP, exécution réelle des contrôleurs concernés, migration testée) avant envoi.</p>

<div class="fix-card">
<div class="file">config/database.php</div>
<p><span class="label">Justification</span> — chaque requête vers Aiven (hors réseau Render) rouvrait une connexion TCP+TLS complète.<br>
<span class="label">Impact attendu</span> — réduction de la latence fixe payée par <i>chaque</i> requête SQL de <i>chaque</i> page du site.</p>
</div>

<div class="fix-card">
<div class="file">start.sh, nixpacks.toml</div>
<p><span class="label">Justification</span> — aucun bytecode cache actif ; chaque requête HTTP recompilait tout le framework.<br>
<span class="label">Impact attendu</span> — gain transversal sur 100&nbsp;% du trafic, potentiellement le plus important de tous les correctifs.</p>
</div>

<div class="fix-card">
<div class="file">AnnonceController.php — store(), uploadImageCloud()</div>
<p><span class="label">Justification</span> — migration cassée en production (colonnes manquantes → erreur 500 systématique à la création d'annonce) ; upload photo synchrone sans compression.<br>
<span class="label">Impact attendu</span> — restaure la création d'annonce (bloquante avant ce fix) et réduit le poids des écritures image vers la base distante.</p>
</div>

<div class="fix-card">
<div class="file">AuthController.php — connecter(), dashboard()</div>
<p><span class="label">Justification</span> — double requête utilisateur au login ; 7 requêtes séquentielles + boucle journalière au dashboard.<br>
<span class="label">Impact attendu</span> — dashboard mis en cache 5 min (1 requête au lieu de ~15 lors d'un cache miss, 0 requête lourde lors d'un hit) ; login sans lecture redondante.</p>
</div>

<div class="fix-card">
<div class="file">app/Models/User.php — getImpactMetricsAttribute()</div>
<p><span class="label">Justification</span> — accessor recalculant intégralement l'historique de commandes/réservations à chaque accès.<br>
<span class="label">Impact attendu</span> — mis en cache 10 min ; élimine des requêtes lourdes répétées sur les pages affichant l'impact écologique.</p>
</div>

<div class="fix-card">
<div class="file">resources/views/layouts/{app,front,dashboard}.blade.php</div>
<p><span class="label">Justification</span> — catégories rechargées sans cache (2× sur une même page) ; comptage de notifications chargeant toute la collection en mémoire.<br>
<span class="label">Impact attendu</span> — 2 requêtes en moins par page, sur la quasi-totalité du site.</p>
</div>

<div class="fix-card">
<div class="file">Admin/AdminController.php — dashboard()</div>
<p><span class="label">Justification</span> — 7 requêtes <code>count()</code> séparées à chaque chargement.<br>
<span class="label">Impact attendu</span> — 3 requêtes utilisateur regroupées en 1 ; ensemble mis en cache 30 s.</p>
</div>

<div class="fix-card">
<div class="file">database/migrations/2026_07_23_200001_add_performance_indexes.php</div>
<p><span class="label">Justification</span> — filtres fréquents (statut, catégorie, expiration, type d'offre) sans index, scans complets probables sur PostgreSQL en croissance de volume.<br>
<span class="label">Impact attendu</span> — accélère le listing et les filtres d'annonces à mesure que la table grossit.</p>
</div>

<div class="fix-card">
<div class="file">app/Notifications/NouvelleAnnonceCategorie.php</div>
<p><span class="label">Justification</span> — notification envoyée en boucle synchrone à chaque abonné lors de la création d'une annonce.<br>
<span class="label">Impact attendu</span> — <code>ShouldQueue</code> ajouté ; effet réel conditionné à un changement de <code>QUEUE_CONNECTION</code> encore à faire (voir Phase 3).</p>
</div>

<h2 id="mesures">7 — Mesures après optimisation</h2>
<div class="table-wrap">
<table>
<tr><th>Opération</th><th>Avant</th><th>Après</th><th>Gain</th></tr>
<tr>
  <td><code class="path">/annonces/&#123;id&#125;</code></td>
  <td class="num">20 000 – 25 000 ms <span style="color:var(--muted)">(mesuré, cause Telescope)</span></td>
  <td class="num">1 200 – 1 800 ms <span style="color:var(--muted)">(mesuré)</span></td>
  <td class="gain-pos num">≈ -94 %</td>
</tr>
<tr>
  <td>Connexion utilisateur</td>
  <td class="num">non mesuré en prod</td>
  <td class="num">non mesuré en prod</td>
  <td class="gain-unk">à mesurer post-déploiement</td>
</tr>
<tr>
  <td>Création d'annonce</td>
  <td class="num">échec (500) puis lent une fois corrigé</td>
  <td class="num">non mesuré en prod</td>
  <td class="gain-unk">à mesurer post-déploiement</td>
</tr>
<tr>
  <td>Modification d'annonce</td>
  <td class="num">non mesuré en prod</td>
  <td class="num">non mesuré en prod</td>
  <td class="gain-unk">à mesurer post-déploiement</td>
</tr>
<tr>
  <td>Suppression d'annonce</td>
  <td class="num">non mesuré en prod</td>
  <td class="num">non mesuré en prod</td>
  <td class="gain-unk">à mesurer post-déploiement</td>
</tr>
<tr>
  <td>Chargement dashboard</td>
  <td class="num">~10-15 requêtes séquentielles par vue (mesuré en nombre de requêtes, pas en ms)</td>
  <td class="num">1 requête (cache miss) / 0 requête lourde (cache hit)</td>
  <td class="gain-pos">réduction de requêtes confirmée par lecture de code + test local</td>
</tr>
</table>
</div>
<div class="callout good">
Pour obtenir les colonnes manquantes avec des millisecondes réelles : demander (ou effectuer) un test de charge simple sur le site déployé — par exemple <code>curl -w "%{time_total}"</code> sur chaque route depuis un poste externe, avant/après un déploiement donné, ou activer temporairement un middleware de chronométrage qui logue la durée de chaque requête. Je peux l'ajouter si utile, en le retirant ensuite pour ne pas laisser de code de debug en production.
</div>

<footer>
Rapport généré à partir de l'analyse du dépôt <code>2026-06/backend</code> — commits <code>998f708</code> à <code>ba9b5ea</code>. Aucune donnée chiffrée n'a été inventée : chaque valeur est soit mesurée par exécution réelle, soit explicitement marquée comme non mesurée.
</footer>

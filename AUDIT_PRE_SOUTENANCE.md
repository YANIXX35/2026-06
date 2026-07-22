# Audit pré-soutenance AntiGaspiCI — rapport

Rapport produit à partir de deux documents fournis (`claude.md` : cahier de vérification technique et
fonctionnelle avec checklist §6 et recommandations §7 ; `2026-06/` : code source), croisés avec une lecture
directe du code, l'historique git, et une exécution locale réelle (serveur + base de données MySQL locale)
pour vérifier plusieurs constats plutôt que les déduire du code seul.

Date : 18/07/2026.

---

## 1. Quelle implémentation est réellement déployée ?

Confirmé : il existe **trois** codebases backend sur la machine (le premier audit n'en connaissait que deux) :

| Dossier | État réel |
|---|---|
| `backend/` (racine `yao_M`) | Copie de travail **sans aucun historique git**, README Laravel générique non personnalisé, aucune config de déploiement. Prototype antérieur. |
| `antigaspi-deploy/` | Laravel API + Angular (architecture découplée). Dernière activité réelle fin avril/mi-mai 2026. Prototype d'architecture, non maintenu. |
| **`2026-06/backend`** | Dépôt git réel (`github.com/YANIXX35/2026-06`), **68 commits**, dernier le 16/07/2026. Sur-ensemble strict de `backend/`. **C'est la version officielle**, déployée sur Render (service auto-nommé `two026-06-3`, cf. §7 ci-dessous pour l'incohérence de nommage). |
| **`2026-06/mobile`** | Application Expo/React Native, 17 écrans, 3 rôles (acheteur/fournisseur/admin), consomme l'API de `2026-06/backend`. Réelle et fonctionnelle, mais **aucun commit depuis le 25/06** (23 jours) et **absente du mémoire** (cf. §5). |

Action prise : `STATUT_PROJET.md` ajouté à la racine de `backend/` et `antigaspi-deploy/`, indiquant explicitement
qu'il s'agit de prototypes antérieurs et pointant vers `2026-06/` comme version officielle.

---

## 2. Checklist §6 du cahier de vérification — état après corrections

| # | Point | Verdict initial | Statut après ce passage |
|---|---|---|---|
| 1 | Une seule implémentation active déployée, l'autre documentée | Non conforme | **Corrigé** — `STATUT_PROJET.md` ajouté dans `backend/` et `antigaspi-deploy/` |
| 2 | Requête accueil = requête `/annonces` | Non conforme (cache 5 min désynchronisé) | **Corrigé** — `AnnonceObserver`/`ReservationObserver` invalident le cache accueil dès qu'une annonce/réservation change de statut, au lieu d'attendre le TTL |
| 3 | `/annonces/{id}` répond sans timeout | Non conforme | **Cause réelle trouvée et corrigée en local** (voir §4) + mitigation de production ajoutée |
| 4 | Catégories = source unique | Non conforme | **Corrigé** — bandeau d'accueil (`welcome.blade.php`) tiré de `$allCats` (table `categories`) au lieu d'un tableau codé en dur |
| 5 | Aucun paiement actif ne contredit « gratuit » | Non conforme | **Corrigé côté discours** — FAQ réécrite (don gratuit / vente payante via Wave-Moov Money simulé). Le module panier/paiement reste actif (décision produit, cf. §6) |
| 6 | Liens légaux réels ou marqués TODO | Non conforme | **Corrigé** — pages Mentions légales / Confidentialité / CGU créées et branchées (footer ×2, page d'inscription). Numéro de téléphone factice explicitement marqué « (exemple) ». Réseaux sociaux : non traités, cf. §6 |
| 7 | Blog recentré alimentation/gaspillage | Déjà corrigé côté flux RSS, UI non alignée | **Corrigé** — le filtre de sources et le titre de la page référençaient encore OMS/Le Monde Santé/Futura/Santé Magazine (dead code jamais mis à jour après le recentrage des flux) ; remplacés par les vraies sources (ADEME, Actu-Environnement) |
| 8 | Chatbot documenté cohérent avec le mémoire | Conforme techniquement (vrai appel Gemini) | Cohérence avec le mémoire : voir §5 |
| 9 | Indicateurs d'impact calculés ou assumés comme démo | Non conforme | **Corrigé** — CO2/kg/repas/FCFA calculés à partir des réservations réellement complétées, au lieu d'un tableau codé en dur |
| 10 | Absence de code mort | Non conforme | **Documenté** (README) pour les deux prototypes abandonnés. `layouts/app.blade.php` vs `layouts/front.blade.php` : **vérifié, ce n'est pas du code mort** — deux layouts réellement utilisés (15 et 11 vues respectivement), pas de suppression faite |
| 11-12 | Cohérence mémoire (données, UML) vs code | À déterminer | **Traité, voir §5** — écart réel identifié sur l'architecture |

---

## 3. Correctifs appliqués — détail par fichier

**Cache / cohérence des compteurs**
- `app/Observers/AnnonceObserver.php`, `app/Observers/ReservationObserver.php` (nouveaux) + `AppServiceProvider.php` : invalidation du cache accueil (`welcome_stats`, `welcome_annonces`, `welcome_categories`, `welcome_impact`) à chaque changement de statut d'une annonce ou d'une réservation.

**Catégories**
- `resources/views/welcome.blade.php` : bandeau défilant remplacé par une boucle sur `$allCats` (déjà calculé par `WelcomeController` mais jusqu'ici inutilisé à cet endroit).

**Modèle économique**
- `resources/views/pages/comment-ca-marche.blade.php` : FAQ « inscription gratuite » et « comment fonctionne le paiement » réécrites pour refléter le code réel (don gratuit, vente payante via Wave/Moov Money **simulé** — la simulation était déjà affichée dans l'écran de paiement lui-même, seule la FAQ ne le disait pas).

**Liens légaux et contact**
- `routes/web.php` : routes `/mentions-legales`, `/confidentialite`, `/cgu`, `/newsletter`.
- `resources/views/pages/mentions-legales.blade.php`, `confidentialite.blade.php`, `cgu.blade.php` (nouveaux) : contenu réel, assumé comme prototype académique.
- `layouts/front.blade.php`, `welcome.blade.php`, `auth/inscription.blade.php` : liens `href="#"` remplacés par les routes ci-dessus.
- `layouts/app.blade.php` : liens « Aide »/« Support » (`href="#"`) reliés à des pages réelles ; numéro de téléphone factice marqué « (exemple) ».
- `resources/views/pages/contact.blade.php` : numéro de téléphone factice non cliquable + note explicite « coordonnées d'exemple ».
- `resources/views/annonces/show.blade.php` : boutons de partage (Facebook/Twitter/WhatsApp) connectés à de vrais intents de partage au lieu de `href="#"`.

**Blog**
- `app/Http/Controllers/BlogController.php`, `resources/views/blog/index.blade.php` : le filtre de sources et le titre de page référençaient encore OMS/Le Monde Santé/Futura Santé/Santé Magazine — des libellés obsolètes jamais mis à jour lors du recentrage des flux RSS vers ADEME/Actu-Environnement (commit `a2fd9f6`). Corrigé pour utiliser les vraies sources.

**Indicateurs d'impact**
- `app/Http/Controllers/WelcomeController.php`, `resources/views/welcome.blade.php` : CO2 évité, kg sauvés, repas équivalents et FCFA valorisés calculés à partir des réservations `complétée` (seule transition de statut représentant un échange réellement abouti dans le modèle de données actuel). Facteurs de conversion (kg→repas, kg→CO2) documentés en commentaire comme des ordres de grandeur, pas des mesures précises.

**Newsletter**
- Migration `newsletter_subscribers`, modèle `NewsletterSubscriber`, `NewsletterController`, route `/newsletter` (nouveaux). Les deux formulaires footer (jusque-là de simples `<input>` sans `<form>`) sont maintenant fonctionnels.

**Sécurité / configuration production**
- `render.yaml`, `.env.example`, `start.sh` : `APP_DEBUG` forcé à `false` (était `true` en production — fuite potentielle de stack traces).
- `render.yaml` : commentaire ajouté documentant l'écart entre le nom de service déclaré (`antigaspi-2026`) et le nom réellement provisionné par Render (`two026-06-3`, visible dans `2026-06/mobile/src/constants/index.ts`).

---

## 4. `/annonces/{id}` : cause trouvée par reproduction locale

Deux bugs distincts ont été identifiés et corrigés, avec vérification par exécution réelle (serveur Laravel
local + MySQL local), pas seulement par lecture de code :

1. **`app/Http/Controllers/AnnonceController.php`** — la requête de tri des annonces urgentes utilisait la
   syntaxe PostgreSQL `NOW() + INTERVAL '24 hours'`, invalide sur MySQL. Corrigé en remplaçant `NOW()`/`INTERVAL`
   par des bornes de temps liées en paramètres (`now()`, `now()->addHours(24)`), portable entre drivers.
2. **`config/telescope.php`** — la connexion de stockage de Laravel Telescope était **codée en dur sur `pgsql`**
   (`env('TELESCOPE_DB_CONNECTION', 'pgsql')`), indépendamment de la connexion réellement utilisée par l'app.
   En local (MySQL, sans extension `pdo_pgsql`), Telescope échouait silencieusement à chaque requête en fin de
   cycle de vie, avec une exception `could not find driver` et l'écriture d'un log JSON contenant l'intégralité
   des requêtes/vues de la page. **Mesuré avant correctif : `/annonces` répondait en 20 à 25 secondes** (contre
   ~1 seconde pour une page statique) ; **après correctif : ~1,2 à 1,8 seconde**, y compris pour une page
   d'annonce individuelle (`/annonces/3` : 1,4 s).

Important : en production, `TELESCOPE_ENABLED=false` est forcé sans condition par `start.sh`, donc ce bug
Telescope précis ne devrait **pas** être la cause du timeout observé sur le site déployé — mais il rendait le
développement local pratiquement inutilisable, ce qui aurait rendu très difficile tout diagnostic ou correction
supplémentaire avant la soutenance. Le corriger était un préalable nécessaire au reste de cet audit.

Pour le timeout observé **en production**, l'hypothèse la plus probable reste :
`start.sh` lance `php artisan serve`, le serveur de développement intégré de PHP, **mono-thread par défaut** —
une requête lente (ex. le chat IA qui garde une connexion streaming ouverte plusieurs secondes) bloque toutes
les autres requêtes derrière elle, ce qui a été reproduit localement avec `/blog` (appel réseau vers 4 flux RSS)
bloquant les requêtes suivantes sur le serveur de dev. Mitigation appliquée : `PHP_CLI_SERVER_WORKERS=4` dans
`start.sh` pour paralléliser les requêtes. Ce n'est pas un serveur de production à part entière — une migration
vers php-fpm+nginx ou FrankenPHP/Octane a été jugée trop risquée à quelques jours d'une soutenance et n'a pas
été faite ; à envisager après la soutenance si le trafic le justifie. Le cold start Render (plan gratuit) reste
une cause plausible additionnelle, hors du contrôle du code.

---

## 5. Mémoire vs code — écart identifié (points 11-12)

Comparaison entre `MEMOIRE_PAGINE6_V2.docx` (daté du 26/05/2026) et le code réel de `2026-06/backend`
(dernier commit 16/07/2026), en se concentrant sur le chapitre Architecture et le chapitre Implémentation.

### Écart majeur : l'architecture décrite n'est pas celle qui est déployée

Le chapitre 6 du mémoire décrit explicitement une architecture **découplée** :

> « Séparation claire des responsabilités : le backend développé avec Laravel 12 expose une API REST qui gère
> la logique métier ainsi que l'accès aux données, tandis que le frontend développé avec Angular 21 se charge
> exclusivement de l'interface utilisateur (...) Couche Présentation (Frontend — Angular 21) (...) Couche
> Données (Base de données — MySQL) (...) Angular 21 ←→ (Requêtes HTTP / JSON) ←→ API REST Laravel 12 ←→
> (Eloquent ORM) ←→ Base de données MySQL »

Or le site réellement déployé (`two026-06-3.onrender.com`, servi par `2026-06/backend`) est un **monolithe
Blade rendu côté serveur**, avec authentification par session pour le site web (Sanctum n'est utilisé que pour
la couche API JSON consommée par l'app mobile), et une base **PostgreSQL** (Aiven), pas MySQL. Le mémoire décrit
en réalité l'architecture d'`antigaspi-deploy/` (Laravel API + Angular) — le prototype non retenu — pas celle du
produit officiel. C'est cohérent avec un constat déjà fait dans l'audit boîte noire initial (présence d'un jeton
CSRF Blade sur chaque page), désormais confirmé côté code.

Le mémoire mentionne aussi **Pusher** comme mécanisme de notifications temps réel (chapitre 8) : aucune trace de
`pusher/pusher-php-server` ni de configuration de broadcasting dans `2026-06/backend/composer.json` — les
notifications réelles passent par le système de notifications base de données de Laravel, pas par du WebSocket
temps réel.

**Recommandation** : avant la soutenance, mettre à jour le chapitre Architecture pour décrire le monolithe
Blade/PostgreSQL effectivement soutenu, et soit retirer la mention Pusher soit l'implémenter réellement. C'est
la correction la plus importante de cette section — c'est le point qu'un jury testant le site en direct (jeton
CSRF visible dans le code source HTML, pas de séparation front/back visible) remarquera le plus facilement.

### Écart secondaire, mais à corriger facilement : le mobile et le paiement Wave/Moov sont déjà réalisés, pas seulement « perspectives »

Le mémoire présente comme perspectives futures des éléments déjà construits :

> Introduction/résumé : « (...) pourra évoluer vers une application mobile, l'intégration d'un système de
> paiement mobile money (...) »
> Chapitre 8, perspectives : « Intégration d'un système de paiement en ligne : ajout des solutions de paiement
> Mobile Money telles que Orange Money, Wave et MTN MoMo afin de faciliter les transactions financières. »

Or l'application mobile (`2026-06/mobile`, 17 écrans) et le paiement Wave/Moov Money (simulé) existent déjà dans
le code déployé. C'est une bonne nouvelle pour la soutenance (le projet est allé plus loin que ce que le mémoire
documente), mais tel quel, le texte contredit ce qu'un jury verra dans la démo live. **Recommandation** :
déplacer ces deux points de « Perspectives » vers une section « Réalisations postérieures à la rédaction »,
ou les intégrer directement au chapitre Implémentation.

### Points vérifiés et cohérents (pas de changement nécessaire)

- Diagramme des cas d'utilisation (4 acteurs : Visiteur, Acheteur, Fournisseur, Administrateur) : cohérent avec les rôles réels du code.
- Diagramme de classes (Commande, CommandeItem, AbonnementCategorie, Notification, Signalement) : cohérent avec les modèles réels.
- Chapitre 8 « Espace d'administration » (gestion utilisateurs, annonces, signalements, catégories, statistiques) : cohérent avec `AdminController`.
- Géolocalisation via OpenStreetMap/Leaflet : confirmé utilisé dans le code (`annonces/index.blade.php`, `create.blade.php`, `edit.blade.php`).
- OTP par e-mail : confirmé implémenté (`PasswordResetOtp`, écrans d'inscription).

---

## 6. Points volontairement non traités — décisions à prendre

- **Réseaux sociaux (`href="#"`)** dans les 3 footers : non corrigés, faute de comptes réels à renseigner. Soit fournir de vraies URLs, soit les retirer avant la soutenance (les laisser en l'état reproduit le problème initial signalé par le PRD).
- **Coordonnées de contact** (adresse « Plateau, Abidjan », e-mails `contact@antigaspi.ci` / `contact@antigaspi-ci.com` — deux domaines différents utilisés selon les pages) : marquées comme exemples, non unifiées ni remplacées par de vraies coordonnées.
- **Double mécanisme de transaction** : le code confirme la coexistence de deux parcours parallèles pour acquérir un même surplus — réservation directe (`ReservationController`, historique du produit) et panier/commande (`CartController`/`PaymentController`, ajouté plus tard pour l'app mobile). La page de détail d'une annonce propose les deux boutons (« Réserver directement » et « Ajouter au panier »). Un garde-fou existe déjà (le paiement vérifie que l'annonce est toujours `disponible` avant de finaliser une commande), donc pas de bug de double-réservation constaté, mais c'est exactement la tension « réservation simple vs panier multi-annonces » déjà signalée dans le premier PRD — à assumer explicitement dans le mémoire plutôt qu'à laisser comme un flou.
- **Serveur de production** : mitigation légère appliquée (`PHP_CLI_SERVER_WORKERS`), pas de passage à php-fpm+nginx/Octane — jugé trop risqué avant la soutenance.
- **Paiement Wave/Moov simulé sans passerelle réelle** : assumé comme démonstration académique (déjà indiqué dans l'UI existante), pas d'intégration réelle faite.

---

## 7. Vérification effectuée

- Lint PHP (`php -l`) sur tous les fichiers modifiés/créés : OK.
- `php artisan migrate --force` en local (MySQL) : la nouvelle migration `newsletter_subscribers` passe sans erreur.
- Serveur Laravel local démarré réellement (`php artisan serve`), pages testées en conditions réelles :
  `/`, `/annonces`, `/annonces/3` (annonce réelle), `/blog`, `/mentions-legales`, `/confidentialite`, `/cgu` —
  toutes en 200, temps de réponse normaux après le correctif Telescope (voir §4 pour les mesures avant/après).
  Rendu vérifié : bandeau de catégories dynamique, indicateurs d'impact non nuls, formulaire newsletter avec la
  bonne action, sources du blog = ADEME/Actu-Environnement.
- `git status` sur le dépôt `2026-06` passé en revue : seuls les fichiers listés en §3 sont modifiés/créés,
  aucune modification involontaire ailleurs.
- Aucun commit ni push effectué — à faire manuellement après relecture.

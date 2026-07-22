<?php

namespace Tests\Feature\AuditFonctionnel;

use App\Models\Categorie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\AuditFonctionnel\Concerns\SeedsTestData;
use Tests\TestCase;

/**
 * Section B du plan de test — Annonces, consultation publique.
 */
class AnnoncesPubliquesTest extends TestCase
{
    use RefreshDatabase;
    use SeedsTestData;

    public function test_accueil_et_annonces_montrent_le_meme_total_disponible(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        for ($i = 0; $i < 5; $i++) {
            $this->makeAnnonce($fournisseur);
        }

        $home = $this->get('/');
        $home->assertStatus(200);
        $home->assertViewHas('nbAnnonces', 5);

        $listing = $this->get('/annonces');
        $listing->assertStatus(200);
        $this->assertSame(5, $listing->viewData('annonces')->total());
    }

    public function test_listing_sans_filtre_ne_montre_que_les_annonces_disponibles(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $this->makeAnnonce($fournisseur, ['statut' => 'disponible']);
        $this->makeAnnonce($fournisseur, ['statut' => 'reservé']);
        $this->makeAnnonce($fournisseur, ['statut' => 'supprimé']);

        $listing = $this->get('/annonces');
        $this->assertSame(1, $listing->viewData('annonces')->total());
    }

    public function test_filtre_par_type_offre(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $this->makeAnnonce($fournisseur, ['type_offre' => 'don', 'prix' => 0]);
        $this->makeAnnonce($fournisseur, ['type_offre' => 'vente']);

        $response = $this->get('/annonces?type_offre=don');
        $annonces = $response->viewData('annonces');
        $this->assertSame(1, $annonces->total());
        $this->assertSame('don', $annonces->first()->type_offre);
    }

    public function test_filtre_par_categorie(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $catA = Categorie::where('nom', 'Fruits & Légumes')->first();
        $catB = Categorie::where('nom', 'Boissons')->first();
        $this->makeAnnonce($fournisseur, ['categorie_id' => $catA->id]);
        $this->makeAnnonce($fournisseur, ['categorie_id' => $catB->id]);

        $response = $this->get('/annonces?categorie=' . $catB->id);
        $annonces = $response->viewData('annonces');
        $this->assertSame(1, $annonces->total());
        $this->assertSame($catB->id, $annonces->first()->categorie_id);
    }

    public function test_filtre_par_ville(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $this->makeAnnonce($fournisseur, ['adresse_collecte' => 'Cocody, Abidjan']);
        $this->makeAnnonce($fournisseur, ['adresse_collecte' => 'Bouaké centre']);

        $response = $this->get('/annonces?ville=Bouaké');
        $annonces = $response->viewData('annonces');
        $this->assertSame(1, $annonces->total());
    }

    /**
     * Le sélecteur de tri (annonces/index.blade.php:328-331) envoie un paramètre
     * `tri` (recent/prix_asc/prix_desc) en query string, mais AnnonceController::index()
     * ne lit jamais `request('tri')` — seul un tri fixe (urgence puis date) est appliqué.
     * Ce test vérifie le comportement attendu par le plan de test ; un échec confirme
     * l'anomalie (tri non fonctionnel), pas un bug du test.
     */
    public function test_tri_par_prix_croissant(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $this->makeAnnonce($fournisseur, ['titre' => 'Cher', 'prix' => 5000]);
        $this->makeAnnonce($fournisseur, ['titre' => 'Pas cher', 'prix' => 100]);

        $response = $this->get('/annonces?tri=prix_asc');
        $prix = $response->viewData('annonces')->pluck('prix')->map(fn ($p) => (float) $p)->values();

        $this->assertSame([100.0, 5000.0], $prix->toArray(), 'Le tri "prix_asc" ne réordonne pas les résultats — paramètre "tri" jamais lu côté contrôleur.');
    }

    public function test_recherche_texte_libre(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $this->makeAnnonce($fournisseur, ['titre' => 'Bananes plantain']);
        $this->makeAnnonce($fournisseur, ['titre' => 'Riz local']);

        $response = $this->get('/annonces?q=plantain');
        $annonces = $response->viewData('annonces');
        $this->assertSame(1, $annonces->total());
    }

    public function test_recherche_sans_resultat_ne_provoque_pas_erreur(): void
    {
        $this->makeCategories();
        $response = $this->get('/annonces?q=xyznotfound');
        $response->assertStatus(200);
        $this->assertSame(0, $response->viewData('annonces')->total());
    }

    public function test_carte_geolocalisee_contient_les_annonces_avec_coordonnees(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $this->makeAnnonce($fournisseur, ['latitude' => 5.359952, 'longitude' => -4.008256]);
        $this->makeAnnonce($fournisseur, ['latitude' => null, 'longitude' => null]);

        $response = $this->get('/annonces');
        $geo = $response->viewData('annoncesGeo');
        $this->assertCount(1, $geo);
    }

    public function test_fiche_annonce_charge_avec_les_informations(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $annonce = $this->makeAnnonce($fournisseur, ['titre' => 'Tomates fraîches']);

        $response = $this->get('/annonces/' . $annonce->id);
        $response->assertStatus(200);
        $response->assertSee('Tomates fraîches');
    }

    public function test_annonce_deja_marquee_expiree_disparait_du_listing(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $this->makeAnnonce($fournisseur, ['statut' => 'expiré', 'date_expiration' => now()->subDay()]);

        $response = $this->get('/annonces');
        $this->assertSame(0, $response->viewData('annonces')->total());
    }

    /**
     * Cas réaliste : la date d'expiration est dépassée mais la commande planifiée
     * `annonces:expirer` (exécutée toutes les heures) n'est pas encore passée dessus —
     * le statut est donc encore "disponible". Le listing ne filtre jamais par date
     * directement (seul le statut est vérifié), donc l'annonce reste visible jusqu'au
     * prochain passage du cron. Documente un délai réel, pas un bug de test.
     */
    public function test_annonce_expiree_mais_pas_encore_traitee_par_le_cron_reste_visible(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $this->makeAnnonce($fournisseur, ['statut' => 'disponible', 'date_expiration' => now()->subMinute()]);

        $response = $this->get('/annonces');
        $this->assertSame(
            1,
            $response->viewData('annonces')->total(),
            'Comportement attendu documenté : reste visible jusqu\'au prochain passage de la commande annonces:expirer (jusqu\'à 1h de délai).'
        );
    }

    public function test_compteur_de_vues_s_incremente(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $annonce = $this->makeAnnonce($fournisseur, ['vues' => 0]);

        $this->get('/annonces/' . $annonce->id);
        $this->assertSame(1, $annonce->fresh()->vues);

        $this->get('/annonces/' . $annonce->id);
        $this->assertSame(2, $annonce->fresh()->vues);
    }
}

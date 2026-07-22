<?php

namespace Tests\Feature\AuditFonctionnel;

use App\Models\CommandeItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\AuditFonctionnel\Concerns\SeedsTestData;
use Tests\TestCase;

/**
 * Section C du plan de test — Espace Fournisseur.
 */
class FournisseurTest extends TestCase
{
    use RefreshDatabase;
    use SeedsTestData;

    public function test_creer_une_annonce_avec_photo(): void
    {
        Storage::fake('public');
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $categorie = \App\Models\Categorie::first();

        $response = $this->actingAs($fournisseur)->post('/annonces', [
            'titre' => 'Surplus de pain', 'description' => 'Invendus du jour',
            'categorie_id' => $categorie->id, 'quantite' => 5, 'unite' => 'kg',
            'prix' => 300, 'type_offre' => 'vente', 'adresse_collecte' => 'Marcory',
            'photos' => [UploadedFile::fake()->create('pain.jpg', 100, 'image/jpeg')],
        ]);

        $this->assertDatabaseHas('annonces', ['titre' => 'Surplus de pain', 'user_id' => $fournisseur->id]);
        $annonce = \App\Models\Annonce::where('titre', 'Surplus de pain')->first();
        $response->assertRedirect(route('annonces.show', $annonce));

        // Sans clé Cloudinary locale, le contrôleur bascule sur le stockage local (fallback déjà prévu dans le code).
        $this->assertDatabaseHas('photos', ['annonce_id' => $annonce->id, 'is_principale' => true]);

        // Visible immédiatement dans le listing public
        $this->get('/annonces')->assertSee('Surplus de pain');
    }

    public function test_modifier_une_annonce_repercute_partout(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $annonce = $this->makeAnnonce($fournisseur, ['titre' => 'Ancien titre']);
        $categorie = \App\Models\Categorie::first();

        $this->actingAs($fournisseur)->put('/annonces/' . $annonce->id, [
            'titre' => 'Nouveau titre', 'description' => 'maj', 'categorie_id' => $categorie->id,
            'quantite' => 8, 'unite' => 'kg', 'prix' => 400, 'type_offre' => 'vente',
            'statut' => 'disponible',
        ]);

        $this->get('/annonces')->assertSee('Nouveau titre');
        $this->get('/annonces/' . $annonce->id)->assertSee('Nouveau titre');
    }

    public function test_cloturer_une_annonce_disparait_du_listing_public(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $annonce = $this->makeAnnonce($fournisseur, ['titre' => 'A clôturer']);
        $categorie = \App\Models\Categorie::first();

        $this->actingAs($fournisseur)->put('/annonces/' . $annonce->id, [
            'titre' => 'A clôturer', 'description' => 'x', 'categorie_id' => $categorie->id,
            'quantite' => 1, 'unite' => 'kg', 'prix' => 100, 'type_offre' => 'vente',
            'statut' => 'expiré',
        ]);

        $response = $this->get('/annonces');
        $response->assertDontSee('A clôturer');
    }

    public function test_supprimer_une_annonce_disparait_du_listing_public(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $annonce = $this->makeAnnonce($fournisseur, ['titre' => 'A supprimer']);

        $this->actingAs($fournisseur)->delete('/annonces/' . $annonce->id);

        $this->assertSame('supprimé', $annonce->fresh()->statut);
        $this->get('/annonces')->assertDontSee('A supprimer');
    }

    public function test_renouveler_une_annonce_applique_une_nouvelle_date_expiration(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $annonce = $this->makeAnnonce($fournisseur, ['date_expiration' => now()->addHours(2)]);
        $categorie = \App\Models\Categorie::first();
        $nouvelleDate = now()->addDays(7);

        $this->actingAs($fournisseur)->put('/annonces/' . $annonce->id, [
            'titre' => $annonce->titre, 'description' => 'x', 'categorie_id' => $categorie->id,
            'quantite' => 1, 'unite' => 'kg', 'prix' => 100, 'type_offre' => 'vente',
            'statut' => 'disponible', 'date_expiration' => $nouvelleDate->format('Y-m-d\TH:i'),
        ]);

        $this->assertTrue($annonce->fresh()->date_expiration->gt(now()->addDays(6)));
    }

    public function test_dashboard_mes_annonces_scope_au_fournisseur_connecte(): void
    {
        $this->makeCategories();
        $moi = $this->makeFournisseur();
        $autre = $this->makeFournisseur();
        $this->makeAnnonce($moi, ['titre' => 'La mienne']);
        $this->makeAnnonce($autre, ['titre' => 'Celle d\'un autre']);

        $response = $this->actingAs($moi)->get('/mes-annonces');
        $response->assertSee('La mienne');
        $response->assertDontSee('Celle d\'un autre');
    }

    public function test_reception_et_traitement_d_une_commande(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $acheteur = $this->makeAcheteur();
        $annonce = $this->makeAnnonce($fournisseur);

        $this->actingAs($acheteur)->post('/annonces/' . $annonce->id . '/panier', ['quantite' => 2]);
        $this->actingAs($acheteur)->post('/commandes', []);

        $item = CommandeItem::where('fournisseur_id', $fournisseur->id)->first();
        $this->assertNotNull($item, 'Aucun CommandeItem créé pour le fournisseur après passage de commande.');
        $this->assertSame('en_attente', $item->statut);

        $this->actingAs($fournisseur)->get('/fournisseur/commandes')->assertSee($annonce->titre);

        $this->actingAs($fournisseur)->post('/commandes/items/' . $item->id . '/accepter');
        $this->assertSame('accepté', $item->fresh()->statut);
    }

    public function test_refuser_une_commande_recue(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $acheteur = $this->makeAcheteur();
        $annonce = $this->makeAnnonce($fournisseur);

        $this->actingAs($acheteur)->post('/annonces/' . $annonce->id . '/panier', ['quantite' => 1]);
        $this->actingAs($acheteur)->post('/commandes', []);
        $item = CommandeItem::where('fournisseur_id', $fournisseur->id)->first();

        $this->actingAs($fournisseur)->post('/commandes/items/' . $item->id . '/refuser');
        $this->assertSame('refusé', $item->fresh()->statut);
    }

    /**
     * Vérifie si l'annonce devient indisponible après une commande passée dessus
     * (empêche la revente du même surplus à plusieurs acheteurs). Un échec confirme
     * une anomalie applicative réelle (aucune mise à jour de statut après commande.passer()),
     * pas un bug de test — voir CommandeController::passer().
     */
    public function test_annonce_devient_indisponible_apres_une_commande(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $acheteur = $this->makeAcheteur();
        $annonce = $this->makeAnnonce($fournisseur, ['quantite' => 5]);

        $this->actingAs($acheteur)->post('/annonces/' . $annonce->id . '/panier', ['quantite' => 5]);
        $this->actingAs($acheteur)->post('/commandes', []);

        $this->assertNotSame(
            'disponible',
            $annonce->fresh()->statut,
            'L\'annonce reste "disponible" après une commande complète du stock — risque de survente du même surplus.'
        );
    }
}

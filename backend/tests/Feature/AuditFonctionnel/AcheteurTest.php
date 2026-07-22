<?php

namespace Tests\Feature\AuditFonctionnel;

use App\Models\Categorie;
use App\Models\Commande;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\AuditFonctionnel\Concerns\SeedsTestData;
use Tests\TestCase;

/**
 * Section D du plan de test — Espace Acheteur.
 */
class AcheteurTest extends TestCase
{
    use RefreshDatabase;
    use SeedsTestData;

    public function test_ajouter_une_annonce_au_panier(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $acheteur = $this->makeAcheteur();
        $annonce = $this->makeAnnonce($fournisseur, ['quantite' => 10, 'prix' => 500]);

        $response = $this->actingAs($acheteur)->post('/annonces/' . $annonce->id . '/panier', ['quantite' => 3]);
        $response->assertSessionHas('cart_success');
        $this->assertDatabaseHas('cart_items', ['user_id' => $acheteur->id, 'annonce_id' => $annonce->id, 'quantite' => 3]);
    }

    public function test_consulter_le_panier_contenu_et_total(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $acheteur = $this->makeAcheteur();
        $annonce = $this->makeAnnonce($fournisseur, ['quantite' => 10, 'prix' => 500]);
        $this->actingAs($acheteur)->post('/annonces/' . $annonce->id . '/panier', ['quantite' => 2]);

        $response = $this->actingAs($acheteur)->get('/panier');
        $response->assertStatus(200);
        $response->assertSee($annonce->titre);
        $response->assertViewHas('total', 1000.0);
    }

    public function test_modifier_quantite_et_retirer_un_article_du_panier(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $acheteur = $this->makeAcheteur();
        $annonce = $this->makeAnnonce($fournisseur, ['quantite' => 10]);
        $this->actingAs($acheteur)->post('/annonces/' . $annonce->id . '/panier', ['quantite' => 2]);
        $item = \App\Models\CartItem::where('user_id', $acheteur->id)->first();

        $this->actingAs($acheteur)->patch('/panier/' . $item->id, ['quantite' => 5]);
        $this->assertSame(5.0, (float) $item->fresh()->quantite);

        $this->actingAs($acheteur)->delete('/panier/' . $item->id);
        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }

    public function test_passer_commande_cree_une_commande_avec_le_bon_statut(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $acheteur = $this->makeAcheteur();
        $annonce = $this->makeAnnonce($fournisseur, ['quantite' => 10, 'prix' => 500]);
        $this->actingAs($acheteur)->post('/annonces/' . $annonce->id . '/panier', ['quantite' => 2]);

        $response = $this->actingAs($acheteur)->post('/commandes', []);
        $response->assertRedirect(route('commandes.index'));

        $commande = Commande::where('user_id', $acheteur->id)->first();
        $this->assertNotNull($commande);
        $this->assertSame('en_attente', $commande->statut);
        $this->assertSame(1000.0, (float) $commande->montant_total);
    }

    /**
     * Vérifie si le statut "payé" reflète une vérification réelle auprès d'une
     * passerelle Wave/Moov Money, ou une simple déclaration non vérifiée côté serveur
     * (PaymentController::confirmer()). Un statut "payé" pour n'importe quel numéro de
     * téléphone valide au format, sans appel réseau sortant, confirme qu'il s'agit
     * d'une simulation non vérifiée — cohérent avec les mentions "Simulation sécurisée"
     * déjà présentes dans l'UI de paiement.
     */
    public function test_paiement_wave_declare_paye_sans_verification_externe(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $acheteur = $this->makeAcheteur();
        $annonce = $this->makeAnnonce($fournisseur, ['quantite' => 10, 'prix' => 500]);
        $this->actingAs($acheteur)->post('/annonces/' . $annonce->id . '/panier', ['quantite' => 1]);
        $this->actingAs($acheteur)->post('/paiement/initier', []);

        $show = $this->actingAs($acheteur)->get('/paiement');
        $show->assertStatus(200);

        $response = $this->actingAs($acheteur)->post('/paiement/confirmer', [
            'mode_paiement' => 'wave',
            'telephone'     => '+2250700000099',
        ]);

        $commande = Commande::where('user_id', $acheteur->id)->first();
        $this->assertNotNull($commande);
        $response->assertRedirect(route('paiement.succes', $commande));
        $this->assertSame('payé', $commande->statut_paiement);
        $this->assertSame('wave', $commande->mode_paiement);
    }

    public function test_page_de_confirmation_de_paiement(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $acheteur = $this->makeAcheteur();
        $annonce = $this->makeAnnonce($fournisseur, ['quantite' => 10, 'prix' => 500]);
        $this->actingAs($acheteur)->post('/annonces/' . $annonce->id . '/panier', ['quantite' => 1]);
        $this->actingAs($acheteur)->post('/paiement/initier', []);
        $this->actingAs($acheteur)->post('/paiement/confirmer', ['mode_paiement' => 'wave', 'telephone' => '+2250700000099']);
        $commande = Commande::where('user_id', $acheteur->id)->first();

        $response = $this->actingAs($acheteur)->get('/paiement/succes/' . $commande->id);
        $response->assertStatus(200);
        $response->assertSee($annonce->titre);
    }

    /**
     * `Artisan::call('view:clear')` force une recompilation fraîche de
     * resources/views/commandes/index.blade.php avant l'assertion. Sans ce
     * vidage explicite, ce test peut passer par chance si un cache compilé
     * antérieur (potentiellement valide, potentiellement obsolète) traîne déjà
     * sur le poste — observé concrètement pendant cet audit : `storage/logs/
     * laravel.log` contient une ParseError réelle ("syntax error, unexpected
     * token '->'") sur cette vue lorsqu'elle est compilée à froid, provenant de
     * `@if(->mode_paiement)` (commandes/index.blade.php:102, variable `$commande`
     * manquante avant la flèche) — bug de code réel, pas un faux positif de test.
     */
    public function test_historique_des_commandes_acheteur(): void
    {
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $acheteur = $this->makeAcheteur();
        $annonce = $this->makeAnnonce($fournisseur, ['quantite' => 10, 'prix' => 500]);
        $this->actingAs($acheteur)->post('/annonces/' . $annonce->id . '/panier', ['quantite' => 1]);
        $this->actingAs($acheteur)->post('/commandes', []);

        $response = $this->actingAs($acheteur)->get('/commandes');
        $response->assertSee($annonce->titre);
    }

    public function test_contacter_un_fournisseur_via_la_messagerie(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $acheteur = $this->makeAcheteur();
        $annonce = $this->makeAnnonce($fournisseur);

        $ouverture = $this->actingAs($acheteur)->get('/messages/ouvrir/' . $fournisseur->id . '?annonce_id=' . $annonce->id);
        $ouverture->assertRedirect();

        $conversation = \App\Models\Conversation::first();
        $this->assertNotNull($conversation);

        $envoi = $this->actingAs($acheteur)->post('/messages/' . $conversation->id, ['contenu' => 'Bonjour, est-ce disponible ?']);
        $envoi->assertRedirect();
        $this->assertDatabaseHas('messages', ['conversation_id' => $conversation->id, 'contenu' => 'Bonjour, est-ce disponible ?']);

        // Le fournisseur voit bien le message
        $lecture = $this->actingAs($fournisseur)->get('/messages/' . $conversation->id);
        $lecture->assertSee('Bonjour, est-ce disponible ?');
    }

    public function test_laisser_un_avis_apres_transaction_completee(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $acheteur = $this->makeAcheteur();
        $annonce = $this->makeAnnonce($fournisseur, ['quantite' => 10]);

        $this->actingAs($acheteur)->post('/annonces/' . $annonce->id . '/reserver', ['quantite_demandee' => 2]);
        $reservation = Reservation::where('user_id', $acheteur->id)->first();
        $this->actingAs($fournisseur)->post('/reservations/' . $reservation->id . '/accepter');
        $this->actingAs($fournisseur)->post('/reservations/' . $reservation->id . '/completer');

        $response = $this->actingAs($acheteur)->post('/reservations/' . $reservation->id . '/avis', [
            'note' => 5, 'commentaire' => 'Très bien, merci !',
        ]);
        $response->assertRedirect();

        $this->assertDatabaseHas('avis', [
            'reservation_id' => $reservation->id, 'fournisseur_id' => $fournisseur->id, 'note' => 5,
        ]);
        $this->assertSame(5.0, (float) $fournisseur->fresh()->note_moyenne);
    }

    public function test_annuler_une_reservation_libere_l_annonce(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $acheteur = $this->makeAcheteur();
        $annonce = $this->makeAnnonce($fournisseur, ['quantite' => 10]);

        $this->actingAs($acheteur)->post('/annonces/' . $annonce->id . '/reserver', ['quantite_demandee' => 2]);
        $this->assertSame('reservé', $annonce->fresh()->statut);

        $reservation = Reservation::where('user_id', $acheteur->id)->first();
        $this->actingAs($acheteur)->post('/reservations/' . $reservation->id . '/annuler');

        $this->assertSame('annulée', $reservation->fresh()->statut);
        $this->assertSame('disponible', $annonce->fresh()->statut, 'L\'annonce doit redevenir disponible après annulation de l\'unique réservation active.');
    }

    public function test_annuler_une_commande(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $acheteur = $this->makeAcheteur();
        $annonce = $this->makeAnnonce($fournisseur, ['quantite' => 10, 'prix' => 500]);
        $this->actingAs($acheteur)->post('/annonces/' . $annonce->id . '/panier', ['quantite' => 1]);
        $this->actingAs($acheteur)->post('/commandes', []);
        $commande = Commande::where('user_id', $acheteur->id)->first();

        $response = $this->actingAs($acheteur)->post('/commandes/' . $commande->id . '/annuler');
        $response->assertRedirect();
        $this->assertSame('annulée', $commande->fresh()->statut);
    }
}

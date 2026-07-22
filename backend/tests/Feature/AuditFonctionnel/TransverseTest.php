<?php

namespace Tests\Feature\AuditFonctionnel;

use App\Notifications\NouvelleCommande;
use App\Notifications\ReservationAcceptee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\AuditFonctionnel\Concerns\SeedsTestData;
use Tests\TestCase;

/**
 * Section F du plan de test — Fonctionnalités transverses + vérifications de
 * cohérence (section 3 du PRD).
 */
class TransverseTest extends TestCase
{
    use RefreshDatabase;
    use SeedsTestData;

    /**
     * Vérification légère seulement : le vrai appel réseau au service Gemini
     * (GuzzleHttp\Client instancié directement dans ChatController, pas via la
     * façade Http — donc non interceptable par Http::fake()) n'est volontairement
     * pas déclenché ici pour ne pas dépendre du réseau ni consommer de quota API
     * pendant la suite automatisée. Vérifié séparément en dehors de PHPUnit avec
     * la vraie clé Gemini (voir rapport, section vérifications manuelles).
     */
    public function test_chatbot_valide_les_entrees_avant_appel_au_service_ia(): void
    {
        $this->postJson('/chat', [])->assertStatus(422);
        $this->postJson('/chat', ['message' => str_repeat('x', 2001)])->assertStatus(422);
    }

    public function test_blog_affiche_des_sources_recentrees_gaspillage(): void
    {
        $response = $this->get('/blog');
        $response->assertStatus(200);
        $response->assertDontSee('Le Monde Santé');
        $response->assertDontSee('Futura Santé');
        $response->assertDontSee('Santé Magazine');
        // Les libellés de sources viennent désormais de BlogController::$sources (ADEME/Actu-Environnement)
        $response->assertSee('ADEME', false);
    }

    public function test_formulaire_de_contact_envoie_un_message(): void
    {
        Mail::fake();

        $response = $this->post('/contact', [
            'nom' => 'Test User', 'email' => 'test@antigaspi.ci',
            'sujet' => 'Question', 'message' => 'Bonjour, ceci est un test.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        Mail::assertSent(\App\Mail\ContactMail::class);
    }

    public function test_formulaire_newsletter_enregistre_reellement_l_inscription(): void
    {
        $response = $this->post('/newsletter', ['email' => 'alerte@test.ci']);
        $response->assertRedirect();
        $this->assertDatabaseHas('newsletter_subscribers', ['email' => 'alerte@test.ci']);
    }

    public function test_liens_legaux_ouvrent_des_pages_reelles(): void
    {
        $this->get('/mentions-legales')->assertStatus(200)->assertSee('Mentions légales');
        $this->get('/confidentialite')->assertStatus(200)->assertSee('Politique de confidentialité');
        $this->get('/cgu')->assertStatus(200)->assertSee('Conditions Générales');
    }

    public function test_boutons_de_partage_pointent_vers_de_vrais_reseaux(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $annonce = $this->makeAnnonce($fournisseur);

        $response = $this->get('/annonces/' . $annonce->id);
        $response->assertSee('facebook.com/sharer', false);
        $response->assertSee('twitter.com/intent', false);
        $response->assertSee('wa.me', false);
    }

    public function test_indicateurs_d_impact_varient_apres_completion_d_une_reservation(): void
    {
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $acheteur = $this->makeAcheteur();
        $annonce = $this->makeAnnonce($fournisseur, ['quantite' => 20, 'unite' => 'kg', 'prix' => 200]);

        $avant = $this->get('/');
        $avant->assertViewHas('kgSauves', 0.0);

        $this->actingAs($acheteur)->post('/annonces/' . $annonce->id . '/reserver', ['quantite_demandee' => 6]);
        $reservation = \App\Models\Reservation::where('user_id', $acheteur->id)->first();
        $this->actingAs($fournisseur)->post('/reservations/' . $reservation->id . '/accepter');
        $this->actingAs($fournisseur)->post('/reservations/' . $reservation->id . '/completer');

        $apres = $this->get('/');
        $apres->assertViewHas('kgSauves', 6.0);
        $apres->assertViewHas('fcfaEconomises', 1200.0); // 6 kg * 200 FCFA
    }

    public function test_notification_declenchee_sur_nouvelle_commande(): void
    {
        Notification::fake();
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $acheteur = $this->makeAcheteur();
        $annonce = $this->makeAnnonce($fournisseur, ['quantite' => 5, 'prix' => 100]);

        $this->actingAs($acheteur)->post('/annonces/' . $annonce->id . '/panier', ['quantite' => 1]);
        $this->actingAs($acheteur)->post('/commandes', []);

        Notification::assertSentTo($fournisseur, NouvelleCommande::class);
    }

    public function test_notification_declenchee_sur_reservation_acceptee(): void
    {
        Notification::fake();
        Mail::fake();
        $this->makeCategories();
        $fournisseur = $this->makeFournisseur();
        $acheteur = $this->makeAcheteur();
        $annonce = $this->makeAnnonce($fournisseur, ['quantite' => 5]);

        $this->actingAs($acheteur)->post('/annonces/' . $annonce->id . '/reserver', ['quantite_demandee' => 1]);
        $reservation = \App\Models\Reservation::where('user_id', $acheteur->id)->first();
        $this->actingAs($fournisseur)->post('/reservations/' . $reservation->id . '/accepter');

        Notification::assertSentTo($acheteur, ReservationAcceptee::class);
    }

    public function test_pages_cles_ont_une_balise_viewport_pour_le_responsive(): void
    {
        foreach (['/', '/annonces', '/inscription'] as $url) {
            $response = $this->get($url);
            $response->assertSee('name="viewport"', false);
        }
    }

    public function test_statistiques_admin_coherentes_avec_les_statistiques_publiques(): void
    {
        $this->makeCategories();
        $admin = $this->makeAdmin();
        $fournisseur = $this->makeFournisseur();
        $this->makeAnnonce($fournisseur);
        $this->makeAnnonce($fournisseur);
        $this->makeAnnonce($fournisseur, ['statut' => 'reservé']);

        $public = $this->get('/');
        $nbAnnoncesPublic = $public->viewData('nbAnnonces');

        $admin_dashboard = $this->actingAs($admin)->get('/admin/dashboard');
        $statsAdmin = $admin_dashboard->viewData('stats');

        // stats.annonces (admin) compte TOUTES les annonces (Annonce::count(), toute statut confondu) = 3,
        // tandis que nbAnnonces (public) ne compte que les "disponible" = 2 : deux définitions différentes
        // du mot "annonces", pas directement comparables telles quelles.
        $this->assertSame(3, $statsAdmin['annonces']);
        $this->assertSame(2, $nbAnnoncesPublic);
        $this->assertNotSame(
            $statsAdmin['annonces'],
            $nbAnnoncesPublic,
            'Rappel volontaire : "annonces" admin (total) et nbAnnonces public (disponibles) ne partagent pas la même définition — à garder en tête pour ne pas les comparer naïvement en soutenance.'
        );
    }
}

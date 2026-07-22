<?php

namespace Tests\Feature\AuditFonctionnel;

use App\Models\Categorie;
use App\Models\Signalement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\AuditFonctionnel\Concerns\SeedsTestData;
use Tests\TestCase;

/**
 * Section E du plan de test — Dashboard Administrateur.
 */
class AdminTest extends TestCase
{
    use RefreshDatabase;
    use SeedsTestData;

    public function test_connexion_admin_acces_reserve_aux_admins(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin)->get('/admin/dashboard')->assertStatus(200);
    }

    public function test_dashboard_vue_d_ensemble_chiffres_coherents(): void
    {
        $this->makeCategories();
        $admin = $this->makeAdmin();
        $fournisseur = $this->makeFournisseur();
        $acheteur = $this->makeAcheteur();
        $this->makeAnnonce($fournisseur);
        $this->makeAnnonce($fournisseur);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertViewHas('stats', function ($stats) {
            return $stats['annonces'] === 2 && $stats['fournisseurs'] === 1 && $stats['acheteurs'] === 1;
        });
    }

    public function test_gestion_utilisateurs_liste_recherche(): void
    {
        $admin = $this->makeAdmin();
        $this->makeAcheteur(['nom' => 'Kouassi', 'prenom' => 'Marie', 'email' => 'marie.kouassi@test.ci']);
        $this->makeAcheteur(['nom' => 'Traore', 'prenom' => 'Ali', 'email' => 'ali.traore@test.ci']);

        $response = $this->actingAs($admin)->get('/admin/utilisateurs?q=Kouassi');
        $response->assertSee('Kouassi');
        $response->assertDontSee('Traore');
    }

    public function test_suspendre_un_utilisateur_l_empeche_de_se_connecter(): void
    {
        $admin = $this->makeAdmin();
        $cible = $this->makeAcheteur(['email' => 'suspendu@test.ci']);

        $this->actingAs($admin)->post('/admin/utilisateurs/' . $cible->id . '/suspendre');
        $this->assertSame('suspendu', $cible->fresh()->statut);

        // Se déconnecter de la session admin avant de tester la connexion de l'utilisateur ciblé
        // (la route /connexion est réservée aux invités : rester authentifié la ferait rediriger
        // avant même d'atteindre le contrôleur, sans erreur de session à observer).
        $this->post('/deconnexion');

        // Effet visible côté utilisateur concerné : connexion refusée
        $tentative = $this->post('/connexion', ['email' => 'suspendu@test.ci', 'password' => 'Password123']);
        $tentative->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_reactiver_un_utilisateur(): void
    {
        $admin = $this->makeAdmin();
        $cible = $this->makeAcheteur(['statut' => 'suspendu']);

        $this->actingAs($admin)->post('/admin/utilisateurs/' . $cible->id . '/activer');
        $this->assertSame('actif', $cible->fresh()->statut);
    }

    public function test_moderation_suppression_annonce_disparait_du_site_public(): void
    {
        $this->makeCategories();
        $admin = $this->makeAdmin();
        $fournisseur = $this->makeFournisseur();
        $annonce = $this->makeAnnonce($fournisseur, ['titre' => 'Annonce à modérer']);

        $this->actingAs($admin)->delete('/admin/annonces/' . $annonce->id);

        $this->assertSame('supprimé', $annonce->fresh()->statut);
        $this->get('/annonces')->assertDontSee('Annonce à modérer');
    }

    public function test_creer_une_categorie_visible_immediatement_dans_le_filtre_annonces(): void
    {
        $this->makeCategories();
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->post('/admin/categories', [
            'nom' => 'Produits Bio', 'icone' => '🌿', 'couleur' => '#22c55e', 'description' => 'Test',
        ]);

        $this->assertDatabaseHas('categories', ['nom' => 'Produits Bio']);
        $this->get('/annonces')->assertSee('Produits Bio');
    }

    public function test_modifier_une_categorie(): void
    {
        $this->makeCategories();
        $admin = $this->makeAdmin();
        $categorie = Categorie::first();

        $this->actingAs($admin)->put('/admin/categories/' . $categorie->id, [
            'nom' => 'Nom modifié', 'icone' => $categorie->icone, 'couleur' => $categorie->couleur,
        ]);

        $this->assertSame('Nom modifié', $categorie->fresh()->nom);
    }

    public function test_gestion_signalements_traitement(): void
    {
        $this->makeCategories();
        $admin = $this->makeAdmin();
        $fournisseur = $this->makeFournisseur();
        $auteur = $this->makeAcheteur();
        $annonce = $this->makeAnnonce($fournisseur);
        $signalement = Signalement::create([
            'user_id' => $auteur->id, 'annonce_id' => $annonce->id,
            'raison' => 'Contenu trompeur', 'statut' => 'en_attente',
        ]);

        $liste = $this->actingAs($admin)->get('/admin/signalements');
        $liste->assertSee('Contenu trompeur');

        $this->actingAs($admin)->post('/admin/signalements/' . $signalement->id . '/traiter');
        $this->assertSame('traité', $signalement->fresh()->statut);
    }

    /**
     * Le plan de test attend une "vue globale des commandes tous fournisseurs
     * confondus" côté admin. Aucune route web (routes/web.php, groupe admin.*)
     * n'existe pour cela — seule l'API JSON mobile l'expose
     * (AdminApiController::commandes, routes/api.php:65). Ce test documente
     * l'absence de la fonctionnalité côté site web, ce n'est pas un bug de test.
     */
    public function test_gestion_des_commandes_absente_du_dashboard_web(): void
    {
        $admin = $this->makeAdmin();

        $this->assertFalse(
            \Illuminate\Support\Facades\Route::has('admin.commandes'),
            'Aucune route web "admin.commandes" trouvée : la gestion des commandes existe uniquement dans l\'API mobile, pas sur le dashboard web.'
        );
    }

    public function test_acces_admin_refuse_pour_un_compte_non_admin(): void
    {
        $acheteur = $this->makeAcheteur();
        $this->actingAs($acheteur)->get('/admin/dashboard')->assertForbidden();

        $fournisseur = $this->makeFournisseur();
        $this->actingAs($fournisseur)->get('/admin/utilisateurs')->assertForbidden();
    }
}

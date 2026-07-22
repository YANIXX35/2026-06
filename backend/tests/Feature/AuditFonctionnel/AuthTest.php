<?php

namespace Tests\Feature\AuditFonctionnel;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\Feature\AuditFonctionnel\Concerns\SeedsTestData;
use Tests\TestCase;

/**
 * Section A du plan de test — Authentification et comptes.
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;
    use SeedsTestData;

    private function otpFor(string $email): string
    {
        return DB::table('password_reset_otps')->where('email', $email)->value('otp');
    }

    public function test_inscription_acheteur_particulier(): void
    {
        Mail::fake();

        $response = $this->post('/inscription', [
            'nom' => 'Doe', 'prenom' => 'Jane', 'email' => 'jane.acheteur@test.ci',
            'password' => 'Password123', 'password_confirmation' => 'Password123',
            'role' => 'acheteur',
        ]);
        $response->assertRedirect(route('inscription.otp.form'));
        $this->assertDatabaseMissing('users', ['email' => 'jane.acheteur@test.ci']);

        $otp = $this->otpFor('jane.acheteur@test.ci');
        $this->assertNotNull($otp, 'OTP absent en base après inscription');

        $verify = $this->post('/inscription/verifier-email', ['otp' => $otp]);
        $verify->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'jane.acheteur@test.ci', 'role' => 'acheteur', 'statut' => 'actif',
        ]);
    }

    public function test_inscription_fournisseur_avec_type_de_structure(): void
    {
        Mail::fake();

        $this->post('/inscription', [
            'nom' => 'Kone', 'prenom' => 'Awa', 'email' => 'awa.fournisseur@test.ci',
            'password' => 'Password123', 'password_confirmation' => 'Password123',
            'role' => 'fournisseur', 'type_structure' => 'restaurant', 'nom_structure' => 'Chez Awa',
        ]);

        $otp = $this->otpFor('awa.fournisseur@test.ci');
        $this->post('/inscription/verifier-email', ['otp' => $otp]);

        $this->assertDatabaseHas('users', [
            'email' => 'awa.fournisseur@test.ci', 'role' => 'fournisseur',
            'type_structure' => 'restaurant', 'nom_structure' => 'Chez Awa',
        ]);
    }

    public function test_otp_inscription_code_invalide_rejete(): void
    {
        Mail::fake();

        $this->post('/inscription', [
            'nom' => 'Doe', 'prenom' => 'John', 'email' => 'john@test.ci',
            'password' => 'Password123', 'password_confirmation' => 'Password123',
            'role' => 'acheteur',
        ]);

        $response = $this->post('/inscription/verifier-email', ['otp' => '000000']);
        $response->assertSessionHasErrors('otp');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'john@test.ci']);
    }

    public function test_connexion_email_mot_de_passe_valide(): void
    {
        $this->makeAcheteur(['email' => 'ok@test.ci', 'password' => Hash::make('Password123')]);

        $response = $this->post('/connexion', ['email' => 'ok@test.ci', 'password' => 'Password123']);
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_connexion_mot_de_passe_errone(): void
    {
        $this->makeAcheteur(['email' => 'ok2@test.ci', 'password' => Hash::make('Password123')]);

        $response = $this->post('/connexion', ['email' => 'ok2@test.ci', 'password' => 'WrongPassword']);
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_connexion_google_redirect_genere_une_url_valide(): void
    {
        $response = $this->get('/auth/google/redirect');
        $response->assertRedirect();
        $this->assertStringContainsString('accounts.google.com', $response->headers->get('Location'));
    }

    public function test_connexion_facebook_redirect_genere_une_url_valide(): void
    {
        $response = $this->get('/auth/facebook/redirect');
        $response->assertRedirect();
        $this->assertStringContainsString('facebook.com', $response->headers->get('Location'));
    }

    public function test_callback_google_cree_ou_lie_un_compte(): void
    {
        $socialUser = new SocialiteUser();
        $socialUser->map([
            'id' => 'google-123', 'name' => 'Google User',
            'email' => 'googleuser@test.ci', 'avatar' => null,
        ]);

        Socialite::shouldReceive('driver->user')->andReturn($socialUser);

        $response = $this->get('/auth/google/callback');
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'googleuser@test.ci', 'social_provider' => 'google', 'role' => 'acheteur',
        ]);
    }

    public function test_mot_de_passe_oublie_flux_complet(): void
    {
        Mail::fake();
        $user = $this->makeAcheteur(['email' => 'forgot@test.ci']);

        $this->post('/mot-de-passe-oublie', ['email' => 'forgot@test.ci']);
        $otp = $this->otpFor('forgot@test.ci');
        $this->assertNotNull($otp);

        $this->post('/verifier-otp', ['otp' => $otp])->assertRedirect(route('password.new.form'));

        $this->post('/nouveau-mot-de-passe', [
            'password' => 'NouveauPass123', 'password_confirmation' => 'NouveauPass123',
        ])->assertRedirect(route('connexion'));

        $this->assertTrue(Hash::check('NouveauPass123', $user->fresh()->password));

        // Connexion avec le nouveau mot de passe
        $this->post('/connexion', ['email' => 'forgot@test.ci', 'password' => 'NouveauPass123'])
            ->assertRedirect(route('dashboard'));
    }

    public function test_deconnexion_invalide_la_session(): void
    {
        $user = $this->makeAcheteur();
        $this->actingAs($user)->post('/deconnexion')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_acces_page_protegee_sans_connexion_redirige(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect(route('connexion'));
    }

    public function test_acheteur_ne_peut_pas_publier_une_annonce(): void
    {
        $acheteur = $this->makeAcheteur();

        $this->actingAs($acheteur)->get('/annonces/publier')->assertForbidden();
        $this->actingAs($acheteur)->post('/annonces', ['titre' => 'x'])->assertForbidden();
    }
}

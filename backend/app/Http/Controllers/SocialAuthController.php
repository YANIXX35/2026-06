<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    private array $allowed = ['google', 'facebook'];

    public function redirect(string $provider)
    {
        abort_unless(in_array($provider, $this->allowed), 404);
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        abort_unless(in_array($provider, $this->allowed), 404);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('connexion')->withErrors(['social' => 'Connexion via '.ucfirst($provider).' échouée. Réessayez.']);
        }

        // Cherche un compte existant par social_id ou par email
        $user = User::where('social_provider', $provider)
                    ->where('social_id', $socialUser->getId())
                    ->first();

        if (!$user && $socialUser->getEmail()) {
            $user = User::where('email', $socialUser->getEmail())->first();
            if ($user) {
                // Lie le compte existant au provider social
                $user->update([
                    'social_provider' => $provider,
                    'social_id'       => $socialUser->getId(),
                ]);
            }
        }

        if (!$user) {
            // Création automatique du compte
            $nameParts = explode(' ', $socialUser->getName() ?? 'Utilisateur', 2);

            // Email de secours unique si le provider ne le fournit pas
            $email = $socialUser->getEmail()
                ?? ($socialUser->getId() . '+' . $provider . '@noemail.social');

            // Évite un conflit si cet email fantôme existe déjà (cas rare)
            if (User::where('email', $email)->exists()) {
                $email = $socialUser->getId() . '+' . $provider . '+' . uniqid() . '@noemail.social';
            }

            $user = User::create([
                'prenom'          => $nameParts[0],
                'nom'             => $nameParts[1] ?? '',
                'email'           => $email,
                'password'        => bcrypt(\Illuminate\Support\Str::random(32)),
                'role'            => 'acheteur',
                'statut'          => 'actif',
                'social_provider' => $provider,
                'social_id'       => $socialUser->getId(),
            ]);
        }

        if ($user->statut === 'suspendu') {
            return redirect()->route('connexion')->withErrors(['social' => 'Votre compte est suspendu.']);
        }

        Auth::login($user, true);

        return redirect()->intended(route('dashboard'));
    }
}

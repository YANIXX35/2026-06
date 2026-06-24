<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email ou mot de passe incorrect.'], 401);
        }

        if ($user->statut === 'suspendu') {
            return response()->json(['message' => 'Votre compte est suspendu.'], 403);
        }

        if ($user->statut === 'en_attente') {
            return response()->json(['message' => 'Veuillez vérifier votre email avant de vous connecter.', 'need_otp' => true, 'email' => $user->email], 403);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $this->formatUser($user),
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:200',
            'email'                 => 'required|email|unique:users',
            'password'              => 'required|string|min:6|confirmed',
            'role'                  => 'required|in:client,fournisseur',
        ]);

        $parts  = explode(' ', trim($request->name), 2);
        $nom    = $parts[0];
        $prenom = $parts[1] ?? $parts[0];

        $user = User::create([
            'nom'      => $nom,
            'prenom'   => $prenom,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role === 'fournisseur' ? 'fournisseur' : 'acheteur',
            'statut'   => 'en_attente',
        ]);

        $otp = PasswordResetOtp::generer($user->email);

        $this->envoyerOtp($user->email, $user->prenom, $otp->otp);

        return response()->json([
            'message' => 'Compte créé. Vérifiez votre email pour le code OTP.',
            'email'   => $user->email,
        ], 201);
    }

    public function verifierOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Compte introuvable.'], 404);
        }

        $record = PasswordResetOtp::where('email', $request->email)->latest()->first();

        if (!$record || !$record->estValide($request->otp)) {
            return response()->json(['message' => 'Code OTP invalide ou expiré.'], 422);
        }

        $user->update(['statut' => 'actif']);
        $record->delete();

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'message' => 'Compte vérifié avec succès !',
            'token'   => $token,
            'user'    => $this->formatUser($user),
        ]);
    }

    public function renvoyerOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->where('statut', 'en_attente')->first();

        if (!$user) {
            return response()->json(['message' => 'Compte introuvable ou déjà actif.'], 404);
        }

        $otp = PasswordResetOtp::generer($user->email);
        $this->envoyerOtp($user->email, $user->prenom, $otp->otp);

        return response()->json(['message' => 'Nouveau code envoyé.']);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnecté.']);
    }

    public function user(Request $request)
    {
        return response()->json($this->formatUser($request->user()));
    }

    private function envoyerOtp(string $email, string $prenom, string $code): void
    {
        try {
            Mail::html(
                view('emails.otp', compact('prenom', 'code'))->render(),
                function ($msg) use ($email, $prenom) {
                    $msg->to($email, $prenom)
                        ->subject('Votre code de vérification AntiGaspiCI');
                }
            );
        } catch (\Throwable $e) {
            \Log::error('OTP mail failed: ' . $e->getMessage());
        }
    }

    private function formatUser(User $user): array
    {
        return [
            'id'    => $user->id,
            'name'  => trim($user->prenom . ' ' . $user->nom),
            'email' => $user->email,
            'role'  => $user->role,
            'actif' => $user->statut === 'actif',
            'photo' => $user->photo ?? null,
        ];
    }
}

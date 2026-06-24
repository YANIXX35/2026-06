<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        if ($user->statut !== 'actif') {
            return response()->json(['message' => 'Votre compte est suspendu.'], 403);
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

        // Séparer nom et prénom
        $parts  = explode(' ', trim($request->name), 2);
        $nom    = $parts[0];
        $prenom = $parts[1] ?? $parts[0];

        $user = User::create([
            'nom'      => $nom,
            'prenom'   => $prenom,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role === 'fournisseur' ? 'fournisseur' : 'acheteur',
            'statut'   => 'actif',
        ]);

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $this->formatUser($user),
        ], 201);
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

    private function formatUser(User $user): array
    {
        return [
            'id'     => $user->id,
            'name'   => $user->prenom . ' ' . $user->nom,
            'email'  => $user->email,
            'role'   => $user->role,
            'actif'  => $user->statut === 'actif',
            'photo'  => $user->photo,
        ];
    }
}

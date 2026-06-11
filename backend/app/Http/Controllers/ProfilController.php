<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    public function show(User $user)
    {
        if ($user->role !== 'fournisseur') abort(404);

        $annonces = $user->annonces()
            ->with(['categorie', 'photoPrincipale'])
            ->where('statut', 'disponible')
            ->latest()
            ->take(6)
            ->get();

        $avis = \App\Models\Avis::with('auteur')
            ->where('fournisseur_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        return view('profil.show', compact('user', 'annonces', 'avis'));
    }

    public function edit()
    {
        return view('profil.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'nom'       => 'required|string|max:100',
            'prenom'    => 'required|string|max:100',
            'telephone' => 'nullable|string|max:20',
            'adresse'   => 'nullable|string|max:300',
            'photo'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $request->validate($rules);

        $data = $request->only(['nom', 'prenom', 'telephone', 'adresse']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $request->file('photo')->store('photos/profils', 'public');
        }

        if ($user->role === 'fournisseur') {
            $data['nom_structure']         = $request->nom_structure;
            $data['description_structure'] = $request->description_structure;
        }

        $user->update($data);

        return back()->with('success', 'Profil mis à jour avec succès.');
    }
}

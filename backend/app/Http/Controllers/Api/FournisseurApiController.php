<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use App\Models\Commande;
use App\Models\CommandeItem;
use App\Models\Photo;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FournisseurApiController extends Controller
{
    // ── MES ANNONCES ──────────────────────────────────────────────────────
    public function mesAnnonces(Request $request)
    {
        $query = Annonce::with('categorie:id,nom')
            ->where('user_id', Auth::id());

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        return response()->json($query->latest()->paginate(20));
    }

    public function creerAnnonce(Request $request)
    {
        $request->validate([
            'titre'           => 'required|string|max:255',
            'description'     => 'required|string',
            'categorie_id'    => 'required|exists:categories,id',
            'type_offre'      => 'required|in:don,vente',
            'quantite'        => 'required|numeric|min:0.01',
            'unite'           => 'required|string|max:50',
            'prix'            => 'nullable|numeric|min:0',
            'adresse_collecte'=> 'nullable|string|max:255',
            'date_expiration' => 'nullable|date|after:now',
        ]);

        $annonce = Annonce::create([
            'user_id'          => Auth::id(),
            'titre'            => $request->titre,
            'description'      => $request->description,
            'categorie_id'     => $request->categorie_id,
            'type_offre'       => $request->type_offre,
            'quantite'         => $request->quantite,
            'unite'            => $request->unite,
            'prix'             => $request->type_offre === 'don' ? 0 : ($request->prix ?? 0),
            'adresse_collecte' => $request->adresse_collecte,
            'date_expiration'  => $request->date_expiration,
            'statut'           => 'disponible',
        ]);

        return response()->json([
            'message' => 'Annonce publiée avec succès.',
            'annonce' => $annonce->load('categorie'),
        ], 201);
    }

    public function modifierAnnonce(Request $request, Annonce $annonce)
    {
        if ($annonce->user_id !== Auth::id()) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $request->validate([
            'titre'           => 'sometimes|string|max:255',
            'description'     => 'sometimes|string',
            'quantite'        => 'sometimes|numeric|min:0.01',
            'prix'            => 'nullable|numeric|min:0',
            'statut'          => 'sometimes|in:disponible,indisponible',
            'adresse_collecte'=> 'nullable|string|max:255',
            'date_expiration' => 'nullable|date|after:now',
        ]);

        $annonce->update($request->only([
            'titre', 'description', 'quantite', 'prix', 'statut',
            'adresse_collecte', 'date_expiration',
        ]));

        return response()->json(['message' => 'Annonce mise à jour.', 'annonce' => $annonce]);
    }

    public function supprimerAnnonce(Annonce $annonce)
    {
        if ($annonce->user_id !== Auth::id()) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $annonce->update(['statut' => 'supprimé']);
        return response()->json(['message' => 'Annonce supprimée.']);
    }

    // ── COMMANDES FOURNISSEUR ─────────────────────────────────────────────
    public function commandes(Request $request)
    {
        $query = Commande::with(['acheteur:id,nom,prenom,email,telephone', 'items' => function ($q) {
            $q->where('fournisseur_id', Auth::id())->with('annonce:id,titre,unite,type_offre');
        }])
        ->whereHas('items', fn($q) => $q->where('fournisseur_id', Auth::id()));

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        return response()->json($query->latest()->paginate(15));
    }

    public function accepterItem(CommandeItem $item)
    {
        if ($item->fournisseur_id !== Auth::id()) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $item->update(['statut' => 'accepté']);
        return response()->json(['message' => 'Commande acceptée.', 'item' => $item]);
    }

    // ── PHOTO ANNONCE ─────────────────────────────────────────────────────
    public function ajouterPhoto(Request $request, Annonce $annonce)
    {
        if ($annonce->user_id !== Auth::id()) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $request->validate([
            'photo' => 'required|image|max:4096',
        ]);

        $file = $request->file('photo');
        $isPremiere = $annonce->photos()->count() === 0;

        try {
            $uploaded = Cloudinary::upload($file->getRealPath(), [
                'folder'         => 'antigasci/annonces',
                'resource_type'  => 'image',
                'transformation' => [['quality' => 'auto', 'fetch_format' => 'auto']],
            ]);
            $url = $uploaded->getSecurePath();
        } catch (\Throwable $e) {
            Log::warning('Cloudinary upload failed (mobile), fallback local', ['error' => $e->getMessage()]);
            $url = $file->store('annonces', 'public');
        }

        $photo = Photo::create([
            'annonce_id'   => $annonce->id,
            'url'          => $url,
            'is_principale' => $isPremiere,
        ]);

        return response()->json(['message' => 'Photo ajoutée.', 'photo' => $photo], 201);
    }

    public function refuserItem(CommandeItem $item)
    {
        if ($item->fournisseur_id !== Auth::id()) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $item->update(['statut' => 'refusé']);
        return response()->json(['message' => 'Commande refusée.', 'item' => $item]);
    }
}

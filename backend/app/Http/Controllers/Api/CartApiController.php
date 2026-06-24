<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartApiController extends Controller
{
    public function index()
    {
        $items = CartItem::with(['annonce.photoPrincipale', 'annonce.categorie'])
            ->where('user_id', Auth::id())
            ->get();

        $total = $items->sum(fn($i) => $i->sousTotal());

        return response()->json([
            'items' => $items,
            'total' => $total,
            'count' => $items->count(),
        ]);
    }

    public function ajouter(Request $request)
    {
        $request->validate([
            'annonce_id' => 'required|exists:annonces,id',
            'quantite'   => 'required|numeric|min:0.01',
        ]);

        $annonce = Annonce::findOrFail($request->annonce_id);

        if ($annonce->statut !== 'disponible' || $annonce->estExpire()) {
            return response()->json(['message' => 'Cette annonce n\'est plus disponible.'], 422);
        }

        if ($annonce->user_id === Auth::id()) {
            return response()->json(['message' => 'Vous ne pouvez pas ajouter votre propre annonce.'], 422);
        }

        $existing = CartItem::where('user_id', Auth::id())
            ->where('annonce_id', $annonce->id)
            ->first();

        if ($existing) {
            $existing->update([
                'quantite' => min($request->quantite + $existing->quantite, $annonce->quantite),
            ]);
            $item = $existing;
        } else {
            $item = CartItem::create([
                'user_id'    => Auth::id(),
                'annonce_id' => $annonce->id,
                'quantite'   => min($request->quantite, $annonce->quantite),
            ]);
        }

        return response()->json(['message' => 'Ajouté au panier.', 'item' => $item], 201);
    }

    public function supprimer(CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $cartItem->delete();
        return response()->json(['message' => 'Article retiré.']);
    }

    public function vider()
    {
        CartItem::where('user_id', Auth::id())->delete();
        return response()->json(['message' => 'Panier vidé.']);
    }
}

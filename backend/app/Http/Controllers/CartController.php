<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $items = CartItem::with(['annonce.photoPrincipale', 'annonce.user', 'annonce.reservations'])
            ->where('user_id', Auth::id())
            ->get();

        $total = $items->sum(fn($item) => $item->sousTotal());

        return view('cart.index', compact('items', 'total'));
    }

    public function ajouter(Request $request, Annonce $annonce)
    {
        $request->validate([
            'quantite' => 'required|numeric|min:0.01|max:' . $annonce->quantite,
        ]);

        if ($annonce->statut !== 'disponible' || $annonce->estExpire()) {
            return back()->with('error', 'Cette annonce n\'est plus disponible.');
        }

        if ($annonce->user_id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas ajouter votre propre annonce au panier.');
        }

        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('annonce_id', $annonce->id)
            ->first();

        if ($cartItem) {
            $cartItem->update(['quantite' => min($request->quantite + $cartItem->quantite, $annonce->quantite)]);
        } else {
            $cartItem = CartItem::create([
                'user_id'    => Auth::id(),
                'annonce_id' => $annonce->id,
                'quantite'   => $request->quantite,
            ]);
        }

        $prixUnitaire = $cartItem->prixUnitaire();
        $isNegocie = $prixUnitaire < $annonce->prix;

        $msg = '« ' . $annonce->titre . ' » ajouté au panier !';
        if ($isNegocie) {
            $msg = '« ' . $annonce->titre . ' » ajouté au panier à votre prix négocié (' . number_format($prixUnitaire, 0, ',', ' ') . ' FCFA) !';
        }

        return back()->with('cart_success', $msg);
    }

    public function mettreAJour(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) abort(403);

        $request->validate([
            'quantite' => 'required|numeric|min:0.01|max:' . $cartItem->annonce->quantite,
        ]);

        $cartItem->update(['quantite' => $request->quantite]);

        return back()->with('success', 'Quantité mise à jour.');
    }

    public function supprimer(CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) abort(403);
        $cartItem->delete();
        return back()->with('success', 'Article retiré du panier.');
    }

    public function vider()
    {
        CartItem::where('user_id', Auth::id())->delete();
        return back()->with('success', 'Panier vidé.');
    }
}

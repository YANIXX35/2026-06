<?php

namespace App\Http\Controllers;

use App\Models\AbonnementCategorie;
use App\Models\Categorie;
use Illuminate\Support\Facades\Auth;

class AbonnementController extends Controller
{
    public function toggle(Categorie $categorie)
    {
        $existing = AbonnementCategorie::where('user_id', Auth::id())
            ->where('categorie_id', $categorie->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $msg = 'Alerte désactivée pour "' . $categorie->nom . '".';
        } else {
            AbonnementCategorie::create([
                'user_id'     => Auth::id(),
                'categorie_id'=> $categorie->id,
            ]);
            $msg = 'Vous recevrez une alerte pour chaque nouvelle annonce en "' . $categorie->nom . '".';
        }

        return back()->with('success', $msg);
    }
}

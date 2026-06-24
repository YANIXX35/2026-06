<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use App\Models\Categorie;
use Illuminate\Http\Request;

class AnnonceApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Annonce::with(['user:id,nom,prenom', 'categorie:id,nom', 'photoPrincipale'])
            ->where('statut', 'disponible');

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('titre', 'ilike', '%' . $request->q . '%')
                  ->orWhere('description', 'ilike', '%' . $request->q . '%');
            });
        }
        if ($request->filled('categorie')) {
            $query->where('categorie_id', $request->categorie);
        }
        if ($request->filled('type_offre')) {
            $query->where('type_offre', $request->type_offre);
        }
        if ($request->filled('ville')) {
            $query->where('adresse_collecte', 'ilike', '%' . $request->ville . '%');
        }

        $annonces = $query->orderByRaw("
            CASE
                WHEN date_expiration IS NOT NULL
                     AND date_expiration > NOW()
                     AND date_expiration <= NOW() + INTERVAL '24 hours'
                THEN 0 ELSE 1
            END ASC
        ")->latest()->paginate(12);

        return response()->json($annonces);
    }

    public function show(Annonce $annonce)
    {
        if ($annonce->statut === 'supprimé') {
            return response()->json(['message' => 'Annonce introuvable.'], 404);
        }

        $annonce->incrementerVues();
        $annonce->load(['user:id,nom,prenom,photo', 'categorie:id,nom', 'photos']);

        return response()->json($annonce);
    }

    public function categories()
    {
        return response()->json(Categorie::select('id', 'nom', 'icone')->get());
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Categorie;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnnonceController extends Controller
{
    public function index(Request $request)
    {
        $query = Annonce::with(['user', 'categorie', 'photoPrincipale'])
            ->where('statut', 'disponible');

        if ($request->filled('q')) {
            $query->where('titre', 'like', '%' . $request->q . '%')
                  ->orWhere('description', 'like', '%' . $request->q . '%');
        }
        if ($request->filled('categorie')) {
            $query->where('categorie_id', $request->categorie);
        }
        if ($request->filled('type_offre')) {
            $query->where('type_offre', $request->type_offre);
        }
        if ($request->filled('ville')) {
            $query->where('adresse_collecte', 'like', '%' . $request->ville . '%');
        }

        $annonces   = $query->latest()->paginate(12);
        $categories = Categorie::all();

        return view('annonces.index', compact('annonces', 'categories'));
    }

    public function show(Annonce $annonce)
    {
        $annonce->incrementerVues();
        $annonce->load(['user', 'categorie', 'photos', 'reservations']);
        $annoncesLiees = Annonce::where('categorie_id', $annonce->categorie_id)
            ->where('id', '!=', $annonce->id)
            ->where('statut', 'disponible')
            ->limit(4)->get();

        return view('annonces.show', compact('annonce', 'annoncesLiees'));
    }

    public function create()
    {
        $this->authorize('create', Annonce::class);
        $categories = Categorie::all();
        return view('annonces.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Annonce::class);

        $validated = $request->validate([
            'titre'           => 'required|string|max:200',
            'description'     => 'nullable|string',
            'categorie_id'    => 'required|exists:categories,id',
            'quantite'        => 'required|numeric|min:0.01',
            'unite'           => 'required|string|max:20',
            'prix'            => 'nullable|numeric|min:0',
            'type_offre'      => 'required|in:vente,don,alimentation_animale,transformation',
            'adresse_collecte'=> 'nullable|string|max:300',
            'date_expiration' => 'nullable|date',
            'photos.*'        => 'nullable|image|max:2048',
        ]);

        if ($validated['type_offre'] === 'don') {
            $validated['prix'] = 0;
        }
        $validated['prix'] = $validated['prix'] ?? 0;
        $validated['description'] = $validated['description'] ?? '';
        $validated['adresse_collecte'] = $validated['adresse_collecte'] ?? '';
        $validated['user_id'] = Auth::id();
        $validated['statut'] = 'disponible';

        $annonce = Annonce::create($validated);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $file) {
                $path = $file->store('annonces', 'public');
                Photo::create([
                    'annonce_id'   => $annonce->id,
                    'url'          => $path,
                    'is_principale'=> $index === 0,
                ]);
            }
        }

        return redirect()->route('annonces.show', $annonce)->with('success', 'Annonce publiée avec succès !');
    }

    public function edit(Annonce $annonce)
    {
        $this->authorize('update', $annonce);
        $categories = Categorie::all();
        return view('annonces.edit', compact('annonce', 'categories'));
    }

    public function update(Request $request, Annonce $annonce)
    {
        $this->authorize('update', $annonce);

        $validated = $request->validate([
            'titre'           => 'required|string|max:200',
            'description'     => 'nullable|string',
            'categorie_id'    => 'required|exists:categories,id',
            'quantite'        => 'required|numeric|min:0.01',
            'unite'           => 'required|string|max:20',
            'prix'            => 'nullable|numeric|min:0',
            'type_offre'      => 'required|in:vente,don,alimentation_animale,transformation',
            'adresse_collecte'=> 'nullable|string|max:300',
            'date_expiration' => 'nullable|date',
            'statut'          => 'required|in:disponible,reservé,expiré',
        ]);

        if ($validated['type_offre'] === 'don') {
            $validated['prix'] = 0;
        }
        $validated['prix'] = $validated['prix'] ?? 0;

        $annonce->update($validated);
        return redirect()->route('dashboard')->with('success', 'Annonce mise à jour.');
    }

    public function destroy(Annonce $annonce)
    {
        $this->authorize('delete', $annonce);
        $annonce->update(['statut' => 'supprimé']);
        return redirect()->route('dashboard')->with('success', 'Annonce supprimée.');
    }

    public function mesAnnonces()
    {
        $annonces = Annonce::with(['categorie', 'photoPrincipale', 'reservations'])
            ->where('user_id', Auth::id())->latest()->paginate(10);
        return view('dashboard.mes-annonces', compact('annonces'));
    }
}

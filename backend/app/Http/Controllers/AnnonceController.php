<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\AbonnementCategorie;
use App\Models\Categorie;
use App\Models\Photo;
use App\Notifications\NouvelleAnnonceCategorie;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

        // Urgents (expire dans 24h) remontés en premier. Bornes passées en
        // parametres lies plutot que NOW()/INTERVAL (specifiques a PostgreSQL)
        // pour rester compatible avec les autres drivers (ex. MySQL en local).
        $annonces = $query->orderByRaw("
            CASE
                WHEN date_expiration IS NOT NULL
                     AND date_expiration > ?
                     AND date_expiration <= ?
                THEN 0 ELSE 1
            END ASC
        ", [now(), now()->addHours(24)])->latest()->paginate(12);
        $categories = Categorie::all();

        $annoncesGeo = $annonces->filter(fn($a) => $a->latitude && $a->longitude)->map(fn($a) => [
            'lat'     => (float) $a->latitude,
            'lng'     => (float) $a->longitude,
            'titre'   => $a->titre,
            'prix'    => $a->type_offre === 'don' ? 'GRATUIT' : number_format($a->prix, 0, ',', ' ').' FCFA',
            'adresse' => $a->adresse_collecte,
            'type'    => $a->type_offre,
            'url'     => route('annonces.show', $a),
            'photo'   => $a->photoPrincipale?->url,
        ])->values();

        $cartItems = collect();
        if (Auth::check()) {
            $cartItems = \App\Models\CartItem::where('user_id', Auth::id())
                ->get()->keyBy('annonce_id');
        }

        return view('annonces.index', compact('annonces', 'categories', 'annoncesGeo', 'cartItems'));
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
            'latitude'        => 'nullable|numeric|between:-90,90',
            'longitude'       => 'nullable|numeric|between:-180,180',
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
                try {
                    $uploaded = Cloudinary::upload($file->getRealPath(), [
                        'folder'          => 'antigasci/annonces',
                        'resource_type'   => 'image',
                        'transformation'  => [['quality' => 'auto', 'fetch_format' => 'auto']],
                    ]);
                    $url = $uploaded->getSecurePath();
                } catch (\Throwable $e) {
                    Log::warning('Cloudinary upload failed, fallback local', ['error' => $e->getMessage()]);
                    $url = $file->store('annonces', 'public');
                }
                Photo::create([
                    'annonce_id'    => $annonce->id,
                    'url'           => $url,
                    'is_principale' => $index === 0,
                ]);
            }
        }

        // Alertes aux abonnés de cette catégorie
        $annonce->load('categorie');
        $abonnes = AbonnementCategorie::with('user')
            ->where('categorie_id', $annonce->categorie_id)
            ->where('user_id', '!=', Auth::id())
            ->get();
        foreach ($abonnes as $abo) {
            $abo->user->notify(new NouvelleAnnonceCategorie($annonce));
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
            'latitude'        => 'nullable|numeric|between:-90,90',
            'longitude'       => 'nullable|numeric|between:-180,180',
            'date_expiration' => 'nullable|date',
            'statut'          => 'required|in:disponible,reservé,expiré',
            'photos.*'        => 'nullable|image|max:2048',
        ]);

        if ($validated['type_offre'] === 'don') {
            $validated['prix'] = 0;
        }
        $validated['prix'] = $validated['prix'] ?? 0;

        $annonce->update($validated);

        if ($request->hasFile('photos')) {
            $annonce->photos()->delete();
            foreach ($request->file('photos') as $index => $file) {
                try {
                    $uploaded = Cloudinary::upload($file->getRealPath(), [
                        'folder'         => 'antigasci/annonces',
                        'resource_type'  => 'image',
                        'transformation' => [['quality' => 'auto', 'fetch_format' => 'auto']],
                    ]);
                    $url = $uploaded->getSecurePath();
                } catch (\Throwable $e) {
                    Log::warning('Cloudinary upload failed on update, fallback local', ['error' => $e->getMessage()]);
                    $url = $file->store('annonces', 'public');
                }
                Photo::create([
                    'annonce_id'    => $annonce->id,
                    'url'           => $url,
                    'is_principale' => $index === 0,
                ]);
            }
        }

        return redirect()->route('annonces.mes-annonces')->with('success', 'Annonce mise à jour.');
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

<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    public function index()
    {
        $categories = Categorie::withCount('annonces')->orderBy('nom')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'         => 'required|string|max:100|unique:categories,nom',
            'icone'       => 'required|string|max:10',
            'couleur'     => 'required|string|max:20',
            'description' => 'nullable|string|max:255',
        ]);

        Categorie::create($request->only(['nom', 'icone', 'couleur', 'description']));

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie « '.$request->nom.' » créée.');
    }

    public function edit(Categorie $categorie)
    {
        return view('admin.categories.edit', compact('categorie'));
    }

    public function update(Request $request, Categorie $categorie)
    {
        $request->validate([
            'nom'         => 'required|string|max:100|unique:categories,nom,'.$categorie->id,
            'icone'       => 'required|string|max:10',
            'couleur'     => 'required|string|max:20',
            'description' => 'nullable|string|max:255',
        ]);

        $categorie->update($request->only(['nom', 'icone', 'couleur', 'description']));

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie mise à jour.');
    }

    public function destroy(Categorie $categorie)
    {
        if ($categorie->annonces()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer cette catégorie — elle contient '.$categorie->annonces()->count().' annonce(s).');
        }

        $categorie->delete();

        return back()->with('success', 'Catégorie supprimée.');
    }
}

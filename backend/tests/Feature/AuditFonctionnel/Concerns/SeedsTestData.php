<?php

namespace Tests\Feature\AuditFonctionnel\Concerns;

use App\Models\Annonce;
use App\Models\Categorie;
use App\Models\User;
use Database\Seeders\CategorieSeeder;
use Illuminate\Support\Facades\Hash;

trait SeedsTestData
{
    protected function makeCategories(): void
    {
        (new CategorieSeeder())->run();
    }

    protected function makeAcheteur(array $overrides = []): User
    {
        return User::create(array_merge([
            'nom'      => 'Acheteur',
            'prenom'   => 'Test',
            'email'    => 'acheteur.test+' . uniqid() . '@antigaspi.ci',
            'password' => Hash::make('Password123'),
            'role'     => 'acheteur',
            'statut'   => 'actif',
            'type_acheteur' => 'particulier',
        ], $overrides));
    }

    protected function makeFournisseur(array $overrides = []): User
    {
        return User::create(array_merge([
            'nom'      => 'Fournisseur',
            'prenom'   => 'Test',
            'email'    => 'fournisseur.test+' . uniqid() . '@antigaspi.ci',
            'password' => Hash::make('Password123'),
            'role'     => 'fournisseur',
            'statut'   => 'actif',
            'type_structure' => 'restaurant',
            'nom_structure'  => 'Restaurant Test',
        ], $overrides));
    }

    protected function makeAdmin(array $overrides = []): User
    {
        return User::create(array_merge([
            'nom'      => 'Admin',
            'prenom'   => 'Test',
            'email'    => 'admin.test+' . uniqid() . '@antigaspi.ci',
            'password' => Hash::make('Password123'),
            'role'     => 'admin',
            'statut'   => 'actif',
        ], $overrides));
    }

    protected function makeAnnonce(User $fournisseur, array $overrides = []): Annonce
    {
        $categorie = Categorie::first() ?? Categorie::create([
            'nom' => 'Fruits & Légumes', 'icone' => '🍎', 'couleur' => '#22c55e', 'description' => 'test',
        ]);

        return Annonce::create(array_merge([
            'user_id'          => $fournisseur->id,
            'categorie_id'     => $categorie->id,
            'titre'            => 'Annonce test ' . uniqid(),
            'description'      => 'Description de test',
            'quantite'         => 10,
            'unite'            => 'kg',
            'prix'             => 500,
            'type_offre'       => 'vente',
            'statut'           => 'disponible',
            'adresse_collecte' => 'Cocody, Abidjan',
        ], $overrides));
    }
}

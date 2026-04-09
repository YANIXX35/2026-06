<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categorie;

class CategorieSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['nom' => 'Fruits & Légumes',       'icone' => '🍎', 'couleur' => '#22c55e', 'description' => 'Fruits, légumes frais et de saison'],
            ['nom' => 'Produits Laitiers',       'icone' => '🥛', 'couleur' => '#3b82f6', 'description' => 'Lait, yaourt, fromage, beurre'],
            ['nom' => 'Pain & Pâtisserie',       'icone' => '🍞', 'couleur' => '#f97316', 'description' => 'Pain, viennoiseries, gâteaux du jour'],
            ['nom' => 'Viandes & Poissons',      'icone' => '🍖', 'couleur' => '#ef4444', 'description' => 'Viandes, poissons, fruits de mer'],
            ['nom' => 'Céréales & Légumineuses', 'icone' => '🌾', 'couleur' => '#eab308', 'description' => 'Riz, maïs, haricots, lentilles, igname'],
            ['nom' => 'Plats Cuisinés',          'icone' => '🍲', 'couleur' => '#8b5cf6', 'description' => 'Repas préparés, invendus de restaurants'],
            ['nom' => 'Épices & Condiments',     'icone' => '🧂', 'couleur' => '#06b6d4', 'description' => 'Épices, sauces, huiles, condiments'],
            ['nom' => 'Boissons',                'icone' => '🧃', 'couleur' => '#10b981', 'description' => 'Jus, boissons naturelles'],
        ];

        foreach ($categories as $cat) {
            Categorie::firstOrCreate(['nom' => $cat['nom']], $cat);
        }
    }
}

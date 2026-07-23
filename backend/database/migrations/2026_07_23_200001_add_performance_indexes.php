<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajouter des index sur les colonnes les plus filtrées pour accélérer les requêtes
     */
    public function up(): void
    {
        Schema::table('annonces', function (Blueprint $table) {
            // Index sur statut (utilisé dans presque toutes les requêtes)
            if (!$this->indexExists('annonces', 'annonces_statut_index')) {
                $table->index('statut');
            }
            // Index sur categorie_id (filtrage par catégorie)
            if (!$this->indexExists('annonces', 'annonces_categorie_id_created_at_index')) {
                $table->index(['categorie_id', 'created_at']);
            }
            // Index sur date_expiration (tri des urgents)
            if (!$this->indexExists('annonces', 'annonces_date_expiration_index')) {
                $table->index('date_expiration');
            }
            // Index sur type_offre (filtrage)
            if (!$this->indexExists('annonces', 'annonces_type_offre_index')) {
                $table->index('type_offre');
            }
        });

        // Index sur photos.annonce_id pour charger les photos rapidement
        Schema::table('photos', function (Blueprint $table) {
            if (!$this->indexExists('photos', 'photos_annonce_id_is_principale_index')) {
                $table->index(['annonce_id', 'is_principale']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('annonces', function (Blueprint $table) {
            $table->dropIndex(['statut']);
            $table->dropIndex(['categorie_id', 'created_at']);
            $table->dropIndex(['date_expiration']);
            $table->dropIndex(['type_offre']);
        });

        Schema::table('photos', function (Blueprint $table) {
            $table->dropIndex(['annonce_id', 'is_principale']);
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $indexes = \Illuminate\Support\Facades\DB::select(
                "SELECT indexname FROM pg_indexes WHERE tablename = ? AND indexname = ?",
                [$table, $indexName]
            );
            return count($indexes) > 0;
        } catch (\Throwable) {
            return false;
        }
    }
};

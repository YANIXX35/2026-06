<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajout de proposeur_id et est_contre_offre à la table offres.
 *
 * proposeur_id  : l'utilisateur qui a proposé cette offre (acheteur OU
 *                 fournisseur). Avant ce champ, on ne pouvait distinguer
 *                 l'initiateur que par convention (acheteur_id = initiateur).
 *                 Avec la contre-offre, le fournisseur peut aussi initier,
 *                 d'où la nécessité d'un champ explicite.
 *
 * est_contre_offre : booléen ; true si c'est le fournisseur qui a re-proposé
 *                    un prix à la suite d'une offre de l'acheteur.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offres', function (Blueprint $table) {
            if (!Schema::hasColumn('offres', 'proposeur_id')) {
                // nullable pour la rétro-compatibilité avec les offres existantes
                // (on ne peut pas recalculer a posteriori qui a proposé)
                $table->foreignId('proposeur_id')
                      ->nullable()
                      ->constrained('users')
                      ->onDelete('set null')
                      ->after('fournisseur_id');
            }
            if (!Schema::hasColumn('offres', 'est_contre_offre')) {
                $table->boolean('est_contre_offre')->default(false)->after('proposeur_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('offres', function (Blueprint $table) {
            if (Schema::hasColumn('offres', 'proposeur_id')) {
                $table->dropConstrainedForeignId('proposeur_id');
            }
            if (Schema::hasColumn('offres', 'est_contre_offre')) {
                $table->dropColumn('est_contre_offre');
            }
        });
    }
};

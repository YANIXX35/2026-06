<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->index(['user_id', 'statut']);
            $table->index('statut');
        });

        Schema::table('offres', function (Blueprint $table) {
            $table->index(['fournisseur_id', 'statut']);
            $table->index(['acheteur_id', 'statut']);
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'statut']);
            $table->dropIndex(['statut']);
        });

        Schema::table('offres', function (Blueprint $table) {
            $table->dropIndex(['fournisseur_id', 'statut']);
            $table->dropIndex(['acheteur_id', 'statut']);
            $table->dropIndex(['statut']);
        });
    }
};

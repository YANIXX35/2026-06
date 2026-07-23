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
        Schema::table('annonces', function (Blueprint $table) {
            if (!Schema::hasColumn('annonces', 'prix_original')) {
                $table->decimal('prix_original', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('annonces', 'est_panier_mystere')) {
                $table->boolean('est_panier_mystere')->default(false);
            }
            if (!Schema::hasColumn('annonces', 'poids_estime_kg')) {
                $table->decimal('poids_estime_kg', 8, 2)->default(1.0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('annonces', function (Blueprint $table) {
            if (Schema::hasColumn('annonces', 'prix_original')) {
                $table->dropColumn('prix_original');
            }
            if (Schema::hasColumn('annonces', 'est_panier_mystere')) {
                $table->dropColumn('est_panier_mystere');
            }
            if (Schema::hasColumn('annonces', 'poids_estime_kg')) {
                $table->dropColumn('poids_estime_kg');
            }
        });
    }
};

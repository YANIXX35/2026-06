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
            $table->decimal('prix_original', 10, 2)->nullable()->after('prix');
            $table->boolean('est_panier_mystere')->default(false)->after('type_offre');
            $table->decimal('poids_estime_kg', 8, 2)->default(1.0)->after('unite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('annonces', function (Blueprint $table) {
            $table->dropColumn(['prix_original', 'est_panier_mystere', 'poids_estime_kg']);
        });
    }
};

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
        Schema::table('commandes', function (Blueprint $table) {
            $table->string('mode_paiement')->nullable()->after('message');
            $table->string('telephone_paiement')->nullable()->after('mode_paiement');
            $table->string('reference_paiement')->nullable()->after('telephone_paiement');
            $table->string('statut_paiement')->default('non_payé')->after('reference_paiement');
        });
    }

    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropColumn(['mode_paiement', 'telephone_paiement', 'reference_paiement', 'statut_paiement']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->decimal('commission_taux', 5, 4)->default(0.1500)->after('montant_total'); // 0.15 = 15%
            $table->decimal('montant_commission', 12, 2)->default(0.00)->after('commission_taux');
            $table->decimal('montant_net_fournisseur', 12, 2)->default(0.00)->after('montant_commission');
        });

        Schema::table('commande_items', function (Blueprint $table) {
            $table->decimal('commission_montant', 10, 2)->default(0.00)->after('prix_unitaire');
            $table->decimal('montant_net', 10, 2)->default(0.00)->after('commission_montant');
        });
    }

    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropColumn(['commission_taux', 'montant_commission', 'montant_net_fournisseur']);
        });

        Schema::table('commande_items', function (Blueprint $table) {
            $table->dropColumn(['commission_montant', 'montant_net']);
        });
    }
};

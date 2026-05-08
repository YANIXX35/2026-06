<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('statut')->default('en_attente'); // en_attente, confirmée, annulée
            $table->decimal('montant_total', 12, 2)->default(0);
            $table->string('adresse_livraison')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();
        });

        Schema::create('commande_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained()->cascadeOnDelete();
            $table->foreignId('annonce_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fournisseur_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('quantite', 10, 2);
            $table->decimal('prix_unitaire', 10, 2)->default(0);
            $table->string('statut')->default('en_attente'); // en_attente, accepté, refusé, preparé, livré
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commande_items');
        Schema::dropIfExists('commandes');
    }
};

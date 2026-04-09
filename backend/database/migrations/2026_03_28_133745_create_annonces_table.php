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
        Schema::create('annonces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('categorie_id')->constrained()->onDelete('restrict');
            $table->string('titre');
            $table->text('description');
            $table->decimal('quantite', 10, 2);
            $table->string('unite')->default('kg');
            $table->decimal('prix', 10, 2)->default(0);
            $table->enum('type_offre', ['vente', 'don', 'alimentation_animale', 'transformation'])->default('vente');
            $table->enum('statut', ['disponible', 'reservé', 'expiré', 'supprimé'])->default('disponible');
            $table->datetime('date_expiration')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('adresse_collecte')->nullable();
            $table->unsignedInteger('vues')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annonces');
    }
};

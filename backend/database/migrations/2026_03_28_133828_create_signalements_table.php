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
        Schema::create('signalements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('annonce_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('signale_user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('raison');
            $table->text('description')->nullable();
            $table->enum('statut', ['en_attente', 'traité', 'rejeté'])->default('en_attente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signalements');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'prix_negocie')) {
                $table->decimal('prix_negocie', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('reservations', 'offre_id')) {
                $table->foreignId('offre_id')->nullable()->constrained('offres')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (Schema::hasColumn('reservations', 'offre_id')) {
                $table->dropConstrainedForeignId('offre_id');
            }
            if (Schema::hasColumn('reservations', 'prix_negocie')) {
                $table->dropColumn('prix_negocie');
            }
        });
    }
};

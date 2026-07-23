<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // En PostgreSQL (utilisé sur Render), modifier le type via SQL brut est 100% garanti de fonctionner
        // sans nécessiter le package doctrine/dbal qui peut parfois causer des échecs silencieux.
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE photos ALTER COLUMN url TYPE TEXT');
        } else {
            Schema::table('photos', function (Blueprint $table) {
                $table->longText('url')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE photos ALTER COLUMN url TYPE VARCHAR(255)');
        } else {
            Schema::table('photos', function (Blueprint $table) {
                $table->string('url', 255)->change();
            });
        }
    }
};

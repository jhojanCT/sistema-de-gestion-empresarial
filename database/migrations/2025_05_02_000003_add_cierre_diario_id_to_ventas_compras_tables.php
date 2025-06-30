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
        Schema::table('ventas', function (Blueprint $table) {
            $table->foreignId('cierre_diario_id')->nullable()->constrained('cierres_diarios')->onDelete('restrict');
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->foreignId('cierre_diario_id')->nullable()->constrained('cierres_diarios')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['cierre_diario_id']);
            $table->dropColumn('cierre_diario_id');
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->dropForeign(['cierre_diario_id']);
            $table->dropColumn('cierre_diario_id');
        });
    }
}; 
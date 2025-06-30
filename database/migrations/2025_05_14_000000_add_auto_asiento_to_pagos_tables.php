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
        Schema::table('pagos_clientes', function (Blueprint $table) {
            $table->boolean('auto_asiento')->default(false)->after('cierre_diario_id');
        });

        Schema::table('pagos_proveedores', function (Blueprint $table) {
            $table->boolean('auto_asiento')->default(false)->after('cierre_diario_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagos_clientes', function (Blueprint $table) {
            $table->dropColumn('auto_asiento');
        });

        Schema::table('pagos_proveedores', function (Blueprint $table) {
            $table->dropColumn('auto_asiento');
        });
    }
}; 
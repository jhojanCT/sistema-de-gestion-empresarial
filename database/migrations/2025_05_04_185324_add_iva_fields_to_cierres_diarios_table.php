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
        Schema::table('cierres_diarios', function (Blueprint $table) {
            $table->decimal('iva_ventas_contado', 10, 2)->default(0);
            $table->decimal('iva_ventas_credito', 10, 2)->default(0);
            $table->decimal('iva_compras_contado', 10, 2)->default(0);
            $table->decimal('iva_compras_credito', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cierres_diarios', function (Blueprint $table) {
            $table->dropColumn([
                'iva_ventas_contado',
                'iva_ventas_credito',
                'iva_compras_contado',
                'iva_compras_credito'
            ]);
        });
    }
};

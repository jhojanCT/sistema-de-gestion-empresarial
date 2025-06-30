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
        Schema::create('pagos_clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained();
            $table->decimal('monto', 12, 2);
            $table->date('fecha_pago');
            $table->string('metodo_pago');
            $table->string('comprobante')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos_clientes');
    }
};

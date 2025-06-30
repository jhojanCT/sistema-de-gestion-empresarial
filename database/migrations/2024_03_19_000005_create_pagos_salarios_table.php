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
        Schema::create('pagos_salarios', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_pago');
            $table->decimal('monto_total', 12, 2);
            $table->string('metodo_pago');
            $table->string('comprobante')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('auto_asiento')->default(false);
            $table->boolean('asiento_generado')->default(false);
            $table->foreignId('cierre_diario_id')->nullable()->constrained('cierres_diarios');
            $table->timestamps();
        });

        Schema::create('detalle_pagos_salarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pago_salario_id')->constrained('pagos_salarios')->onDelete('cascade');
            $table->foreignId('empleado_id')->constrained();
            $table->decimal('monto', 12, 2);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_pagos_salarios');
        Schema::dropIfExists('pagos_salarios');
    }
}; 
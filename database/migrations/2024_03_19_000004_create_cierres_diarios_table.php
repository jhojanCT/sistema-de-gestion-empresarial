<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cierres_diarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users');
            $table->date('fecha');
            $table->decimal('ventas_contado', 12, 2)->default(0);
            $table->decimal('ventas_credito', 12, 2)->default(0);
            $table->decimal('compras_contado', 12, 2)->default(0);
            $table->decimal('compras_credito', 12, 2)->default(0);
            $table->decimal('cobros_credito', 12, 2)->default(0);
            $table->decimal('pagos_credito', 12, 2)->default(0);
            $table->decimal('gastos', 12, 2)->default(0);
            $table->decimal('otros_ingresos', 12, 2)->default(0);
            $table->decimal('saldo_inicial', 12, 2)->default(0);
            $table->decimal('saldo_final', 12, 2)->default(0);
            $table->decimal('diferencia', 12, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->boolean('cerrado')->default(false);
            $table->timestamp('fecha_cierre')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cierres_diarios');
    }
}; 
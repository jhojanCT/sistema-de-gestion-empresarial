<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asientos_contables', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->string('numero_asiento', 20)->unique();
            $table->string('tipo_documento', 50);
            $table->string('numero_documento', 50);
            $table->text('concepto');
            $table->enum('estado', ['BORRADOR', 'APROBADO', 'ANULADO'])->default('BORRADOR');
            $table->foreignId('centro_costo_id')->nullable()->constrained('centros_costo')->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->decimal('monto_total', 15, 2)->default(0);
            $table->decimal('saldo_pendiente', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asientos_contables');
    }
}; 
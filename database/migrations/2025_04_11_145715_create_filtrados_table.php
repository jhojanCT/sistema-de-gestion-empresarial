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
        Schema::create('filtrados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materia_prima_sin_filtrar_id')->constrained('materia_prima_sin_filtrar');
            $table->decimal('cantidad_entrada', 10, 2);
            $table->decimal('cantidad_salida', 10, 2);
            $table->decimal('desperdicio', 10, 2); // Calculado: entrada - salida
            $table->date('fecha');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filtrados');
    }
};

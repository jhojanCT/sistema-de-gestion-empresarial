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
        Schema::create('producciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materia_prima_filtrada_id')->constrained('materia_prima_filtrada');
            $table->decimal('cantidad_utilizada', 10, 2);
            $table->foreignId('producto_id')->constrained();
            $table->decimal('cantidad_producida', 10, 2);
            $table->decimal('costo_produccion', 12, 2); // calculado
            $table->date('fecha');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producciones');
    }
};

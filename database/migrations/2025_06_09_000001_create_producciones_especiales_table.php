<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('producciones_especiales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materia_prima_sin_filtrar_id')->constrained('materia_prima_sin_filtrar');
            $table->foreignId('producto_id')->constrained();
            $table->decimal('cantidad_utilizada', 10, 2);
            $table->decimal('cantidad_producida', 10, 2);
            $table->decimal('costo_produccion', 12, 2);
            $table->date('fecha');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('producciones_especiales');
    }
}; 
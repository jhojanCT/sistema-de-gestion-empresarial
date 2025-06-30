<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('materia_prima_molida', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materia_prima_filtrada_id')->constrained('materia_prima_filtrada')->onDelete('cascade');
            $table->decimal('cantidad', 12, 3);
            $table->date('fecha_molido');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('materia_prima_molida');
    }
}; 
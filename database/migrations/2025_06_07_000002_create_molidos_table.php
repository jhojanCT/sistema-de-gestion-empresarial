<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('molidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materia_prima_filtrada_id')->constrained('materia_prima_filtrada')->onDelete('cascade');
            $table->decimal('cantidad_entrada', 12, 3);
            $table->decimal('cantidad_salida', 12, 3);
            $table->date('fecha');
            $table->text('observaciones')->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('molidos');
    }
}; 
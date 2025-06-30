<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('producciones', function (Blueprint $table) {
            $table->dropForeign(['materia_prima_filtrada_id']);
            $table->dropColumn('materia_prima_filtrada_id');
            $table->foreignId('materia_prima_molida_id')->after('id')->constrained('materia_prima_molida');
        });
    }

    public function down()
    {
        Schema::table('producciones', function (Blueprint $table) {
            $table->dropForeign(['materia_prima_molida_id']);
            $table->dropColumn('materia_prima_molida_id');
            $table->foreignId('materia_prima_filtrada_id')->after('id')->constrained('materia_prima_filtrada');
        });
    }
}; 
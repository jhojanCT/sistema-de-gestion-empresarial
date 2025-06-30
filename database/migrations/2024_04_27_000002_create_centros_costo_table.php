<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('centros_costo', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->string('tipo', 50); // PRODUCCION, SERVICIO, ADMINISTRATIVO
            $table->boolean('es_auxiliar')->default(false);
            $table->unsignedBigInteger('centro_costo_padre_id')->nullable();
            $table->decimal('presupuesto_mensual', 12, 2)->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('centro_costo_padre_id')
                  ->references('id')
                  ->on('centros_costo')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('centros_costo');
    }
}; 
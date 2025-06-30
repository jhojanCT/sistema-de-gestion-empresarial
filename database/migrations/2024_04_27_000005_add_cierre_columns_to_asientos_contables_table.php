<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asientos_contables', function (Blueprint $table) {
            // No se necesita agregar ninguna columna nueva
        });
    }

    public function down(): void
    {
        Schema::table('asientos_contables', function (Blueprint $table) {
            // No se necesita eliminar ninguna columna
        });
    }
}; 
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->enum('metodo_pago', ['efectivo', 'transferencia'])->nullable()->after('total');
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->enum('metodo_pago', ['efectivo', 'transferencia'])->nullable()->after('total');
        });
    }

    public function down()
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn('metodo_pago');
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->dropColumn('metodo_pago');
        });
    }
}; 
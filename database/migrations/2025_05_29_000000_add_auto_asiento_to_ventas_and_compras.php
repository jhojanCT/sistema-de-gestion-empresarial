<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->boolean('auto_asiento')->default(false)->after('total');
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->boolean('auto_asiento')->default(false)->after('total');
        });
    }

    public function down()
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn('auto_asiento');
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->dropColumn('auto_asiento');
        });
    }
}; 
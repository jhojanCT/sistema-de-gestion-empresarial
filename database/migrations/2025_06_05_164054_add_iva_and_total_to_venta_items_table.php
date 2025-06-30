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
        Schema::table('venta_items', function (Blueprint $table) {
            $table->decimal('iva_amount', 12, 2)->default(0.00)->after('subtotal');
            $table->decimal('total', 12, 2)->default(0.00)->after('iva_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venta_items', function (Blueprint $table) {
            $table->dropColumn('iva_amount');
            $table->dropColumn('total');
        });
    }
};

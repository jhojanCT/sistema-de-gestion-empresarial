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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->nullable()->constrained();
            $table->enum('tipo', ['contado', 'credito']);
            $table->date('fecha');
            $table->boolean('has_invoice')->default(false);
            $table->string('invoice_number')->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('iva_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->boolean('pagada')->default(false); // Para crédito
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};

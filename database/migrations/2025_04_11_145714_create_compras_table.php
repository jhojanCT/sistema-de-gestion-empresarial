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
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->constrained('proveedores')->onDelete('restrict');
            $table->foreignId('centro_costo_id')->nullable()->constrained('centros_costo')->onDelete('restrict');
            $table->enum('tipo', ['contado', 'credito']);
            $table->date('fecha');
            $table->boolean('has_invoice')->default(false);
            $table->string('invoice_number')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('iva_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->boolean('pagada')->default(false); // Para crédito
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};

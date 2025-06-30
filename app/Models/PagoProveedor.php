<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoProveedor extends Model
{
    use HasFactory;
    protected $table = 'pagos_proveedores';
    protected $casts = [
        'fecha_pago' => 'datetime',
        'monto' => 'decimal:2',
        'auto_asiento' => 'boolean'
    ];

    protected $fillable = [
        'compra_id',
        'monto',
        'fecha_pago',
        'metodo_pago', // efectivo, transferencia, etc.
        'comprobante', // Número de factura o referencia
        'cierre_diario_id',
        'auto_asiento'
    ];

    protected $attributes = [
        'auto_asiento' => false
    ];

    /**
     * Obtiene la compra asociada al pago
     */
    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class);
    }

    public function cierreDiario()
    {
        return $this->belongsTo(CierreDiario::class);
    }

    /**
     * Obtiene el asiento contable asociado al pago
     */
    public function asientoContable()
    {
        return $this->hasOne(AsientoContable::class, 'numero_documento', 'id')
            ->where('tipo_documento', 'PAGO_PROVEEDOR');
    }
}
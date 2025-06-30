<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoCliente extends Model
{
    use HasFactory;

    protected $table = 'pagos_clientes';
    protected $casts = [
        'fecha_pago' => 'datetime',
        'monto' => 'decimal:2',
        'auto_asiento' => 'boolean'
    ];
    

    protected $fillable = [
        'venta_id',
        'monto',
        'fecha_pago',
        'metodo_pago',
        'comprobante',
        'cierre_diario_id',
        'auto_asiento'
    ];

    protected $attributes = [
        'auto_asiento' => false
    ];

    /**
     * Obtiene la venta asociada al pago
     */
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
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
            ->where('tipo_documento', 'COBRO_CLIENTE');
    }
}
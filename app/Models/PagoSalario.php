<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoSalario extends Model
{
    use HasFactory;

    protected $table = 'pagos_salarios';
    
    protected $casts = [
        'fecha_pago' => 'date',
        'monto_total' => 'decimal:2',
        'auto_asiento' => 'boolean'
    ];

    protected $fillable = [
        'fecha_pago',
        'monto_total',
        'metodo_pago',
        'comprobante',
        'observaciones',
        'auto_asiento',
        'cierre_diario_id'
    ];

    protected $attributes = [
        'auto_asiento' => false
    ];

    /**
     * Obtiene los detalles del pago de salarios
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(DetallePagoSalario::class);
    }

    /**
     * Obtiene el cierre diario asociado al pago
     */
    public function cierreDiario(): BelongsTo
    {
        return $this->belongsTo(CierreDiario::class);
    }

    /**
     * Obtiene el asiento contable asociado al pago
     */
    public function asientoContable()
    {
        return $this->hasOne(AsientoContable::class, 'numero_documento', 'id')
            ->where('tipo_documento', 'PAGO_SALARIO');
    }
} 
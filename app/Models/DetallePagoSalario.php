<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetallePagoSalario extends Model
{
    use HasFactory;

    protected $table = 'detalle_pagos_salarios';
    
    protected $casts = [
        'monto' => 'decimal:2'
    ];

    protected $fillable = [
        'pago_salario_id',
        'empleado_id',
        'monto',
        'observaciones'
    ];

    /**
     * Obtiene el pago de salario asociado al detalle
     */
    public function pagoSalario(): BelongsTo
    {
        return $this->belongsTo(PagoSalario::class);
    }

    /**
     * Obtiene el empleado asociado al detalle
     */
    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }
} 
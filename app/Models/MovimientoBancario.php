<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MovimientoBancario extends Model
{
    protected $table = 'movimientos_bancarios';

    protected $fillable = [
        'tipo',
        'monto',
        'fecha',
        'concepto',
        'movimiento_type',
        'movimiento_id'
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'monto' => 'decimal:2'
    ];

    /**
     * Obtiene el modelo relacionado al movimiento (polimórfico)
     */
    public function movimiento(): MorphTo
    {
        return $this->morphTo();
    }
} 
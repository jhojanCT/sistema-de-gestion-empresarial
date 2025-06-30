<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'venta_id',
        'tipo_item',
        'materia_prima_filtrada_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function materiaPrimaFiltrada()
    {
        return $this->belongsTo(MateriaPrimaFiltrada::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
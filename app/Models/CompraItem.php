<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompraItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'compra_id',
        'tipo_item',
        'materia_prima_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    public function materiaPrima()
    {
        return $this->belongsTo(MateriaPrimaSinFiltrar::class, 'materia_prima_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
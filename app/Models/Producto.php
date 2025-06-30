<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'stock',
        'precio_venta',
        'tipo',
        'unidad_medida'
    ];

    public function producciones()
    {
        return $this->hasMany(Produccion::class);
    }

    public function ventas()
    {
        return $this->hasManyThrough(Venta::class, VentaItem::class, 'producto_id', 'id', 'id', 'venta_id');
    }

    public function compras()
    {
        return $this->hasManyThrough(Compra::class, CompraItem::class, 'producto_id', 'id', 'id', 'compra_id');
    }
}
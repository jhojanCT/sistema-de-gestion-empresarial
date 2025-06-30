<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MateriaPrimaSinFiltrar extends Model
{
    use HasFactory;

    protected $table = 'materia_prima_sin_filtrar';

    protected $fillable = [
        'nombre',
        'descripcion',
        'stock',
        'unidad_medida',
    ];

    public function filtrados()
    {
        return $this->hasMany(Filtrado::class);
    }

    public function compraItems()
    {
        return $this->hasMany(CompraItem::class, 'materia_prima_id');
    }

    public function compras()
    {
        return $this->belongsToMany(Compra::class, 'compra_items', 'materia_prima_id', 'compra_id')
                    ->withPivot('precio_unitario', 'cantidad')
                    ->orderBy('fecha', 'desc');
    }
}

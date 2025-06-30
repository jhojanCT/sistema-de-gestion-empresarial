<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MateriaPrimaFiltrada extends Model
{
    use HasFactory;

    protected $table = 'materia_prima_filtrada';

    protected $fillable = [
        'nombre',
        'stock',
        'unidad_medida',
    ];

    public function producciones()
    {
        return $this->hasMany(Produccion::class);
    }
    public function materiaPrimaSinFiltrar()
    {
        return $this->belongsTo(MateriaPrimaSinFiltrar::class, 'materia_prima_id');
    }

    public function ventas()
    {
        return $this->belongsToMany(Venta::class, 'venta_items', 'materia_prima_filtrada_id', 'venta_id')
                    ->withPivot('precio_unitario', 'cantidad')
                    ->orderBy('fecha', 'desc');
    }

    public function molidos()
    {
        return $this->hasMany(Molido::class);
    }

    public function materiaPrimaMolida()
    {
        return $this->hasMany(MateriaPrimaMolida::class);
    }

}

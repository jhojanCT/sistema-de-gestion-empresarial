<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filtrado extends Model
{
    use HasFactory;
    protected $casts = [
        'fecha' => 'date',
    ];
    

    protected $fillable = [
        'materia_prima_sin_filtrar_id',
        'cantidad_entrada',
        'cantidad_salida',
        'desperdicio',
        'fecha'
    ];

    public function materiaPrimaSinFiltrar()
    {
        return $this->belongsTo(MateriaPrimaSinFiltrar::class);
    }
    public function materiaPrimaFiltrada()
        {
    return $this->belongsTo(MateriaPrimaFiltrada::class, 'materia_prima_filtrada_id');
    }

    
}
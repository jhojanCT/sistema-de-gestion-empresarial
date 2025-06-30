<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produccion extends Model
{
    use HasFactory;
    protected $casts = [
        'fecha' => 'date',
    ];
    protected $table = 'producciones';

    protected $fillable = [
        'materia_prima_filtrada_id',
        'producto_id',
        'cantidad_utilizada',
        'cantidad_producida',
        'costo_produccion',
        'fecha'
    ];

    public function materiaPrimaMolida()
    {
        return $this->belongsTo(MateriaPrimaMolida::class, 'materia_prima_molida_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
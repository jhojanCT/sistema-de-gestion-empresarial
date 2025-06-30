<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MateriaPrimaMolida extends Model
{
    use HasFactory;

    protected $table = 'materia_prima_molida';

    protected $fillable = [
        'materia_prima_filtrada_id',
        'cantidad',
        'fecha_molido',
        'observaciones',
    ];

    public function materiaPrimaFiltrada()
    {
        return $this->belongsTo(MateriaPrimaFiltrada::class);
    }
} 
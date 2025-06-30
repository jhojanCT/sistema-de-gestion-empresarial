<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Molido extends Model
{
    use HasFactory;

    protected $table = 'molidos';

    protected $fillable = [
        'materia_prima_filtrada_id',
        'cantidad_entrada',
        'cantidad_salida',
        'fecha',
        'observaciones',
        'usuario_id',
    ];

    public function materiaPrimaFiltrada()
    {
        return $this->belongsTo(MateriaPrimaFiltrada::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
} 
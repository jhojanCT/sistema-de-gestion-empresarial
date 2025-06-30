<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduccionEspecial extends Model
{
    use HasFactory;

    protected $table = 'producciones_especiales';

    protected $fillable = [
        'materia_prima_sin_filtrar_id',
        'producto_id',
        'cantidad_utilizada',
        'cantidad_producida',
        'costo_produccion',
        'fecha',
        'observaciones',
    ];

    public function materiaPrimaSinFiltrar()
    {
        return $this->belongsTo(MateriaPrimaSinFiltrar::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
} 
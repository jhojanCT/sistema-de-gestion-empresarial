<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CentroCosto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'centros_costo';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'tipo',
        'es_auxiliar',
        'centro_costo_padre_id',
        'presupuesto_mensual',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'es_auxiliar' => 'boolean',
        'presupuesto_mensual' => 'decimal:2'
    ];

    // Relaciones
    public function padre()
    {
        return $this->belongsTo(CentroCosto::class, 'centro_costo_padre_id');
    }

    public function hijos()
    {
        return $this->hasMany(CentroCosto::class, 'centro_costo_padre_id');
    }

    public function asientos()
    {
        return $this->hasMany(AsientoContable::class, 'centro_costo_id');
    }

    public function detallesAsiento()
    {
        return $this->hasMany(DetalleAsiento::class, 'centro_costo_id');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePrincipales($query)
    {
        return $query->whereNull('centro_costo_padre_id');
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Métodos
    public function getTotalAsignadoAttribute()
    {
        return $this->detallesAsiento()->sum('debe');
    }

    public function getTotalGastadoMesActual()
    {
        $mesActual = now()->format('Y-m');
        return $this->detallesAsiento()
            ->whereHas('asiento', function($query) use ($mesActual) {
                $query->whereRaw("DATE_FORMAT(fecha, '%Y-%m') = ?", [$mesActual]);
            })
            ->sum('debe');
    }

    public function getVariacionPresupuestalMesActual()
    {
        return $this->presupuesto_mensual - $this->getTotalGastadoMesActual();
    }

    public function getPorcentajeEjecucionMesActual()
    {
        if ($this->presupuesto_mensual == 0) return 0;
        return ($this->getTotalGastadoMesActual() / $this->presupuesto_mensual) * 100;
    }

    public function getTotalAcumuladoAnual()
    {
        $añoActual = now()->year;
        return $this->detallesAsiento()
            ->whereHas('asiento', function($query) use ($añoActual) {
                $query->whereYear('fecha', $añoActual);
            })
            ->sum('debe');
    }
} 
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CuentaContable extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cuentas_contables';

    protected $fillable = [
        'codigo',
        'nombre',
        'tipo',
        'naturaleza',
        'cuenta_padre_id',
        'nivel',
        'es_centro_costo',
        'activo'
    ];

    protected $casts = [
        'es_centro_costo' => 'boolean',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function cuentaPadre()
    {
        return $this->belongsTo(CuentaContable::class, 'cuenta_padre_id');
    }

    public function cuentasHijas()
    {
        return $this->hasMany(CuentaContable::class, 'cuenta_padre_id');
    }

    public function detallesAsiento()
    {
        return $this->hasMany(DetalleAsiento::class, 'cuenta_id');
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Métodos
    public function getSaldoActualAttribute()
    {
        $debe = $this->detallesAsiento()->sum('debe');
        $haber = $this->detallesAsiento()->sum('haber');
        
        return $this->naturaleza === 'DEUDORA' 
            ? $debe - $haber 
            : $haber - $debe;
    }

    /**
     * Calcula el saldo total de la cuenta incluyendo todas las cuentas hijas
     *
     * @return float Saldo total calculado
     */
    public function getSaldoTotalAttribute()
    {
        $saldo = $this->getSaldoActualAttribute();
        foreach ($this->cuentasHijas as $hija) {
            $saldo += $hija->getSaldoTotalAttribute();
        }
        return $saldo;
    }

    /**
     * Calcula el saldo de la cuenta hasta una fecha específica
     *
     * @param string $fecha Fecha hasta la cual calcular el saldo (formato Y-m-d)
     * @return float Saldo calculado
     */
    public function calcularSaldo($fecha)
    {
        $debe = $this->detallesAsiento()
            ->whereHas('asiento', function($query) use ($fecha) {
                $query->where('fecha', '<=', $fecha)
                      ->where('estado', 'aprobado');
            })
            ->sum('debe');
            
        $haber = $this->detallesAsiento()
            ->whereHas('asiento', function($query) use ($fecha) {
                $query->where('fecha', '<=', $fecha)
                      ->where('estado', 'aprobado');
            })
            ->sum('haber');
        
        return $this->naturaleza === 'DEUDORA' 
            ? $debe - $haber 
            : $haber - $debe;
    }
} 
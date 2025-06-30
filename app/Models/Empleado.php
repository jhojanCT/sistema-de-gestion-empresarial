<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'ci',
        'cargo',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relación con los detalles de pago de salario
    public function detallesPagoSalario()
    {
        return $this->hasMany(DetallePagoSalario::class);
    }

    // Relación con los pagos de salario a través de los detalles
    public function pagosSalarios()
    {
        return $this->belongsToMany(PagoSalario::class, 'detalle_pagos_salarios', 'empleado_id', 'pago_salario_id')
            ->withPivot(['monto', 'observaciones'])
            ->withTimestamps();
    }

    // Accesor para nombre completo
    public function getNombreCompletoAttribute()
    {
        return $this->apellido 
            ? "{$this->nombre} {$this->apellido}"
            : $this->nombre;
    }

    // Scope para empleados activos
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
} 
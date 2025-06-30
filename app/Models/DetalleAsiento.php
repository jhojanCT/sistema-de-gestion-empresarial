<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class DetalleAsiento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'detalles_asiento';

    protected $fillable = [
        'asiento_id',
        'cuenta_id',
        'centro_costo_id',
        'debe',
        'haber',
        'descripcion'
    ];

    protected $casts = [
        'debe' => 'decimal:2',
        'haber' => 'decimal:2',
    ];

    // Accessors
    protected function debe(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (float) $value,
            set: fn ($value) => (float) $value
        );
    }

    protected function haber(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (float) $value,
            set: fn ($value) => (float) $value
        );
    }

    // Relaciones
    public function asiento()
    {
        return $this->belongsTo(AsientoContable::class, 'asiento_id');
    }

    public function cuenta()
    {
        return $this->belongsTo(CuentaContable::class, 'cuenta_id');
    }

    public function centroCosto()
    {
        return $this->belongsTo(CentroCosto::class, 'centro_costo_id');
    }

    // Métodos
    public function getSaldoAttribute()
    {
        return $this->debe - $this->haber;
    }
} 
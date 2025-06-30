<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';
    use HasFactory;

    protected $fillable = [
        'nombre',
        'telefono',
        'direccion',
        'email'
    ];

    // App\Models\Proveedor.php
    
    public function compras()
    {
        return $this->hasMany(\App\Models\Compra::class);
    }
    public function getRouteKeyName()
    {
        return 'id';
    }

}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Compra extends Model
{
    use HasFactory, SoftDeletes;
    protected $casts = [
        'fecha' => 'date',
        'pagada' => 'boolean',
        'has_invoice' => 'boolean',
        'auto_asiento' => 'boolean',
    ];

    protected $fillable = [
        'proveedor_id',
        'centro_costo_id',
        'tipo',
        'fecha',
        'has_invoice',
        'invoice_number',
        'subtotal',
        'iva_amount',
        'total',
        'pagada',
        'auto_asiento',
        'metodo_pago'
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function centroCosto()
    {
        return $this->belongsTo(CentroCosto::class);
    }

    public function items()
    {
        return $this->hasMany(CompraItem::class);
    }

    public function pagos()
    {
        return $this->hasMany(PagoProveedor::class);
    }
    
    public function saldoPendiente()
    {
        return $this->total - $this->pagos()->sum('monto');
    }

    public function asientoContable()
    {
        return $this->hasOne(AsientoContable::class, 'numero_documento', 'id')
                    ->where('tipo_documento', 'COMPRA');
    }
}
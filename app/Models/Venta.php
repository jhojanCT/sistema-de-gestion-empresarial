<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';
    use HasFactory;
    protected $casts = [
        'fecha' => 'date',
        'pagada' => 'boolean',
        'has_invoice' => 'boolean',
        'auto_asiento' => 'boolean',
    ];
    

    protected $fillable = [
        'cliente_id',
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

    protected $attributes = [
        'pagada' => false
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function items()
    {
        return $this->hasMany(VentaItem::class);
    }

    public function pagos()
    {
        return $this->hasMany(PagoCliente::class);
    }
    
    public function saldoPendiente()
    {
        return $this->total - $this->pagos()->sum('monto');
    }

    public function asientoContable()
    {
        return $this->hasOne(AsientoContable::class, 'numero_documento', 'id')
                    ->where('tipo_documento', 'VENTA');
    }
}
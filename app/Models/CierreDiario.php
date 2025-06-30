<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class CierreDiario extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cierres_diarios';

    protected $fillable = [
        'usuario_id',
        'fecha',
        'ventas_contado',
        'ventas_credito',
        'compras_contado',
        'compras_credito',
        'cobros_credito',
        'pagos_credito',
        'gastos',
        'otros_ingresos',
        'saldo_inicial',
        'saldo_final',
        'diferencia',
        'observaciones',
        'cerrado',
        'fecha_cierre',
        'iva_ventas_contado',
        'iva_ventas_credito',
        'iva_compras_contado',
        'iva_compras_credito'
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_cierre' => 'datetime',
        'cerrado' => 'boolean',
        'ventas_contado' => 'decimal:2',
        'ventas_credito' => 'decimal:2',
        'compras_contado' => 'decimal:2',
        'compras_credito' => 'decimal:2',
        'cobros_credito' => 'decimal:2',
        'pagos_credito' => 'decimal:2',
        'gastos' => 'decimal:2',
        'otros_ingresos' => 'decimal:2',
        'saldo_inicial' => 'decimal:2',
        'saldo_final' => 'decimal:2',
        'diferencia' => 'decimal:2',
        'iva_ventas_contado' => 'decimal:2',
        'iva_ventas_credito' => 'decimal:2',
        'iva_compras_contado' => 'decimal:2',
        'iva_compras_credito' => 'decimal:2'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function asientos()
    {
        return $this->hasMany(AsientoContable::class, 'cierre_diario_id');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'cierre_diario_id');
    }

    public function compras()
    {
        return $this->hasMany(Compra::class, 'cierre_diario_id');
    }

    public function pagosClientes()
    {
        return $this->hasMany(PagoCliente::class, 'cierre_diario_id');
    }

    public function pagosProveedores()
    {
        return $this->hasMany(PagoProveedor::class, 'cierre_diario_id');
    }

    public function pagos()
    {
        return $this->pagosClientes()->union(
            $this->pagosProveedores()->select([
                'id',
                'monto',
                'fecha_pago',
                'metodo_pago',
                'comprobante',
                'cierre_diario_id',
                DB::raw("'proveedor' as tipo"),
                'compra_id as referencia_id'
            ])
        )->select([
            'id',
            'monto',
            'fecha_pago',
            'metodo_pago',
            'comprobante',
            'cierre_diario_id',
            DB::raw("'cliente' as tipo"),
            'venta_id as referencia_id'
        ]);
    }
} 
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AsientoContable extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'asientos_contables';

    protected $fillable = [
        'fecha',
        'numero_asiento',
        'tipo_documento',
        'numero_documento',
        'concepto',
        'estado',
        'centro_costo_id',
        'user_id',
        'cierre_anual_id',
        'tipo_operacion', // 'venta_credito', 'compra_credito', 'pago_venta', 'pago_compra'
        'documento_relacionado_id', // ID del documento relacionado (venta/compra)
        'fecha_vencimiento', // Para operaciones a crédito
        'monto_total',
        'saldo_pendiente'
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_vencimiento' => 'date',
        'total_debe' => 'float',
        'total_haber' => 'float',
        'monto_total' => 'float',
        'saldo_pendiente' => 'float',
        'estado' => 'string',
        'tipo_operacion' => 'string'
    ];

    protected $appends = ['total_debe', 'total_haber'];

    // Relaciones
    public function centroCosto()
    {
        return $this->belongsTo(CentroCosto::class, 'centro_costo_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleAsiento::class, 'asiento_id');
    }

    // Nuevas relaciones
    public function documentoRelacionado()
    {
        return $this->belongsTo(AsientoContable::class, 'documento_relacionado_id');
    }

    public function pagosRelacionados()
    {
        return $this->hasMany(AsientoContable::class, 'documento_relacionado_id');
    }

    // Scopes
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopePorFecha($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
    }

    // Nuevos scopes
    public function scopeVentasCredito($query)
    {
        return $query->where('tipo_operacion', 'venta_credito');
    }

    public function scopeComprasCredito($query)
    {
        return $query->where('tipo_operacion', 'compra_credito');
    }

    public function scopePagosPendientes($query)
    {
        return $query->where('saldo_pendiente', '>', 0);
    }

    // Métodos
    public function getTotalDebeAttribute()
    {
        return (float) $this->detalles->sum(function($detalle) {
            return (float) $detalle->debe;
        });
    }

    public function getTotalHaberAttribute()
    {
        return (float) $this->detalles->sum(function($detalle) {
            return (float) $detalle->haber;
        });
    }

    public function estaBalanceado()
    {
        return abs($this->total_debe - $this->total_haber) < 0.01;
    }

    /**
     * Obtiene las cuentas requeridas según el tipo de operación
     *
     * @return \Illuminate\Support\Collection IDs de las cuentas requeridas
     */
    protected function getCuentasRequeridasPorTipo()
    {
        $cuentasRequeridas = collect();

        switch ($this->tipo_operacion) {
            case 'venta_credito':
                $cuentasRequeridas = collect([
                    // Cuenta de clientes
                    CuentaContable::where('codigo', 'like', '1301%')->first()->id,
                    // Cuenta de ventas
                    CuentaContable::where('codigo', 'like', '4101%')->first()->id,
                    // Cuenta de IVA por cobrar
                    CuentaContable::where('codigo', 'like', '2408%')->first()->id,
                ]);
                break;
            case 'compra_credito':
                $cuentasRequeridas = collect([
                    // Cuenta de proveedores
                    CuentaContable::where('codigo', 'like', '2101%')->first()->id,
                    // Cuenta de compras
                    CuentaContable::where('codigo', 'like', '5101%')->first()->id,
                    // Cuenta de IVA por pagar
                    CuentaContable::where('codigo', 'like', '2408%')->first()->id,
                ]);
                break;
            case 'pago_venta':
                $cuentasRequeridas = collect([
                    // Cuenta de caja o banco
                    CuentaContable::where('codigo', 'like', '1101%')->first()->id,
                    // Cuenta de clientes
                    CuentaContable::where('codigo', 'like', '1301%')->first()->id,
                ]);
                break;
            case 'pago_compra':
                $cuentasRequeridas = collect([
                    // Cuenta de caja o banco
                    CuentaContable::where('codigo', 'like', '1101%')->first()->id,
                    // Cuenta de proveedores
                    CuentaContable::where('codigo', 'like', '2101%')->first()->id,
                ]);
                break;
        }

        return $cuentasRequeridas->filter();
    }

    /**
     * Valida que el asiento contenga todas las cuentas requeridas según el tipo de operación
     *
     * @return bool
     */
    public function validarCuentasRequeridas()
    {
        if (empty($this->tipo_operacion)) {
            return true; // Si no hay tipo de operación, no se valida
        }

        $cuentasRequeridas = $this->getCuentasRequeridasPorTipo();
        if ($cuentasRequeridas->isEmpty()) {
            return true; // Si no hay cuentas requeridas para este tipo, no se valida
        }

        $cuentasUsadas = $this->detalles->pluck('cuenta_id')->unique();
        return $cuentasRequeridas->diff($cuentasUsadas)->isEmpty();
    }

    public function aprobar()
    {
        if ($this->estaBalanceado() && $this->validarCuentasRequeridas()) {
            $this->estado = 'APROBADO';
            $this->save();
            return true;
        }
        return false;
    }

    public function anular()
    {
        $this->estado = 'ANULADO';
        $this->save();
    }

    // Nuevos métodos
    public function registrarPago($monto, $fecha)
    {
        if ($this->saldo_pendiente <= 0) {
            return false;
        }

        $montoPago = min($monto, $this->saldo_pendiente);
        
        // Crear asiento de pago
        $pago = new AsientoContable([
            'fecha' => $fecha,
            'tipo_operacion' => $this->tipo_operacion == 'venta_credito' ? 'pago_venta' : 'pago_compra',
            'documento_relacionado_id' => $this->id,
            'monto_total' => $montoPago,
            'saldo_pendiente' => 0,
            'estado' => 'APROBADO'
        ]);

        // Actualizar saldo pendiente del documento original
        $this->saldo_pendiente -= $montoPago;
        if ($this->saldo_pendiente <= 0) {
            $this->estado = 'PAGADO';
        }
        $this->save();

        return $pago;
    }
} 
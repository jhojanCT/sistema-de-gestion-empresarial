<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class CierreDiarioResumenSheet implements FromArray, WithTitle
{
    protected $cierre;
    public function __construct($cierre) { $this->cierre = $cierre; }
    public function array(): array
    {
        return [
            ['Fecha', $this->cierre->fecha->format('d/m/Y')],
            ['Usuario', optional($this->cierre->usuario)->name],
            ['--- Movimientos que afectan caja ---'],
            ['Saldo Inicial', $this->cierre->saldo_inicial],
            ['Ventas Contado', $this->cierre->ventas_contado],
            ['Cobros Crédito', $this->cierre->cobros_credito],
            ['Otros Ingresos', $this->cierre->otros_ingresos],
            ['Compras Contado', $this->cierre->compras_contado],
            ['Pagos Crédito', $this->cierre->pagos_credito],
            ['Gastos', $this->cierre->gastos],
            ['Saldo Final', $this->cierre->saldo_final],
            ['--- Movimientos informativos (no afectan caja) ---'],
            ['Ventas Crédito', $this->cierre->ventas_credito],
            ['Compras Crédito', $this->cierre->compras_credito],
            ['IVA Ventas Contado', $this->cierre->iva_ventas_contado],
            ['IVA Ventas Crédito', $this->cierre->iva_ventas_credito],
            ['IVA Compras Contado', $this->cierre->iva_compras_contado],
            ['IVA Compras Crédito', $this->cierre->iva_compras_credito],
            ['Diferencia', $this->cierre->diferencia],
            ['Observaciones', $this->cierre->observaciones],
        ];
    }
    public function title(): string { return 'Resumen'; }
} 
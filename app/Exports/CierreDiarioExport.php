<?php

namespace App\Exports;

use App\Models\CierreDiario;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CierreDiarioExport implements WithMultipleSheets
{
    protected $cierreId;

    public function __construct($cierreId)
    {
        $this->cierreId = $cierreId;
    }

    public function sheets(): array
    {
        $cierre = CierreDiario::with(['ventas.items.producto', 'ventas.items.materiaPrimaFiltrada', 'compras.items.producto', 'compras.items.materiaPrima', 'compras.proveedor', 'ventas.cliente'])->findOrFail($this->cierreId);
        return [
            new CierreDiarioResumenSheet($cierre),
            new CierreDiarioVentasSheet($cierre),
            new CierreDiarioComprasSheet($cierre),
        ];
    }
} 
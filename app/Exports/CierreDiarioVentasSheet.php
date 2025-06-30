<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class CierreDiarioVentasSheet implements FromArray, WithTitle
{
    protected $cierre;
    public function __construct($cierre) { $this->cierre = $cierre; }
    public function array(): array
    {
        $rows = [
            ['VENTAS AL CONTADO (Afectan caja)'],
            ['#', 'Cliente', 'Tipo', 'Subtotal', 'IVA', 'Total', 'Ítem Tipo', 'Ítem Nombre', 'Cantidad', 'Unidad', 'Precio Unitario', 'Subtotal Ítem']
        ];
        $i = 1;
        foreach ($this->cierre->ventas->where('tipo', 'contado') as $venta) {
            foreach ($venta->items as $item) {
                $rows[] = [
                    $i,
                    optional($venta->cliente)->nombre,
                    ucfirst($venta->tipo),
                    $venta->subtotal,
                    $venta->iva_amount,
                    $venta->total,
                    $item->producto ? 'Producto' : ($item->materiaPrimaFiltrada ? 'Materia Prima' : '-'),
                    $item->producto ? $item->producto->nombre : ($item->materiaPrimaFiltrada ? $item->materiaPrimaFiltrada->nombre : '-'),
                    $item->cantidad,
                    $item->producto ? $item->producto->unidad_medida : ($item->materiaPrimaFiltrada ? $item->materiaPrimaFiltrada->unidad_medida : '-'),
                    $item->precio_unitario,
                    $item->subtotal
                ];
            }
            $i++;
        }
        $rows[] = [''];
        $rows[] = ['VENTAS A CRÉDITO (Solo informativo, no afecta caja)'];
        $rows[] = ['#', 'Cliente', 'Tipo', 'Subtotal', 'IVA', 'Total', 'Ítem Tipo', 'Ítem Nombre', 'Cantidad', 'Unidad', 'Precio Unitario', 'Subtotal Ítem'];
        $i = 1;
        foreach ($this->cierre->ventas->where('tipo', 'credito') as $venta) {
            foreach ($venta->items as $item) {
                $rows[] = [
                    $i,
                    optional($venta->cliente)->nombre,
                    ucfirst($venta->tipo),
                    $venta->subtotal,
                    $venta->iva_amount,
                    $venta->total,
                    $item->producto ? 'Producto' : ($item->materiaPrimaFiltrada ? 'Materia Prima' : '-'),
                    $item->producto ? $item->producto->nombre : ($item->materiaPrimaFiltrada ? $item->materiaPrimaFiltrada->nombre : '-'),
                    $item->cantidad,
                    $item->producto ? $item->producto->unidad_medida : ($item->materiaPrimaFiltrada ? $item->materiaPrimaFiltrada->unidad_medida : '-'),
                    $item->precio_unitario,
                    $item->subtotal
                ];
            }
            $i++;
        }
        return $rows;
    }
    public function title(): string { return 'Ventas'; }
} 
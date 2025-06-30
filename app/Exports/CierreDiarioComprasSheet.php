<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class CierreDiarioComprasSheet implements FromArray, WithTitle
{
    protected $cierre;
    public function __construct($cierre) { $this->cierre = $cierre; }
    public function array(): array
    {
        $rows = [
            ['COMPRAS AL CONTADO (Afectan caja)'],
            ['#', 'Proveedor', 'Tipo', 'Subtotal', 'IVA', 'Total', 'Ítem Tipo', 'Ítem Nombre', 'Cantidad', 'Unidad', 'Precio Unitario', 'Subtotal Ítem']
        ];
        $i = 1;
        foreach ($this->cierre->compras->where('tipo', 'contado') as $compra) {
            foreach ($compra->items as $item) {
                $rows[] = [
                    $i,
                    optional($compra->proveedor)->nombre,
                    ucfirst($compra->tipo),
                    $compra->subtotal,
                    $compra->iva_amount,
                    $compra->total,
                    $item->producto ? 'Producto' : ($item->materiaPrima ? 'Materia Prima' : '-'),
                    $item->producto ? $item->producto->nombre : ($item->materiaPrima ? $item->materiaPrima->nombre : '-'),
                    $item->cantidad,
                    $item->producto ? $item->producto->unidad_medida : ($item->materiaPrima ? $item->materiaPrima->unidad_medida : '-'),
                    $item->precio_unitario,
                    $item->subtotal
                ];
            }
            $i++;
        }
        $rows[] = [''];
        $rows[] = ['COMPRAS A CRÉDITO (Solo informativo, no afecta caja)'];
        $rows[] = ['#', 'Proveedor', 'Tipo', 'Subtotal', 'IVA', 'Total', 'Ítem Tipo', 'Ítem Nombre', 'Cantidad', 'Unidad', 'Precio Unitario', 'Subtotal Ítem'];
        $i = 1;
        foreach ($this->cierre->compras->where('tipo', 'credito') as $compra) {
            foreach ($compra->items as $item) {
                $rows[] = [
                    $i,
                    optional($compra->proveedor)->nombre,
                    ucfirst($compra->tipo),
                    $compra->subtotal,
                    $compra->iva_amount,
                    $compra->total,
                    $item->producto ? 'Producto' : ($item->materiaPrima ? 'Materia Prima' : '-'),
                    $item->producto ? $item->producto->nombre : ($item->materiaPrima ? $item->materiaPrima->nombre : '-'),
                    $item->cantidad,
                    $item->producto ? $item->producto->unidad_medida : ($item->materiaPrima ? $item->materiaPrima->unidad_medida : '-'),
                    $item->precio_unitario,
                    $item->subtotal
                ];
            }
            $i++;
        }
        return $rows;
    }
    public function title(): string { return 'Compras'; }
} 
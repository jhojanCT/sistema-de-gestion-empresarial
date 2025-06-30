<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BalanceComprobacionExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $cuentas;
    protected $totales;
    protected $estadisticas;
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct($cuentas, $totales, $estadisticas, $fechaInicio, $fechaFin)
    {
        $this->cuentas = $cuentas;
        $this->totales = $totales;
        $this->estadisticas = $estadisticas;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function collection()
    {
        $data = collect();

        // Agregar encabezado del reporte
        $data->push([
            'BALANCE DE COMPROBACIÓN',
            '',
            '',
            '',
            '',
            '',
            '',
        ]);

        $data->push([
            'Período: ' . $this->fechaInicio . ' al ' . $this->fechaFin,
            '',
            '',
            '',
            '',
            '',
            '',
        ]);

        $data->push([]); // Línea en blanco

        // Agregar datos de las cuentas
        foreach ($this->cuentas as $cuenta) {
            $saldoActual = $cuenta->saldo_anterior + $cuenta->total_debe - $cuenta->total_haber;
            
            $data->push([
                $cuenta->codigo,
                $cuenta->nombre,
                $cuenta->saldo_anterior,
                $cuenta->total_debe,
                $cuenta->total_haber,
                $saldoActual,
                $cuenta->tipo
            ]);
        }

        // Agregar línea en blanco
        $data->push([]);

        // Agregar totales
        $data->push([
            'TOTALES',
            '',
            $this->totales['saldo_anterior_deudor'] - $this->totales['saldo_anterior_acreedor'],
            $this->totales['debe'],
            $this->totales['haber'],
            $this->totales['saldo_actual_deudor'] - $this->totales['saldo_actual_acreedor'],
            ''
        ]);

        // Agregar estadísticas
        $data->push([]);
        $data->push(['ESTADÍSTICAS DEL PERÍODO']);
        $data->push(['Total de Cuentas', $this->estadisticas['total_cuentas']]);
        $data->push(['Cuentas con Movimiento', $this->estadisticas['cuentas_con_movimiento']]);
        
        if (isset($this->estadisticas['ratios'])) {
            if (isset($this->estadisticas['ratios']['rotacion_cuentas'])) {
                $data->push(['Rotación de Cuentas', number_format($this->estadisticas['ratios']['rotacion_cuentas'] * 100, 2) . '%']);
            }
            if (isset($this->estadisticas['ratios']['indice_endeudamiento'])) {
                $data->push(['Índice de Endeudamiento', number_format($this->estadisticas['ratios']['indice_endeudamiento'] * 100, 2) . '%']);
            }
        }

        // Agregar distribución por tipo de cuenta
        $data->push([]);
        $data->push(['DISTRIBUCIÓN POR TIPO DE CUENTA']);
        foreach ($this->estadisticas['tipos_cuenta'] as $tipo => $cantidad) {
            $data->push([$tipo, $cantidad]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Código',
            'Cuenta',
            'Saldo Anterior',
            'Debe',
            'Haber',
            'Saldo Actual',
            'Tipo'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        
        // Estilo para el título
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        // Estilo para los encabezados
        $sheet->getStyle('A4:G4')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E9ECEF']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);
        
        // Estilo para los datos
        $sheet->getStyle('A5:G' . ($lastRow - 8))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);
        
        // Alineación de números
        $sheet->getStyle('C5:F' . ($lastRow - 8))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // Código
            'B' => 40,  // Cuenta
            'C' => 20,  // Saldo Anterior
            'D' => 20,  // Debe
            'E' => 20,  // Haber
            'F' => 20,  // Saldo Actual
            'G' => 15,  // Tipo
        ];
    }

    public function title(): string
    {
        return 'Balance de Comprobación';
    }
} 
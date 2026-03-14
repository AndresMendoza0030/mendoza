<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\FromCollection;    // colección de exportación
use Maatwebsite\Excel\Concerns\WithEvents;     // Registra automáticamente oyentes de eventos
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;    // exportar 0 se muestra como es, no es nulo
use Maatwebsite\Excel\Concerns\WithTitle;    // establecer nombre de trabajo
use Maatwebsite\Excel\Events\AfterSheet;    // el evento se genera al final del proceso de la hoja de trabajo

class ExportCompraSum implements
    FromCollection,
    WithMapping,
    WithHeadings,
    WithTitle,
    WithStrictNullComparison,
    WithColumnFormatting,
    WithColumnWidths,
    WithStyles,
    WithEvents

{
    public $data, $bodega, $nombrebodega, $desde, $hasta, $tt1, $tt0;


    public function __construct($data, $bodega, $nombrebodega, $desde, $hasta)
    {
        $this->data     = $data;
        $this->desde    = $desde;
        $this->hasta    = $hasta;
        $this->bodega = $bodega;
        $this->nombrebodega = $nombrebodega;
        $this->tt0 = 0;
        $this->tt1 = 0;

        $this->ftitulo = "Compra-Sumarizada";
        //definiendo parametros
    }

    /**
     * registerEvents    freeze the first row with headings
     * @return array
     * @author   liuml  <liumenglei0211@163.com>
     * @DateTime 2018/11/1  11:19
     */

    /**
     * Los datos que deben exportarse se procesan en este método de manera uniforme. En este método, también puede usar
     * el Modelo para obtener los datos directamente.
     * Los datos aquí se pasan del Controlador. En cuanto a cómo pasar, consulte el código en el Controlador que figura a continuación.
     * El procesamiento de datos en el interior es demasiado largo, uso "..." para superfluo.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return
            [
                ["MENDOZA ALVARADO, SA DE CV"],
                ["RESUMEN DE COMPRA X BODEGA X PERIODO"],
                ["[" . $this->bodega . "] - " . $this->nombrebodega . " DESDE " . date('d-m-Y', strtotime($this->desde)) . " HASTA " . date('d-m-Y', strtotime($this->hasta))],
                [],
                [
                    'FECHA',
                    'NOMBRE DE PROVEEDOR',
                    'REFERENCIA',
                    'V.GRAVADO',
                    'V.IVA',
                    'T O T A L',
                ]
            ];
    }



    public function registerEvents(): array
    {
        // Fuente Negrita Tamaño 12 (titulos)
        $fontNegrita =
            [
                'font' => ['bold' => true, 'size' => 11]
            ];
        // Alinear al centro
        $colCenter = ['alignment' => ['horizontal' => 'center']];
        $rowCenter = ['alignment' => ['vertical' => 'center']];
        return
            [
                AfterSheet::class => function (AfterSheet $event) use ($fontNegrita, $colCenter, $rowCenter) {
                    $event->sheet->getDelegate()->setMergeCells(['A1:F1', 'A2:F2', 'A3:F3']);
                    $event->sheet->getStyle('A1:F1')->ApplyFromArray($colCenter);
                    $event->sheet->getStyle('A2:F2')->ApplyFromArray($colCenter);
                    $event->sheet->getStyle('A3:F3')->ApplyFromArray($colCenter);
                    $event->sheet->getStyle('A5:F5')->ApplyFromArray($colCenter);
                    $event->sheet->getStyle('A1:F3')->ApplyFromArray($fontNegrita);
                    $event->sheet->getStyle('A5:F5')->ApplyFromArray($fontNegrita);

                    $event->sheet->getStyle('D1:E5000')->getNumberFormat()->setFormatCode('#,##0.0000');
                    $event->sheet->getStyle('F1:F5000')->getNumberFormat()->setFormatCode('#,##0.00');

                    $i = 5;
                    $lastCellValue = null;
                    foreach ($this->data as $dt) {
                        $i++;
                    }
                    $i += 1;

                    $event->sheet->setCellValue('C' . $i, "TOTAL:");
                    $event->sheet->setCellValue('F' . $i, $this->tt1);
                    $event->sheet->getStyle('C' . $i)->ApplyFromArray($fontNegrita);
                    $event->sheet->getStyle('F' . $i)->ApplyFromArray($fontNegrita);
                }
            ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 60,
            'C' => 40,
            'D' => 15,
            'E' => 15,
            'F' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            5    => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'E0E0E0',]
                ],


            ],

        ];
    }

    public function map($data): array
    {
        $this->tt1 += $data->total;

        //

        return
            [
                date('d-m-Y', strtotime($data->fecha)),
                $data->razon,
                utf8_decode($data->documento . " " . $data->numdocufiscal),
                number_format($data->gravado, 4),
                number_format($data->iva, 4),
                number_format($data->total, 4)
            ];
        //

    }

    public function title(): string
    {
        // Establecer el nombre de la tarjeta de trabajo
        return $this->ftitulo;
    }

    public function columnFormats(): array
    {
        return [];
    }
}

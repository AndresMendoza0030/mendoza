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
 
   class ExportCatalogoProd implements FromCollection,WithMapping, WithHeadings, WithTitle, WithStrictNullComparison,
   WithColumnFormatting, WithColumnWidths,WithStyles,WithEvents

   {
       public $data,$titulo;


       public function __construct($data)
       {
           $this->data     = $data;

           $this->ftitulo = "Catalogo-Productos";
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
            [ "MENDOZA ALVARADO, SA DE CV"],
            [ "CATALOGO GENERAL DE PRODUCTOS"],
            [],
            [ ],
            [  'PRODUCTO ID',
               'CLASE INVENTARIO',
               'LINEA INVENTARIO.',
               'NOMBRE DEL PRODUCTO',
               'PRESENTACION',
               'MANEJO POR',
               'COSTO UNIT',
            ]
           ];
       }



       public function registerEvents(): array
       {
            // Fuente Negrita Tamaño 12 (titulos)
            $fontNegrita =
            [
                'font' => ['bold' => true,'size' => 11]
            ];
            // Alinear al centro
            $colCenter = ['alignment' => ['horizontal' => 'center']];
            $rowCenter = ['alignment' => ['vertical' => 'center']];
        return
        [
            AfterSheet::class => function(AfterSheet $event) use($fontNegrita, $colCenter, $rowCenter)
            {
                $event->sheet->getDelegate()->setMergeCells( ['A1:G1','A2:G2','A3:G3']);
                $event->sheet->getStyle('A1:G1')->ApplyFromArray($colCenter);
                $event->sheet->getStyle('A2:G2')->ApplyFromArray($colCenter);
                $event->sheet->getStyle('A3:G3')->ApplyFromArray($colCenter);
                $event->sheet->getStyle('A5:G5')->ApplyFromArray($colCenter);                
                $event->sheet->getStyle('A1:G3')->ApplyFromArray($fontNegrita);
                $event->sheet->getStyle('A5:G5')->ApplyFromArray($fontNegrita);

                $event->sheet->getStyle('E1:G5000')->getNumberFormat()->setFormatCode('#,##0.0000');

                $i = 5;
                $lastCellValue = null;
/*                 foreach ($this->data as $dt)
                {
                    $i++;
                }
                $i+=1;

                $event->sheet->setCellValue('F' . $i, "TOTAL:");
                $event->sheet->setCellValue('G' . $i, $this->tt1);
                $event->sheet->getStyle('J' . $i)->ApplyFromArray($fontNegrita);
                $event->sheet->getStyle('L' . $i)->ApplyFromArray($fontNegrita);
 */            
            }
        ];
       }

       public function columnWidths(): array
       {
           return [
               'A' => 15,
               'B' => 50,            
               'C' => 50,
               'D' => 70,
               'E' => 50,               
               'F' => 50,
               'G' => 15,               
           ];
       }

       public function styles(Worksheet $sheet)
       {
           return [
               // Style the first row as bold text.
               5    => ['font' => ['bold' => true, 'size' => 11],
                        'fill' => [ 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'E0E0E0',]],


                       ],
   
           ];
       }       

       public function map($data): array
       {
            //
                return 
                [
                    $data->id, $data->cclase, $data->llinea, $data->nombre, $data->presentacion, $data->manejopor, number_format($data->costo,4)
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

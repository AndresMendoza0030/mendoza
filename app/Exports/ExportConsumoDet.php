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
 
   class ExportConsumoDet implements FromCollection,WithMapping, WithHeadings, WithTitle, WithStrictNullComparison,
   WithColumnFormatting, WithColumnWidths,WithStyles,WithEvents

   {
       public $data, $bodega, $nombrebodega, $desde, $hasta, $tt0,$tt1,$tt2,$tt3,$tt4,$tt5,$tt6,$tt7;


       public function __construct($data, $bodega,$nombrebodega,$desde,$hasta)
       {
            $this->data     = $data;
            $this->hasta    = $hasta;
            $this->bodega = $bodega;
            $this->nombrebodega= $nombrebodega;
            $this->tt0=0;
            $this->tt1=0;
            $this->tt2=0;
            $this->tt3=0;
            $this->tt4=0;
            $this->tt5=0;
            $this->tt6=0;
            $this->tt7=0;
            //
            $this->ftitulo = "Consumo-Detallado";
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
            [ "DETALLE DE CONSUMO X BODEGA X PERIODO"],
            [ "[".$this->bodega."] - ".$this->nombrebodega." DESDE ".date('d-m-Y', strtotime($this->desde))." HASTA ".date('d-m-Y', strtotime($this->hasta))],
            [ ],
            [  'NOMBRE DEL DESTINO',
               'CLASE DE INVENTARIO',
               'NOMBRE DEL PRODUCTO',
               'CANTIDAD',
               'MONTO S/IVA',
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
                $event->sheet->getDelegate()->setMergeCells( ['A1:E1','A2:E2','A3:E3']);
                $event->sheet->getStyle('A1:E1')->ApplyFromArray($colCenter);
                $event->sheet->getStyle('A2:E2')->ApplyFromArray($colCenter);
                $event->sheet->getStyle('A3:E3')->ApplyFromArray($colCenter);
                $event->sheet->getStyle('A5:E5')->ApplyFromArray($colCenter);                
                $event->sheet->getStyle('A1:E3')->ApplyFromArray($fontNegrita);
                $event->sheet->getStyle('A5:E5')->ApplyFromArray($fontNegrita);

                $event->sheet->getStyle('D1:E5000')->getNumberFormat()->setFormatCode('#,##0.00');

                $i = 5;
                $lastCellValue = null;
                foreach ($this->data as $dt)
                {
                    $i++;
                }
                $i+=1;
                //
                $event->sheet->setCellValue('C' . $i, "TOTALES:");
                $event->sheet->setCellValue('D' . $i, $this->tt1);

                $event->sheet->getStyle('C'.$i.':D'.$i)->ApplyFromArray($fontNegrita);
                //
            }
        ];
       }

       public function columnWidths(): array
       {
           return [
                'A' => 45,
                'B' => 45,
                'C' => 50,
                'D' => 15,
                'E' => 15,
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
            $this->tt1+=round($data->tgravado,2);
    
                return 
                [
                    utf8_decode($data->destino), utf8_decode($data->cclase), utf8_decode($data->producto), 
                    number_format($data->cantidad,2), number_format($data->tgravado,2)
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
           return [
           ];
       }
   }

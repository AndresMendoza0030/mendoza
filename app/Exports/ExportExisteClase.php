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
 
   class ExportExisteClase implements FromCollection,WithMapping, WithHeadings, WithTitle, WithStrictNullComparison,
   WithColumnFormatting, WithColumnWidths,WithStyles,WithEvents

   {
       public $data, $clase, $nombreclase, $linea, $tt0, $tt1,$encabe;


       public function __construct($data, $clase,$cclase)
       {
           $this->data     = $data;
           $this->clase = $clase;
           $this->nombreclase= $cclase;
           $this->tt0=0;
           $this->tt1=0;
           $this->encabe=0;

           $this->ftitulo = "Existencia-Clase";
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
            [ "EXISTENCIA GENERAL X CLASE"],
            [ $this->clase."-".$this->nombreclase],
            [ ],
            [  'PRODUCTO ID',
               'LINEA INVENTARIO',
               'REG.SANITARIO',
               'ID.BARRA',
               'MANEJO POR',
               'NOMBRE DEL PRODUCTO',
               'COSTO UNIT',
               'EXISTENCIA',
               'MONTO EXISTENCIA',
               'VTA.SUGERIDO',
               'PRECIO VENTA'
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
                $event->sheet->getDelegate()->setMergeCells( ['A1:K1','A2:K2','A3:K3']);
                $event->sheet->getStyle('A1:K1')->ApplyFromArray($colCenter);
                $event->sheet->getStyle('A2:K2')->ApplyFromArray($colCenter);
                $event->sheet->getStyle('A3:K3')->ApplyFromArray($colCenter);
                $event->sheet->getStyle('A5:K5')->ApplyFromArray($colCenter);                
                $event->sheet->getStyle('A1:K3')->ApplyFromArray($fontNegrita);
                $event->sheet->getStyle('A5:K5')->ApplyFromArray($fontNegrita);

                $event->sheet->getStyle('G1:G5000')->getNumberFormat()->setFormatCode('#,##0.0000');
                $event->sheet->getStyle('H1:H5000')->getNumberFormat()->setFormatCode('###0');                
                $event->sheet->getStyle('D1:D5000')->getNumberFormat()->setFormatCode('###0');                
                $event->sheet->getStyle('I1:K5000')->getNumberFormat()->setFormatCode('#,##0.0000');

                $i = 5;
                $lastCellValue = null;
                foreach ($this->data as $dt)
                {
                    $i++;
                }
                $i+=1;

                $event->sheet->setCellValue('H' . $i, "TOTAL:");
                $event->sheet->setCellValue('I' . $i, $this->tt1);
                $event->sheet->getStyle('J' . $i)->ApplyFromArray($fontNegrita);
                $event->sheet->getStyle('L' . $i)->ApplyFromArray($fontNegrita);
            }
        ];
       }

       public function columnWidths(): array
       {
           return [
                'A' => 15,
                'B' => 40,
                'C' => 30,
                'D' => 50,
                'E' => 30,
               'F' => 70,
               'G' => 15,            
               'H' => 15,
               'I' => 15,
               'J' => 15,
               'K' => 15
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
            $this->tt1+=(round($data['existenciat']*$data['costo'],4));

            //

                return 
                [
                    $data['producto_id'], $data['clinea'],$data['registro'],$data['barra'],$data['manejopor'],
                    $data['producto'], $data['costo'], $data['existenciat'],
                    round($data['existenciat']*$data['costo'],4),$data['sugerido'],$data['precioventa']
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
            'B' => '0',
            'C' => '0.00',
            'D' => '0.00',
            'E' => '0.00',
            'F' => '0.00',
           ];
       }
   }

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Fpdf;
use Storage;

use App\Inventoryhead;
use App\Product;
use App\ClienteProveedor;
use App\Movdetail;
use App\Documento;
use App\Warehouse;

class PdfRpComprasPeriodoDet extends BaseController
{
    public $registros, $desde, $hasta, $bodega;
    protected $pdf;

    public function __construct(\App\RpCompraDet $pdf)
    {
        $this->pdf = $pdf;
    }

    /**
     * Genera el reporte detallado de Compras en un periodo para una bodega dada.
     *
     * @param  string  $desde  Fecha inicial (YYYY-MM-DD)
     * @param  string  $hasta  Fecha final (YYYY-MM-DD)
     * @param  int     $bod    ID de la bodega
     * @return void
     */
    public function PDFCompraPeriodoX($desde, $hasta, $bod)
    {
        // Asignamos las fechas para usarlas en el PDF
        $this->pdf->desde = $desde;
        $this->desde      = $desde;

        $this->pdf->hasta = $hasta;
        $this->hasta      = $hasta;

        $this->bodega = $bod;

        // Obtenemos la bodega de forma segura:
        $warehouse = Warehouse::find($bod);
        // Si existe, tomamos su nombre; si no, ponemos un texto por defecto
        $this->pdf->labodega = $warehouse ? $warehouse->nombre : 'BODEGA DESCONOCIDA';

        // Configuración inicial de PDF
        $this->pdf->AliasNbPages();
        // Página formato Horizontal (L)
        $this->pdf->AddPage("L");
        $this->pdf->SetTitle("DetalladoCompra");

        // Contenido principal
        $this->contenido();

        // Emitir el PDF
        $this->pdf->Output();
        exit;
    }

    /**
     * Genera el contenido (detalle) del PDF.
     */
    public function contenido()
    {
        // Consulta de detalles
        $detalle = Movdetail::leftJoin('inventoryhead as ih', 'ih.id', '=', 'movdetails.numerodoc')
            ->leftJoin('documentos as dc', 'dc.id', 'ih.tpdocufiscal')
            ->select(
                'movdetails.*',
                'ih.sujeto as razon',
                'dc.documento as documento',
                'ih.gravado as hgravado',
                'ih.iva as hiva',
                'ih.total as htotal',
                'ih.numdocufiscal as numdocufiscal'
            )
            ->where('movdetails.tipodoc', 'CPRA')
            ->where('movdetails.bodega', $this->bodega)
            ->whereDate('movdetails.fecha', '>=', $this->desde)
            ->whereDate('movdetails.fecha', '<=', $this->hasta)
            ->orderBy('fecha')
            ->orderBy('razon')
            ->get();

        // Si la colección está vacía, evitamos errores y mostramos un mensaje o salimos.
        if ($detalle->isEmpty()) {
            $this->pdf->SetFont('Arial', 'B', 10);
            $this->pdf->Cell(0, 10, 'NO EXISTEN REGISTROS PARA EL PERIODO SELECCIONADO.', 0, 1, 'C');
            return;
        }

        // Variables de totales
        $tt  = 0;  // No se usa directamente, pero se deja para consistencia
        $r   = 1;  // Contador de línea
        $t1  = 0;  // Subtotal gravado (por fecha) en cabecera
        $t2  = 0;  // Subtotal iva (por fecha) en cabecera
        $t3  = 0;  // Subtotal total (por fecha) en cabecera
        $tt1 = 0;  // Subtotal gravado (por fila) en detalle
        $tt2 = 0;  // Subtotal iva (por fila) en detalle
        $tt3 = 0;  // Subtotal total (por fila) en detalle
        $ttf1 = 0; // Gravado acumulado final
        $ttf2 = 0; // IVA acumulado final
        $ttf3 = 0; // Total acumulado final
        $tf1 = 0;  // Gravado acumulado final (header)
        $tf2 = 0;  // IVA acumulado final (header)
        $tf3 = 0;  // Total acumulado final (header)

        // Tomamos la fecha del PRIMER registro
        // Nota: Usamos notación de objeto ->fecha
        // porque Eloquent retorna objetos, no arrays
        $fe = $detalle[0]->fecha;

        $primero = 1;
        $this->pdf->SetFont('Arial', '', 8);

        // Recorremos los registros
        foreach ($detalle as $det) {
            // Si la fecha cambió, imprimimos subtotales de la fecha anterior
            if ($fe != $det->fecha) {
                $this->pdf->SetFont('Arial', 'B', 8);
                $this->pdf->setX(10);
                $this->pdf->cell(
                    85,
                    6,
                    utf8_decode("TOTAL OPERACION FECHA:") . date('d-m-Y', strtotime($fe)),
                    1,
                    "",
                    "L"
                );
                $this->pdf->cell(15, 6, number_format($t1, 4), 1, "", "R");
                $this->pdf->cell(15, 6, number_format($t2, 4), 1, "", "R");
                $this->pdf->cell(15, 6, number_format($t3, 2), 1, "", "R");
                $this->pdf->cell(94, 6, "", 1, "", "R");
                $this->pdf->cell(17, 6, number_format($tt1, 4), 1, "", "R");
                $this->pdf->cell(17, 6, number_format($tt2, 4), 1, "", "R");
                $this->pdf->cell(17, 6, number_format($tt3, 2), 1, "", "R");
                $this->pdf->Ln();
                $this->pdf->SetFont('Arial', '', 8);

                // Reiniciamos los subtotales
                $t1  = 0;
                $t2  = 0;
                $t3  = 0;
                $tt1 = 0;
                $tt2 = 0;
                $tt3 = 0;

                // Actualizamos la fecha para el siguiente bloque
                $fe = $det->fecha;
                $primero++;
            }

            // Imprimimos el detalle de la fila actual
            $this->pdf->cell(15, 5, date('d-m-Y', strtotime($det->fecha)), 0, "", "C");
            $this->pdf->cell(50, 5, substr(utf8_decode($det->razon), 0, 48), 0, "", "L");

            // Documento (reducimos fuente temporalmente)
            $this->pdf->SetFont('Arial', '', 7);
            $this->pdf->cell(20, 5, utf8_decode($det->documento . " " . $det->numdocufiscal), 0, "", "C");
            $this->pdf->SetFont('Arial', '', 8);

            // Montos en cabecera
            $this->pdf->cell(15, 5, number_format($det->hgravado, 4), 0, "", "R");
            $this->pdf->cell(15, 5, number_format($det->hiva, 4), 0, "", "R");
            $this->pdf->cell(15, 5, number_format($det->htotal, 2), 0, "", "R");

            // Producto (nuevamente fuente reducida para mostrar ID y nombre)
            $this->pdf->SetFont('Arial', '', 7);
            $this->pdf->cell(
                60,
                5,
                "[" . $det->producto_id . "] " . substr(utf8_decode($det->producto), 0, 35),
                0,
                "",
                "L"
            );
            $this->pdf->SetFont('Arial', '', 8);

            // Cantidad, Costo y Totales en detalle
            $this->pdf->cell(17, 5, number_format($det->cantidad, 0), 0, "", "C");
            $this->pdf->cell(17, 5, number_format($det->precosto, 4), 0, "", "R");
            $this->pdf->cell(17, 5, number_format($det->gravado, 4), 0, "", "R");
            $this->pdf->cell(17, 5, number_format($det->iva, 4), 0, "", "R");
            $this->pdf->cell(17, 5, number_format($det->total, 2), 0, "", "R");
            $this->pdf->Ln();

            // Acumulamos subtotales de cabecera
            $t1  += $det->hgravado;
            $t2  += $det->hiva;
            $t3  += $det->htotal;

            // Acumulamos subtotales de detalle
            $tt1 += $det->gravado;
            $tt2 += $det->iva;
            $tt3 += $det->total;

            // Acumulados finales
            $ttf1 += $det->gravado;
            $ttf2 += $det->iva;
            $ttf3 += $det->total;

            $tf1 += $det->hgravado;
            $tf2 += $det->hiva;
            $tf3 += $det->htotal;

            $r++;
        }

        // Al finalizar el bucle, imprimimos el subtotal de la última fecha
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->setX(10);
        $this->pdf->cell(
            85,
            6,
            utf8_decode("TOTAL OPERACION FECHA:") . date('d-m-Y', strtotime($fe)),
            1,
            "",
            "L"
        );
        $this->pdf->cell(15, 6, number_format($t1, 4), 1, "", "R");
        $this->pdf->cell(15, 6, number_format($t2, 4), 1, "", "R");
        $this->pdf->cell(15, 6, number_format($t3, 2), 1, "", "R");
        $this->pdf->cell(94, 6, "", 1, "", "R");
        $this->pdf->cell(17, 6, number_format($tt1, 4), 1, "", "R");
        $this->pdf->cell(17, 6, number_format($tt2, 4), 1, "", "R");
        $this->pdf->cell(17, 6, number_format($tt3, 2), 1, "", "R");
        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->Ln();

        // Finalmente, imprimimos el TOTAL DEL PERIODO
        $this->pdf->setX(10);
        $this->pdf->cell(85, 6, utf8_decode("TOTAL DEL PERIODO"), 1, "", "L");
        $this->pdf->cell(15, 6, number_format($tf1, 4), 1, "", "R");
        $this->pdf->cell(15, 6, number_format($tf2, 4), 1, "", "R");
        $this->pdf->cell(15, 6, number_format($tf3, 2), 1, "", "R");
        $this->pdf->cell(94, 6, "", 1, "", "R");
        $this->pdf->cell(17, 6, number_format($ttf1, 4), 1, "", "R");
        $this->pdf->cell(17, 6, number_format($ttf2, 4), 1, "", "R");
        $this->pdf->cell(17, 6, number_format($ttf3, 2), 1, "", "R");
        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->Ln();

        $this->pdf->SetFont('Arial', '', 8);
    }
}

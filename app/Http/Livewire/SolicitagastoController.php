<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Livewire\WithPagination;
use Livewire\Component;

use App\Warehouse;
use App\Productclass;
use App\Productline;
use App\Product;
use App\Destino;
use App\Productstock;
use App\Reqhead;
use App\Detareq;
use App\Generalsetup;
use App\User;
use Crabbly\Fpdf\Fpdf;

class SolicitagastoController extends Component
{
    use WithPagination;

    // Properties
    public $selected_id, $search;  // para búsquedas y fila seleccionada
    private $pagination = 4;       // paginación de tabla
    public $lpresentacion, $lmanejado;
    public $lafecha, $bodega, $destino, $bodegas, $destinos, $detalle, $modous = 0;
    public $tregistros, $numeroreg, $tasaiva, $concepto;
    public $elstock, $lacantidad, $elprecio, $lproducto;
    public $productoid; // Importante para guardar ID del producto seleccionado

    public function mount()
    {
        $lafecha = date('Y-m-d');
        $this->lafecha = $lafecha;

        $this->detalle     = collect([]);
        $this->tregistros  = 0;
        $this->numeroreg   = 0;
        $this->bodega      = '';
        $this->lproducto   = '';
        $this->concepto    = '';

        // bodega_pred y destino_pred sacados del user logueado
        $this->destino = auth()->user()->destino_pred;
        $this->bodega  = auth()->user()->bodega_pred;

        // Si no hay bodega_pred, permitimos seleccionar manualmente
        if (trim(auth()->user()->bodega_pred) == '') {
            $this->modous = 1;
        }

        // Config para IVA
        $empresa       = Generalsetup::first();
        $this->tasaiva = round($empresa->piva / 100, 2);
    }

    // Método Livewire principal
    public function render()
    {
        // Buscador de productos
        if (strlen($this->lproducto) > 0) {
            $this->productos = Product::leftJoin('productstocks as stk', function ($join) {
                $join->on('stk.producto_id', '=', 'products.id')
                    ->on('stk.bodega', '=', DB::raw("'" . $this->bodega . "'"));
            })
                ->select(
                    "products.*",
                    'stk.existencia as stock',
                    DB::raw("CONCAT(TRIM(nombre)) as producto")
                )
                ->where('tipo', '=', 'PRODUCTO')
                ->where('estatus', '=', '0')
                ->where(function ($query) {
                    $query->where('nombre', 'like', '%' . $this->lproducto . '%')
                        ->orWhere('presentacion', 'like', '%' . $this->lproducto . '%')
                        ->orWhere('products.id', 'like', '%' . $this->lproducto . '%');
                })
                ->get();
        } else {
            $this->productos = [];
        }

        // Si hay registros en detalle, deshabilitamos combo bodega/destino
        if ($this->tregistros > 0) {
            $this->emit('enable-bodega', 2);
        }

        // Listas de bodega y destino
        $this->bodegas  = Warehouse::where('estatus', '=', 0)->get();
        $this->destinos = Destino::where('estatus', '=', 0)->get();

        return view('livewire.solicitagasto.component');
    }

    // Asignar bodega/destino/concepto
    public function asgBodega($valor)
    {
        $this->bodega = $valor;
    }
    public function asgDestino($valor)
    {
        $this->destino = $valor;
    }
    public function asgConcepto($valor)
    {
        $this->concepto = $valor;
    }

    // Seleccionar producto
    public function seleProducto($reg)
    {
        $this->lacantidad = '';
        $prodJson         = json_decode($reg);

        $this->lproducto  = trim($prodJson->producto);
        $this->productoid = $prodJson->id;
        $this->elprecio   = round($prodJson->costo, 4);
        $this->lpresentacion = $prodJson->presentacion ?? '';
        $this->lmanejado     = $prodJson->manejopor ?? '';

        $this->elstock = '';
        $stock = Productstock::where('producto_id', $this->productoid)
            ->where('bodega', $this->bodega)
            ->first();
        if ($stock) {
            $this->elstock = $stock->existencia;
        }
        // Habilitar la caja de cantidad
        $this->emit('enable-cajas', 1);

        if ($this->tregistros > 0) {
            $this->emit('enable-bodega', 2);
        }
    }

    // Validar cantidad (opcional)
    public function validaCantidad($valor)
    {
        /*
        if ($this->elstock < $valor)
        {
            $this->emit('msg-error', "Cantidad mayor a la existencia actual.");
            $this->lacantidad='';
            $this->emit('enable-cajas',1);
        }
        else
        {
        */
        $this->emit('enable-cajas', 0);
        $this->emit('enable-grabalinea');
        //}
    }

    // Guardar una línea en el array $detalle
    public function saveLine()
    {
        $preciov = round($this->elprecio * $this->lacantidad, 4);
        $iv      = round($preciov * $this->tasaiva, 4);
        $monto   = round($preciov + $iv, 2);

        $this->detalle->push([
            "reg"        => $this->numeroreg,
            "productoid" => $this->productoid,
            "producto"   => $this->lproducto,
            "presentacion"  => $this->lpresentacion,
            "manejopor"     => $this->lmanejado,
            "cantidad"   => $this->lacantidad,
            "unitario"   => round($monto / $this->lacantidad, 4),
            "monto"      => $monto
        ]);

        $this->numeroreg  += 1;
        $this->tregistros += 1;

        // Limpiar
        $this->lproducto   = '';
        $this->lpresentacion = '';
        $this->lmanejado = '';
        $this->lacantidad  = '';
        $this->elprecio    = 0;

        $this->emit('enable-bodega', 2);
    }

    // Descartar todo
    public function descartarOperacion()
    {
        $this->resetInput();
    }
    public function resetInput()
    {
        $this->detalle     = collect([]);
        $this->tregistros  = 0;
        $this->numeroreg   = 0;
        $this->bodega      = '';
        $this->destino     = '';
        $this->concepto    = '';
        $this->emit('enable-bodega', 1);
    }

    // Escuchar eventos
    protected $listeners = [
        'deleteRow' => 'destroy',
    ];

    // Eliminar una línea del detalle
    public function destroy($id)
    {
        $nuevodet = collect([]);
        foreach ($this->detalle as $rs) {
            if ($rs['reg'] != $id) {
                $nuevodet->push([
                    "reg"       => $rs['reg'],
                    "productoid" => $rs['productoid'],
                    "producto"  => $rs['producto'],
                    "producto"  => $rs['producto'],
                    "presentacion"  => $rs['presentacion'],
                    "manejopor"  => $rs['manejopor'],
                    "unitario"  => $rs['unitario'],
                    "monto"     => $rs['monto']
                ]);
            }
        }
        $this->detalle    = $nuevodet;
        $this->tregistros = $this->detalle->count();
    }

    // Grabar la solicitud en DB
    public function grabarBd()
    {
        if ($this->bodega < 1 || $this->destino < 1) {
            return;
        }

        DB::beginTransaction();
        try {
            // Creamos el registro en Reqhead
            $head = Reqhead::create([
                'bodega'     => $this->bodega,
                'destino_id' => $this->destino,
                'fecha'      => Carbon::parse($this->lafecha),
                'tipodoc'    => 'PGAS',
                'estatus'    => 0,
                'user_id'    => auth()->user()->id,  // clave p/relacionar con user
                'concepto'   => trim($this->concepto),
            ]);

            // Grabamos el detalle
            foreach ($this->detalle as $det) {
                $produx = Product::find($det['productoid']);
                Detareq::create([
                    'bodega'      => $head->bodega,
                    'tipodoc'     => $head->tipodoc,
                    'numerodoc'   => $head->id,
                    'fecha'       => $head->fecha,
                    'producto_id' => $det['productoid'],
                    'producto'    => $det['producto'],
                    'cantidad'    => $det['cantidad'],
                    'user_id'     => $head->user_id,
                    'precosto'    => $produx->costo,
                    'preventa'    => $produx->precioventa,
                    'subtotal'    => round($det['cantidad'] * $produx->costo, 4),
                    'estatus'     => 0
                ]);
            }

            DB::commit();
            $this->resetInput();
            $this->emit('msgok', 'Solicitud ha sido guardada...');
            $this->emit('confirmaOrden', $head->id);
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
        }
    }

    // =========================================================
    // NUEVOS MÉTODOS PARA VER MIS SOLICITUDES Y MOSTRAR DETALLE
    // =========================================================

    // 1) Método para ver todas MIS solicitudes
    public function verMisSolicitudes()
    {
        // Filtramos por user actual
        $usuarioId = auth()->user()->id;
        $solicitudes = Reqhead::where('user_id', $usuarioId)
            ->orderBy('id', 'desc')
            ->get();

        // Retornamos la vista "livewire.solicitagasto.mis-solicitudes"
        return view('livewire.solicitagasto.mis-solicitudes', compact('solicitudes'));
    }

    // 2) Método para mostrar el DETALLE de una solicitud
    public function showSolicitud($id)
    {
        // Obtener la solicitud con eager loading de detalles
        $solicitud = Reqhead::with('detalles')->findOrFail($id);

        // Verificar si el usuario es administrador o gerente
        if (auth()->user()->hasRole('gerente') || auth()->user()->hasRole('admin')) {
            // Los administradores y gerentes pueden ver todas las solicitudes
            return view('livewire.solicitagasto.show-solicitud', compact('solicitud'));
        }

        // Verificar si la solicitud pertenece al usuario actual
        if ($solicitud->user_id != auth()->id()) {
            abort(403, 'No puedes ver solicitudes de otros usuarios');
        }

        // Si es el propietario de la solicitud, mostrar el detalle
        return view('livewire.solicitagasto.show-solicitud', compact('solicitud'));
    }


    public function verSolicitudesAdmin(Request $request)
    {
        // Verificar si el usuario está autenticado
        if (!auth()->check()) {
            abort(403, 'Usuario no autenticado');
        }

        // Capturar el valor de búsqueda y fechas
        $search = $request->input('search');  // Recibe el término de búsqueda
        $startDate = $request->input('start_date');  // Fecha de inicio
        $endDate = $request->input('end_date');  // Fecha de fin

        // Obtener todas las solicitudes con filtros de búsqueda y paginación
        $solicitudes = Reqhead::with('detalles', 'warehouse', 'destinoRel', 'user')  // Asegurándonos de incluir la relación 'user'
            ->when($search, function ($query, $search) {
                // Filtrar por ID de solicitud o nombre de usuario
                return $query->where('id', 'like', '%' . $search . '%')  // Buscar por ID de solicitud
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');  // Buscar por nombre de usuario
                    });
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                // Filtrar por el rango de fechas
                return $query->whereBetween('fecha', [$startDate, $endDate]);  // Filtrar por rango de fechas
            })
            ->orderBy('id', 'desc')
            ->paginate(10);  // Pagina los resultados, 10 por página

        // Retornar la vista con todas las solicitudes y los filtros aplicados
        return view('livewire.solicitagasto.solicitudes-admin', compact('solicitudes', 'search', 'startDate', 'endDate'));
    }




    public function generarPDFSolicitud($id)
    {
        // Obtener la solicitud con sus detalles y las relaciones necesarias
        $solicitud = Reqhead::with('detalles', 'user', 'destinoRel', 'warehouse')->findOrFail($id);

        // Crear el objeto FPDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Courier', 'B', 15);
        $pdf->Image(public_path('images/logo/nlogo.jpg'), 70, 5, 75);
        // Encabezado
        //$pdf->Cell(0, 10, 'CLINICA RADIOLOGICA Y LABORATORIO CLINICO DR. MENDOZA', 0, 1, 'C');
        //$pdf->Cell(0, 10, '9A. AVENIDA SUR, NO. 411, SAN MIGUEL', 0, 1, 'C');
        $pdf->Ln(10);  // Salto de línea

        // Información de la solicitud dentro de un recuadro
        $pdf->SetFont('Courier', 'B', 10);
        $pdf->Rect(10, 40, 192, 20);  // Dibuja el recuadro (X, Y, ancho, alto)
        $pdf->SetXY(12, 42); // Ajusta la posición para la información dentro del recuadro

        // Información de la solicitud con más espacio entre las líneas
        $pdf->Cell(60, 8, 'Solicitante: ' . $solicitud->user->name . '     ' .  'Destino: ' . ($solicitud->destinoRel->nombre ?? 'No especificado'), 0, 1);
        //$pdf->Cell(60, 8, 'Destino: ' . ($solicitud->destinoRel->nombre ?? 'No especificado'), 0, 1);
        // $pdf->Cell(60, 8, 'Requisicion #: ' . $solicitud->id, 0, 1);
        $pdf->Cell(60, 8, 'Fecha: ' . \Carbon\Carbon::parse($solicitud->fecha)->format('d-m-Y H:i') . '        ' . 'Requisicion #: ' . $solicitud->id, 0, 1);

        $pdf->Ln(10);  // Salto de línea después de la información

        // Título de la tabla de productos
        $pdf->SetFillColor(168, 167, 167);  // Color de fondo: gris
        // Establecer el color del texto (rojo)
        //$pdf->SetTextColor(255, 0, 0);  // Color del texto: rojo
        // Establecer el color de los bordes (negro)
        // $pdf->SetDrawColor(0, 0, 0);  // Color de los bordes: negro
        $pdf->SetTextColor(0, 0, 0);  // Color de texto: negro

        $pdf->SetFont('Courier', 'B', 8);
        $pdf->Cell(15, 6, 'Codigo', 1, 0, 'C', true);
        $pdf->Cell(115, 6, 'Nombre Producto', 1, 0, 'C', true);
        $pdf->Cell(22, 6, 'Presentacion', 1, 0, 'C', true);
        //$pdf->Cell(20, 6, 'ManejadoPor', 1, 0, 'C', true);
        $pdf->Cell(20, 6, 'Solicitado', 1, 0, 'C', true);
        $pdf->Cell(20, 6, 'Autorizado', 1, 1, 'C', true);

        // Cuerpo de la tabla con los productos
        $pdf->SetFont('Courier', '', 8);
        foreach ($solicitud->detalles as $item) {
            // Obtener datos del producto
            $producto = Product::find($item->producto_id); // Consultamos la tabla de productos para obtener la presentación y manejador
            $presentacion = $producto ? $producto->presentacion : 'No especificado';
            $manejador = $producto ? $producto->manejopor : 'No especificado';

            // Aseguramos que las celdas no se monten
            $pdf->Cell(15, 6, $item->producto_id, 1, 0, 'C');
            $pdf->Cell(115, 6, utf8_decode($item->producto), 1, 0, 'L');  // Alineamos el nombre del producto a la izquierda
            $pdf->Cell(22, 6, $presentacion, 1, 0, 'C');
            //$pdf->Cell(20, 6, $manejador, 1, 0, 'C');
            $pdf->Cell(20, 6, number_format($item->cantidad, 2), 1, 0, 'C');  // Formateamos la cantidad solicitada
            $pdf->Cell(20, 6, number_format($item->autorizado, 2), 1, 1, 'C');  // Formateamos la cantidad despachada
        }

        // Pie de página con los apartados para firmar
        $pdf->Ln(10);  // Salto de línea
        $pdf->Ln(10);  // Salto de línea
        $pdf->Ln(10);  // Salto de línea
        $pdf->SetFont('Courier', 'B', 10);
        $pdf->Cell(60, 10, '   _________________________' . '     ' . '_________________________' . '     ' . '_________________________', 0, 1);
        $pdf->Cell(60, 10, '    Firma del Solicitante:' . '           ' . 'Firma del Aprobador: ' . '        ' . 'Firma del Despachador:', 0, 1,);
        $pdf->Cell(60, 8, '    ' . $solicitud->user->name, 0, 1);
        //$pdf->Cell(60, 10, 'Firma del Aprobador: ____________________', 0, 1);
        // Generar el PDF para salida (lo puedes forzar a descarga o visualizar en el navegador)
        $pdf->Output();
        exit;
    }
}

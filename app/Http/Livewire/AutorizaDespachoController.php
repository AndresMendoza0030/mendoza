<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\WithPagination;
use Livewire\Component;
use App\Warehouse;
use App\Product;
use App\Productstock;
use App\Inventoryhead;
use App\Movdetail;
use App\Destino;
use App\Reqhead;
use App\Detareq;
use App\Documento;
use App\Generalsetup;
use App\Vencimiento;
use App\User;

class AutorizaDespachoController extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    //properties
    public  $selected_id, $search, $lafecha, $tipodoc, $condetalle, $listo, $numeroreg;
    public  $mdproducto, $mdcantidad, $mdstock, $mdautorizado, $mdreg, $numerodocumento = 0;
    public  $elproducto, $cnsolicitada, $cnstock;
    public  $labodega, $cuales, $cualesbod, $idtrabajo, $xdocumento, $bodegatrabajo, $tipomovimiento;
    public  $x = 0;

    private $pagination = 12;
    public  $action = 1;

    // Colecciones
    public  $detalle, $info, $bodegas, $destinos, $productos, $tipomovs;

    public function mount()
    {
        $this->lafecha   = date('Y-m-d');
        $this->labodega  = '';
        $this->tipodoc   = '';
        $this->cuales    = 1;
        $this->cualesbod = 1;
        $this->condetalle = 0;
        $this->listo     = 0;
        $this->numeroreg = 0;

        $this->detalle   = collect([]);
        $this->info      = collect([]);
    }

    public function render()
    {
        if ($this->labodega == '' && $this->tipodoc == '') {
            // Muestra estatus=0 o 1 y despachado='N'
            $pendientes = Reqhead::leftjoin('documentos as d', 'd.documento', 'reqhead.tipodoc')
                ->leftjoin('warehouses as w', 'w.id', 'reqhead.bodega')
                ->leftjoin('warehouses as w2', 'w2.id', 'reqhead.bodegadestino')
                ->leftjoin('users as u', 'u.id', 'reqhead.user_id')
                ->leftjoin('destinos as ds', 'ds.id', 'reqhead.destino_id')
                ->select(
                    'reqhead.*',
                    'd.nombre as documento',
                    'w.nombre as cbodega',
                    'u.name as usuario',
                    'w2.nombre as cbodegadestino',
                    'ds.nombre as cdestino',
                    'reqhead.id as idm'
                )
                ->whereIn('reqhead.estatus', [0, 1])
                ->where('reqhead.despachado', 'N')
                ->orderBy('reqhead.bodega')
                ->orderBy('reqhead.tipodoc')
                ->paginate($this->pagination);
        } elseif ($this->labodega != '' && $this->tipodoc == '') {
            $pendientes = Reqhead::leftjoin('documentos as d', 'd.documento', 'reqhead.tipodoc')
                ->leftjoin('warehouses as w', 'w.id', 'reqhead.bodega')
                ->leftjoin('warehouses as w2', 'w2.id', 'reqhead.bodegadestino')
                ->leftjoin('users as u', 'u.id', 'reqhead.user_id')
                ->leftjoin('destinos as ds', 'ds.id', 'reqhead.destino_id')
                ->select(
                    'reqhead.*',
                    'd.nombre as documento',
                    'w.nombre as cbodega',
                    'u.name as usuario',
                    'w2.nombre as cbodegadestino',
                    'ds.nombre as cdestino',
                    'reqhead.id as idm'
                )
                ->whereIn('reqhead.estatus', [0, 1])
                ->where('reqhead.despachado', 'N')
                ->where('reqhead.bodega', $this->labodega)
                ->orderBy('reqhead.bodega')
                ->orderBy('reqhead.tipodoc')
                ->paginate($this->pagination);
        } elseif ($this->labodega != '' && $this->tipodoc != '') {
            $pendientes = Reqhead::leftjoin('documentos as d', 'd.documento', 'reqhead.tipodoc')
                ->leftjoin('warehouses as w', 'w.id', 'reqhead.bodega')
                ->leftjoin('warehouses as w2', 'w2.id', 'reqhead.bodegadestino')
                ->leftjoin('users as u', 'u.id', 'reqhead.user_id')
                ->leftjoin('destinos as ds', 'ds.id', 'reqhead.destino_id')
                ->select(
                    'reqhead.*',
                    'd.nombre as documento',
                    'w.nombre as cbodega',
                    'u.name as usuario',
                    'w2.nombre as cbodegadestino',
                    'ds.nombre as cdestino',
                    'reqhead.id as idm'
                )
                ->whereIn('reqhead.estatus', [0, 1])
                ->where('reqhead.tipodoc', $this->tipodoc)
                ->where('reqhead.despachado', 'N')
                ->where('reqhead.bodega', $this->labodega)
                ->orderBy('reqhead.bodega')
                ->orderBy('reqhead.tipodoc')
                ->paginate($this->pagination);
        } else { // labodega=='' && tipodoc!=''
            $pendientes = Reqhead::leftjoin('documentos as d', 'd.documento', 'reqhead.tipodoc')
                ->leftjoin('warehouses as w', 'w.id', 'reqhead.bodega')
                ->leftjoin('warehouses as w2', 'w2.id', 'reqhead.bodegadestino')
                ->leftjoin('users as u', 'u.id', 'reqhead.user_id')
                ->leftjoin('destinos as ds', 'ds.id', 'reqhead.destino_id')
                ->select(
                    'reqhead.*',
                    'd.nombre as documento',
                    'w.nombre as cbodega',
                    'u.name as usuario',
                    'w2.nombre as cbodegadestino',
                    'ds.nombre as cdestino',
                    'reqhead.id as idm'
                )
                ->whereIn('reqhead.estatus', [0, 1])
                ->where('reqhead.tipodoc', '=', $this->tipodoc)
                ->where('reqhead.despachado', 'N')
                ->orderBy('reqhead.bodega')
                ->orderBy('reqhead.tipodoc')
                ->paginate($this->pagination);
        }

        $fecha = date('d-m-Y');
        $this->fecha = $fecha;

        $this->crearArreglos();

        return view('livewire.autorizadespacho.component', [
            'docum' => $pendientes,
        ]);
    }

    public function updatingSearch()
    {
        $this->gotoPage(1);
    }

    // Editar la línea individual
    public function editaLinea($rg)
    {
        $this->mdreg = 0;
        foreach ($this->info as $i) {
            if ($i['reg'] == $rg) {
                $this->mdproducto   = $i['producto'];
                $this->mdcantidad   = $i['solicitado'];
                $this->mdstock      = $i['stock'];
                // Lo que el usuario ya había puesto como autorizado
                $this->mdautorizado = $i['autorizado'] ?? '';
                //
                $this->mdreg       = $rg;
            }
        }
        if ($this->mdreg > 0) {
            $this->emit("modalEdicion", [
                "mdproducto" => $this->mdproducto,
                "mdcantidad" => $this->mdcantidad,
                "mdstock"    => $this->mdstock
            ]);
        }
    }

    // Grabar el cambio parcial
    public function grabaItem($valor)
    {
        // $valor es la cantidad que el usuario digita en el modal
        $rg = $this->mdreg;

        $this->info->transform(function ($item) use ($rg, $valor) {
            if ($item['reg'] == $rg) {
                $item['autorizado'] = $valor;
            }
            return $item;
        });

        // Actualizar la DB en detareqs
        $registro = Detareq::find($rg);
        if ($registro) {
            $registro->autorizado = $valor;
            $registro->save();
        }
    }

    // Autorizar completamente 1 línea
    public function autorizaLinea($rgy)
    {
        $this->info->transform(function ($item) use ($rgy) {
            if ($item['reg'] == $rgy) {
                $item['autorizado'] = $item['solicitado'];
                // Actualizamos la DB
                if (isset($item['iddetalle'])) {
                    $detalle = Detareq::find($item['iddetalle']);
                    if ($detalle) {
                        $detalle->autorizado = $detalle->cantidad;
                        $detalle->save();
                    }
                }
            }
            return $item;
        });
        $this->emit('msgok', "Item fue autorizado al 100%");
    }

    // Autoriza todo el documento
    public function autorizaDocumento()
    {
        if ($this->numerodocumento > 0) {
            // Recorre $this->info y guarda la cantidad PARCIAL
            foreach ($this->info as $i) {
                $registro = Detareq::find($i['iddetalle']);
                if ($registro) {
                    // En lugar de forzar a la cantidad solicitada, usamos la parcial:
                    $registro->autorizado = $i['autorizado'];
                    $registro->save();
                }
            }

            // Cambiamos cabecera a estatus=1
            $docum = Reqhead::find($this->numerodocumento);
            if ($docum) {
                $docum->autorizado   = "S";
                $docum->autorizadoen = Carbon::now();
                $docum->autoriza_id  = auth()->user()->id;
                $docum->estatus      = 1;
                $docum->save();
            }
        }

        // Limpieza
        $this->info = collect([]);
        $this->emit('msgok', 'Documento autorizado con las cantidades parciales!');
        $this->action = 1;
    }

    public function verDetalle($idm)
    {
        $this->x       = 0;
        $this->listo   = 0;
        $this->detalle = collect([]);
        $this->info    = collect([]);

        // Obtenemos la cabecera
        $documento = Reqhead::where('reqhead.id', $idm)
            ->leftJoin('documentos as d', 'd.documento', 'reqhead.tipodoc')
            ->leftJoin('warehouses as w', 'w.id', 'reqhead.bodega')
            ->leftJoin('warehouses as w2', 'w2.id', 'reqhead.bodegadestino')
            ->leftJoin('users as u', 'u.id', 'reqhead.user_id')
            ->leftJoin('destinos as ds', 'ds.id', 'reqhead.destino_id')
            ->select(
                'reqhead.*',
                'd.nombre as documento',
                'w.nombre as cbodega',
                'u.email as usuario',
                'w2.nombre as cbodegadestino',
                'ds.nombre as cdestino',
                'd.movimiento as tipodocumento'
            )
            ->first();

        if ($documento) {
            $this->labodega      = $documento->bodega;
            $this->tipodocumento = $documento->tipodocumento;
            $this->numerodocumento = $idm;
        }

        $this->despachabod   = $documento->cbodega;
        $this->eldestino     = trim($documento->cbodegadestino) . trim($documento->cdestino) . trim($documento->nombrepaciente);
        $this->lafechadoc    = trim($documento->fecha) . ' sol.por: ' . trim($documento->usuario);
        $this->bodegatrabajo = $documento->bodega;
        $this->xdocumento    = $documento->documento;

        // ====== AQUÍ VIENE EL JOIN CON productstocks ======
        $detalle = Detareq::leftJoin('products as p', 'p.id', '=', 'detareqs.producto_id')
            ->leftJoin('productstocks as s', function ($join) {
                // SÓLO aquí en la clausura, indicamos la relación con “bodega”.
                // Es un left join, así si s.bodega no coincide, s.* será null,
                // pero el producto igual aparecerá.
                $join->on('s.producto_id', '=', 'detareqs.producto_id')
                    ->where('s.bodega', '=', $this->labodega);
            })
            ->leftJoin('documentos as dc', 'dc.documento', 'detareqs.tipodoc')
            ->select(
                'detareqs.*',
                'dc.movimiento as movimiento',
                DB::raw('COALESCE(s.existencia,0) as stock'),
                DB::raw("CONCAT(TRIM(p.nombre),' ',TRIM(p.presentacion)) as elproducto")
            )
            // OJO: no pongas ->where('s.bodega','=',...) aquí fuera
            ->where('detareqs.bodega', '=', $this->labodega)
            ->where('detareqs.numerodoc', '=', $idm)
            ->get();



        $reg = 1;
        foreach ($detalle as $d) {
            $st       = $d->stock ?? 0;         // stock en la bodega
            $despacho = 0;
            $lv       = 0;
            $cv       = '';
            $pide     = "SI";

            // Tus lógicas de tipodocumento == "S" o == "E" ...
            // por ejemplo:

            if ($this->tipodocumento == "S") {
                // Ver si hay Lote en Vencimiento
                $vence = Vencimiento::where('producto_id', $d->producto_id)
                    ->where('bodega', $this->labodega)
                    ->where('total', '>', 0)
                    ->get();
                // ...
            }
            // etc.

            // Lo importante es que finalmente push en $this->info:
            $this->info->push([
                "reg"        => $reg,
                "id"         => $d->idoriginal,   // si se usa
                "productoid" => $d->producto_id,
                "producto"   => $d->elproducto,
                "solicitado" => $d->cantidad,
                "stock"      => $st,
                "despachado" => $despacho,
                "movimiento" => $this->tipodocumento,
                "cvence"     => $cv,
                "lotevence"  => $lv,
                "pedir"      => $pide,
                "iddetalle"  => $d->id,
                "autorizado" => $d->autorizado   // para autorización parcial
            ]);

            $reg++;
        }

        $this->idtrabajo = $idm;
        $this->action    = 2;
    }


    public function setAction($laccion)
    {
        $this->action = $laccion;
    }

    public function crearArreglos()
    {
        $this->bodegas  = Warehouse::where('estatus', 0)->get();
        $this->destinos = Destino::where('estatus', 0)->get();
        $this->tipomovs = Documento::where('estatus', 0)
            ->where('tipo', 'SOL')->get();
    }

    public function resetInput()
    {
        $this->crearArreglos();

        $fecha = date('d-m-Y');
        $this->fecha = $fecha;

        $this->bodega   = '';
        $this->destino  = '';
        $this->concepto = '';

        $this->producto     = '';
        $this->elproducto   = '';
        $this->buscarProducto = '';
        $this->selected_id   = null;
        $this->action        = 1;
        $this->search        = '';
    }

    protected $listeners = [
        'deleteRow'         => 'destroy',
        'deleteRow2'        => 'destroy2',
        'deleteDoc'         => 'destroyAll',
        'grabaItem'         => 'grabaItem',
        'autorizaDocumento' => 'autorizaDocumento'
    ];

    public function destroy($id)
    {
        $nuevodet = collect([]);
        foreach ($this->info as $rs) {
            if ($rs['iddetalle'] != $id) {
                $nuevodet->push([
                    "reg" => $rs['reg'],
                    "id" => $rs['id'],
                    "productoid" => $rs['productoid'],
                    "producto" => $rs['producto'],
                    "solicitado" => $rs['solicitado'],
                    "stock" => $rs['stock'],
                    "despachado" => $rs['despachado'],
                    "movimiento" => $rs['movimiento'],
                    "cvence" => $rs['cvence'],
                    "lotevence" => $rs['lotevence'],
                    "pedir" => $rs['pedir'],
                    "iddetalle" => $rs['iddetalle'],
                    "autorizado" => $rs['autorizado']
                ]);
            }
        }
        $aborrar = Detareq::find($id);
        if ($aborrar) {
            $aborrar->delete();
        }
        $this->info = $nuevodet;
        $this->emit('msgok', 'Item eliminado con éxito');
    }

    public function destroyAll($id)
    {
        if ($id) {
            DB::beginTransaction();
            try {
                $head = Reqhead::find($id);
                $elnumero = $head->id;
                $head->delete();
                Detareq::where('numerodoc', $elnumero)->delete();
                DB::commit();
                $this->emit('msgok', "La Solicitud ha sido Eliminada por Completo!!!");
            } catch (\Exception $e) {
                DB::rollback();
                dd($e);
            }
        }
    }
}

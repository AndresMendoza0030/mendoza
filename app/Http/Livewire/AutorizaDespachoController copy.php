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
use App\Productstock;
use App\Productclassinvoice;
use App\Inventoryhead;
use App\Movdetail;
use App\Destino;
use App\Productsstocks;
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
    public  $selected_id, $search, $lafecha, $tipodoc, $condetalle, $listo, $numeroreg;               //para búsquedas y fila seleccionada
    public  $mdproducto, $mdcantidad, $mdstock, $mdautorizado, $mdreg, $numerodocumento = 0;
    public  $elproducto, $cnsolicitada, $cnstock;
    public  $labodega, $cuales, $cualesbod, $idtrabajo, $xdocumento, $bodegatrabajo, $tipomovimiento;
    public  $x = 0;

    private $pagination = 12;                     //paginación de tabla
    public  $action = 1;

    public  $detalle, $info, $bodegas, $destinos, $productos, $tipomovs; //listas para select


    public function mount()
    {
        $lafecha = date('Y-m-d');
        $this->lafecha = $lafecha;
        $this->labodega = '';
        $this->tipodoc = '';
        $this->cuales = 1;
        $this->cualesbod = 1;
        $this->condetalle = 0;
        $this->listo = 0;
        $this->numeroreg = 0;
        //
        $this->detalle = collect([]);
        $this->info = collect([]);
        //
    }

    //método que se ejecuta al inciar el componente
    public function render()
    {
        if ($this->labodega == '' && $this->tipodoc == '') {
            // EJEMPLO de una sola condición (sin filtrar por bodega o tipodoc)
            // MUESTRA estatus en [0,1] Y despachado='N'
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
                ->whereIn('reqhead.estatus', [0, 1])   // <- AQUI: ambos estados (Pendiente:0) y (Autorizado:1)
                ->where('reqhead.despachado', '=', 'N') // <- NO despachado
                ->orderBy('reqhead.bodega')
                ->orderBy('reqhead.tipodoc')
                ->paginate($this->pagination);
        }

        if ($this->labodega != '' && $this->tipodoc == '') {
            $pendientes = Reqhead::leftjoin('documentos as d', 'd.documento', 'reqhead.tipodoc')
                ->leftjoin('warehouses as w', 'w.id', 'reqhead.bodega')
                ->leftjoin('warehouses as w2', 'w2.id', 'reqhead.bodegadestino')
                ->leftjoin('users as u', 'u.id', 'reqhead.user_id')
                ->leftjoin('destinos as ds', 'ds.id', 'reqhead.destino_id')
                ->select(
                    "reqhead.*",
                    'd.nombre as documento',
                    'w.nombre as cbodega',
                    'u.name as usuario',
                    'w2.nombre as cbodegadestino',
                    'ds.nombre as cdestino',
                    'w.nombre as cbodega',
                    'reqhead.id as idm'
                )
                ->whereIn('reqhead.estatus', [0, 1])
                ->where('reqhead.despachado', '=', 'N')->where('reqhead.bodega', '=', $this->labodega)
                ->orderby('reqhead.bodega')->orderby('reqhead.tipodoc')->paginate($this->pagination);
        }

        if ($this->labodega != '' && $this->tipodoc != '') {
            $pendientes = Reqhead::leftjoin('documentos as d', 'd.documento', 'reqhead.tipodoc')
                ->leftjoin('warehouses as w', 'w.id', 'reqhead.bodega')
                ->leftjoin('warehouses as w2', 'w2.id', 'reqhead.bodegadestino')
                ->leftjoin('users as u', 'u.id', 'reqhead.user_id')
                ->leftjoin('destinos as ds', 'ds.id', 'reqhead.destino_id')
                ->select(
                    "reqhead.*",
                    'd.nombre as documento',
                    'w.nombre as cbodega',
                    'u.name as usuario',
                    'w2.nombre as cbodegadestino',
                    'ds.nombre as cdestino',
                    'w.nombre as cbodega',
                    'reqhead.id as idm'
                )
                ->where('reqhead.estatus', '=', [0, 1])->where('reqhead.tipodoc', '=', $this->tipodoc)
                ->where('reqhead.despachado', '=', 'N')->where('reqhead.bodega', '=', $this->labodega)
                ->orderby('reqhead.bodega')->orderby('reqhead.tipodoc')->paginate($this->pagination);
        }

        if ($this->labodega == '' && $this->tipodoc != '') {
            $pendientes = Reqhead::leftjoin('documentos as d', 'd.documento', 'reqhead.tipodoc')
                ->leftjoin('warehouses as w', 'w.id', 'reqhead.bodega')
                ->leftjoin('warehouses as w2', 'w2.id', 'reqhead.bodegadestino')
                ->leftjoin('users as u', 'u.id', 'reqhead.user_id')
                ->leftjoin('destinos as ds', 'ds.id', 'reqhead.destino_id')
                ->select(
                    "reqhead.*",
                    'd.nombre as documento',
                    'w.nombre as cbodega',
                    'u.name as usuario',
                    'w2.nombre as cbodegadestino',
                    'ds.nombre as cdestino',
                    'w.nombre as cbodega',
                    'reqhead.id as idm'
                )
                ->where('reqhead.estatus', '=', [0, 1])->where('reqhead.tipodoc', '=', $this->tipodoc)
                ->where('reqhead.despachado', '=', 'N')
                ->orderby('reqhead.bodega')->orderby('reqhead.tipodoc')->paginate($this->pagination);
        }



        $fecha = date('d-m-Y');
        $this->fecha = $fecha;


        //$this->pendientes = $pendientes;
        $this->crearArreglos();

        //asignamos a la propiedad clientes el resultado dela búsqueda
        return view('livewire.autorizadespacho.component', [
            'docum' => $pendientes,
        ]);
    }

    public function updatingSearch()
    {
        $this->gotoPage(1);
    }

    public function editaLinea($rg)
    {
        $this->mdreg = 0;
        foreach ($this->info as $i) {
            if ($i['reg'] == $rg) {
                $this->mdproducto = $i['producto'];
                $this->mdcantidad = $i['solicitado'];
                $this->mdstock    = $i['stock'];
                $this->mdautorizado = '';
                //
                $this->mdreg = $rg;
                //dd($this->mdproducto."  ".$this->mdcantidad."  ".$this->mdstock);
                //
            }
        }
        if ($this->mdreg > 0)
            $this->emit("modalEdicion", ["mdproducto" => $this->mdproducto, "mdcantidad" => $this->mdcantidad, "mdstock" => $this->mdstock]);
    }

    public function grabaItem($valor)
    {
        //
        $v1 = $valor;
        $rg = $this->mdreg;

        $idr = $rg;
        //
        $this->info->transform(function ($item) use ($rg, $v1) {
            if ($item['reg'] == $rg) {
                $item['autorizado'] = $v1;
                //$idr=$item['iddetalle'];
            }
            return $item;
        });
        //upd tabla
        $registro = Detareq::find($idr);
        if ($registro) {
            $registro->autorizado = $valor;
            $registro->save();
        }
        //
    }

    public function autorizaLinea($rgy)
    {
        //
        $elid = 0;
        $soli = 0;
        $rg = $rgy;
        //
        $this->info->transform(function ($item) use ($rg, $elid, $soli) {
            if ($item['reg'] == $rg) {
                $item['autorizado'] = $item['solicitado'];
                $elid = $item['iddetalle'];
                $soli = $item['solicitado'];
            }
            return $item;
        });
        //upd tabla
        $registro = Detareq::find($elid);
        if ($registro) {
            $registro->autorizado = $registro->cantidad;
            $registro->save();
        }
        //
        $this->emit('msgok', "Item ha sido autorizado");
    }

    public function autorizaDocumento()
    {
        if ($this->numerodocumento > 0) {
            // 1) Autorizar cada ítem de la colección $this->info
            foreach ($this->info as $i) {
                $registro = Detareq::find($i['iddetalle']);
                if ($registro) {
                    // Se asigna como “autorizado” toda la cantidad solicitada
                    $registro->autorizado = $registro->cantidad;
                    $registro->save();
                }
            }

            // 2) Buscar la cabecera y marcarla como autorizada
            $docum = Reqhead::find($this->numerodocumento);
            if ($docum) {
                $docum->autorizado   = "S";
                $docum->autorizadoen = Carbon::now();
                $docum->autoriza_id  = auth()->user()->id;

                // NUEVO: Ajustar 'estatus' a 1 para indicar “Autorizado”
                $docum->estatus      = 1;
                $docum->save();
            }
        }

        // 3) Limpieza y notificación
        $this->info = collect([]);
        $this->emit('msgok', 'El documento fue Autorizado en su totalidad!!!');
        $this->action = 1;
    }


    public function verDetalle($idm)
    {

        $this->x = 0;
        $this->listo = 0;
        //
        $this->detalle = collect([]);
        $this->info = collect([]);


        $documento = Reqhead::where('reqhead.id', '=', $idm)
            ->leftjoin('documentos as d', 'd.documento', 'reqhead.tipodoc')
            ->leftjoin('warehouses as w', 'w.id', 'reqhead.bodega')
            ->leftjoin('warehouses as w2', 'w2.id', 'reqhead.bodegadestino')
            ->leftjoin('users as u', 'u.id', 'reqhead.user_id')
            ->leftjoin('destinos as ds', 'ds.id', 'reqhead.destino_id')
            ->select(
                "reqhead.*",
                'd.nombre as documento',
                'w.nombre as cbodega',
                'u.email as usuario',
                'w2.nombre as cbodegadestino',
                'ds.nombre as cdestino',
                'd.movimiento as tipodocumento'
            )
            ->first();
        //
        if ($documento) {
            $this->labodega = $documento->bodega;
            $this->tipodocumento = $documento->tipodocumento;
            $this->numerodocumento = $idm;
        }

        $this->despachabod = $documento->cbodega;
        $this->eldestino  = TRIM($documento->cbodegadestino) . TRIM($documento->cdestino) . TRIM($documento->nombrepaciente);
        $this->lafechadoc = TRIM($documento->fecha) . ' sol.por: ' . TRIM($documento->usuario);
        $this->bodegatrabajo = $documento->bodega;

        $this->xdocumento = $documento->documento;

        //->leftjoin('productstocks as w','w.producto_id', 'detareqs.producto_id')
        //
        $detalle = Detareq::leftjoin('products as p', 'p.id', 'detareqs.producto_id')
            ->leftjoin('documentos as dc', 'dc.documento', 'detareqs.tipodoc')
            ->select(
                "detareqs.*",
                'dc.movimiento as movimiento',
                DB::raw("CONCAT(TRIM(p.nombre),' ',TRIM(p.presentacion)) as elproducto")
            )
            ->where('detareqs.bodega', '=', $this->labodega)->where('detareqs.numerodoc', '=', $idm)
            ->get();
        //


        $midetalle = collect([]);

        $reg = 1;

        foreach ($detalle as $d) {
            if ($this->tipodocumento == "S") {
                $st = '';
                $stk = Productstock::where('producto_id', '=', $d->producto_id)->where('bodega', '=', $this->labodega)
                    ->first();
                if ($stk) {
                    $st = $stk->existencia;
                }

                $despacho = 0;
                $lv = 0;
                $cv = '';
                $pide = "SI";
                //
                $vence = Vencimiento::where('producto_id', '=', $d->producto_id)->where('bodega', '=', $this->labodega)
                    ->where('total', '>', 0)->get();

                if ($vence->count() > 0) {
                    if ($vence->count() == 1 && $vence[0]->total >= $d->cantidad) {
                        $despacho = $d->cantidad;
                        $lv = $vence[0]->id;
                        $pide = "NO";
                        $cv = $vence[0]->mes . "/" . $vence[0]->anno;
                    }
                } else {
                    if (floatval($st) >= floatval($d->cantidad)) {
                        $despacho = $d->cantidad;
                        $lv = 0;
                        $pide = "NO";
                        $cv = '';
                    }
                }
                $this->info->push([
                    "reg" => $reg,
                    "id" => $d->idoriginal,
                    "productoid" => $d->producto_id,
                    "producto" => $d->producto,
                    "solicitado" => $d->cantidad,
                    "stock" => $st,
                    "despachado" => $despacho,
                    "movimiento" => $this->tipodocumento,
                    "cvence" => $cv,
                    "lotevence" => $lv,
                    "pedir" => $pide,
                    "iddetalle" => $d->id,
                    "autorizado" => $d->autorizado
                ]);
                //
                $reg += 1;
                //
            }
            //
            if ($this->tipodocumento == "E") {
                $st = '';
                $cv = '';
                $despacho = $d->cantidad;
                $lv = $d->lotevence;
                $pide = "NO";
                //
                $vence = Vencimiento::find($d->lotevence);
                if ($vence) {
                    $cv = $vence->mes . "/" . $vence->anno;
                }
                //
                $this->info->push([
                    "reg" => $reg,
                    "id" => $d->idoriginal,
                    "productoid" => $d->producto_id,
                    "producto" => $d->producto,
                    "solicitado" => $d->cantidad,
                    "stock" => $st,
                    "despachado" => $despacho,
                    "movimiento" => $this->tipodocumento,
                    "cvence" => $cv,
                    "lotevence" => $lv,
                    "pedir" => $pide,
                    "iddetalle" => $d->id,
                    "autorizado" => $d->autorizado
                ]);
                //
                $reg += 1;
                //
            }

            if ($pide == "NO") //crear en detalle
            {
                $this->detalle->push([
                    "reg" => $this->numeroreg,
                    "id" => $d->idoriginal,
                    "productoid" => $d->producto_id,
                    "producto" => $d->producto,
                    "solicitado" => $d->cantidad,
                    "aprocesar" => $d->cantidad,
                    "cvence" => $cv,
                    "lotevence" => $lv
                ]);
                $this->numeroreg += 1;
            }
        }
        //
        //
        $pedido = 0;
        $despacho = 0;
        //
        $pedido = $this->info->sum('solicitado');
        $despacho = $this->detalle->sum('aprocesar');
        if ($pedido == $despacho) {
            $this->listo = 1;
        }
        //
        $this->idtrabajo = $idm;
        $this->action = 2;
        //$this->detalle = $detalle;
    }


    public function setAction($laccion)
    {
        $this->action = $laccion;
    }

    public function crearArreglos()
    {
        $this->bodegas      = Warehouse::where('estatus', '=', '0')->get();
        $this->destinos     = Destino::where('estatus', '=', '0')->get();
        $this->tipomovs     = Documento::where('estatus', '=', '0')
            ->where('tipo', '=', 'SOL')->get();
    }
    //método para reiniciar variables
    public function resetInput()
    {
        $this->crearArreglos();

        $fecha = date('d-m-Y');
        $this->fecha = $fecha;

        $this->bodega = '';
        $this->destino = '';
        $this->concepto = '';

        $this->producto = '';

        $this->elproducto = '';
        $this->buscarProducto = '';

        $this->selected_id = null;
        $this->action = 1;
        $this->search = '';
    }


    //escuchar eventos y ejecutar acción solicitada
    protected  $listeners =
    [
        'deleteRow'         => 'destroy',
        'deleteRow2'        => 'destroy2',
        'deleteDoc'         => 'destroyAll',
        'grabaItem'         => 'grabaItem',
        'autorizaDocumento' => 'autorizaDocumento'
    ];


    //método para eliminar un registro de la coleccion info
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
        //
        $aborrar = Detareq::find($id);
        if ($aborrar) {
            $aborrar->delete();
        }
        //
        $this->info = $nuevodet;
        $this->emit('msgok', 'El item fue eliminado con exito...');
        //
    }

    //método para eliminar toto el documento de reqhead y detareq
    public function destroyAll($id)
    {
        if ($id) {
            //iniciamos transacción
            DB::beginTransaction();
            try {
                $head = Reqhead::find($id);
                $elnumero = $head->id;
                $head->delete();
                $deta = Detareq::where('numerodoc', '=', $elnumero);
                $deta->delete();
                //confirmamos transacción
                DB::commit();
                $this->emit('msgok', "La Soicitud ha sido Eliminada por Completo!!!");
            } catch (\Exception $e) {
                //para no tener inconsistencia de la info, deshacemos los cambios en caso de que algo falle
                DB::rollback();
                $status = $e->getMessage();
                dd($e);
            }
        }
    }
}

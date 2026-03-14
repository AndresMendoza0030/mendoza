<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\WithPagination;
use Livewire\Component;
use App\Warehouse;
use App\Productstock;
use App\Inventoryhead;
use App\Movdetail;
use App\Destino;
use App\Reqhead;
use App\Detareq;
use App\Documento;
use App\Generalsetup;
use App\Vencimiento;
use App\Product;
use App\User;

class VisordespachoController extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    //properties
    public  $selected_id, $search, $lafecha, $tipodoc, $condetalle, $listo, $numeroreg;
    public  $despachabod, $eldestino, $lafechadoc, $detalledoc;
    public  $elproducto, $cnsolicitada, $cnstock, $okProcesa = 0;
    public  $labodega, $cuales, $cualesbod, $idtrabajo, $xdocumento, $bodegatrabajo, $tipomovimiento;
    public  $x = 0;

    private $pagination = 12; //paginación
    public  $action = 1;

    public  $detalle, $info, $bodegas, $destinos, $productos, $tipomovs; //listas

    public function mount()
    {
        $this->lafecha = date('Y-m-d');
        $this->labodega = '';
        $this->tipodoc  = '';
        $this->cuales   = 1;
        $this->cualesbod = 1;
        $this->condetalle = 0;
        $this->listo = 0;
        $this->numeroreg = 0;

        $this->detalle = collect([]);
        $this->info = collect([]);
    }

    public function render()
    {
        // Mostramos solicitudes con estatus=1 (autorizadas) y despachado='N' (no despachadas)
        // en lugar de estatus=0
        if ($this->labodega == '' && $this->tipodoc == '') {
            $pendientes = Reqhead::leftJoin('documentos as d', 'd.documento', 'reqhead.tipodoc')
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
                    'w.nombre as cbodega',
                    'reqhead.id as idm'
                )
                ->where('reqhead.estatus', '=', 1)             // <--- CAMBIO A 1
                ->where('reqhead.autorizado', '=', 'S')
                ->where('reqhead.despachado', '=', 'N')
                ->orderBy('reqhead.bodega')
                ->orderBy('reqhead.tipodoc')
                ->paginate($this->pagination);
        } elseif ($this->labodega != '' && $this->tipodoc == '') {
            $pendientes = Reqhead::leftJoin('documentos as d', 'd.documento', 'reqhead.tipodoc')
                ->leftJoin('warehouses as w', 'w.id', 'reqhead.bodega')
                ->leftJoin('warehouses as w2', 'w2.id', 'reqhead.bodegadestino')
                ->leftJoin('users as u', 'u.id', 'reqhead.user_id')
                ->leftJoin('destinos as ds', 'ds.id', 'reqhead.destino_id')
                ->select(
                    'reqhead.*',
                    'd.nombre as documento',
                    'w.nombre as cbodega',
                    'u.name as usuario',
                    'w2.nombre as cbodegadestino',
                    'ds.nombre as cdestino',
                    'w.nombre as cbodega',
                    'reqhead.id as idm'
                )
                ->where('reqhead.estatus', '=', 1)             // <--- CAMBIO A 1
                ->where('reqhead.autorizado', '=', 'S')
                ->where('reqhead.despachado', '=', 'N')
                ->where('reqhead.bodega', '=', $this->labodega)
                ->orderBy('reqhead.bodega')
                ->orderBy('reqhead.tipodoc')
                ->paginate($this->pagination);
        } elseif ($this->labodega != '' && $this->tipodoc != '') {
            $pendientes = Reqhead::leftJoin('documentos as d', 'd.documento', 'reqhead.tipodoc')
                ->leftJoin('warehouses as w', 'w.id', 'reqhead.bodega')
                ->leftJoin('warehouses as w2', 'w2.id', 'reqhead.bodegadestino')
                ->leftJoin('users as u', 'u.id', 'reqhead.user_id')
                ->leftJoin('destinos as ds', 'ds.id', 'reqhead.destino_id')
                ->select(
                    'reqhead.*',
                    'd.nombre as documento',
                    'w.nombre as cbodega',
                    'u.name as usuario',
                    'w2.nombre as cbodegadestino',
                    'ds.nombre as cdestino',
                    'w.nombre as cbodega',
                    'reqhead.id as idm'
                )
                ->where('reqhead.estatus', '=', 1)            // <--- CAMBIO A 1
                ->where('reqhead.tipodoc', '=', $this->tipodoc)
                ->where('reqhead.autorizado', '=', 'S')
                ->where('reqhead.despachado', '=', 'N')
                ->where('reqhead.bodega', '=', $this->labodega)
                ->orderBy('reqhead.bodega')
                ->orderBy('reqhead.tipodoc')
                ->paginate($this->pagination);
        } else { // $this->labodega=='' && $this->tipodoc!=''
            $pendientes = Reqhead::leftJoin('documentos as d', 'd.documento', 'reqhead.tipodoc')
                ->leftJoin('warehouses as w', 'w.id', 'reqhead.bodega')
                ->leftJoin('warehouses as w2', 'w2.id', 'reqhead.bodegadestino')
                ->leftJoin('users as u', 'u.id', 'reqhead.user_id')
                ->leftJoin('destinos as ds', 'ds.id', 'reqhead.destino_id')
                ->select(
                    'reqhead.*',
                    'd.nombre as documento',
                    'w.nombre as cbodega',
                    'u.name as usuario',
                    'w2.nombre as cbodegadestino',
                    'ds.nombre as cdestino',
                    'w.nombre as cbodega',
                    'reqhead.id as idm'
                )
                ->where('reqhead.estatus', '=', 1)            // <--- CAMBIO A 1
                ->where('reqhead.tipodoc', '=', $this->tipodoc)
                ->where('reqhead.despachado', '=', 'N')
                ->where('reqhead.autorizado', '=', 'S')
                ->orderBy('reqhead.bodega')
                ->orderBy('reqhead.tipodoc')
                ->paginate($this->pagination);
        }

        $fecha = date('d-m-Y');
        $this->fecha = $fecha;

        $this->crearArreglos();

        return view('livewire.visordespacho.component', [
            'docum' => $pendientes,
        ]);
    }

    public function updatingSearch()
    {
        $this->gotoPage(1);
    }

    public function verDetalle($idm)
    {
        $this->x      = 0;
        $this->listo  = 0;
        $this->detalle = collect([]);
        $this->info   = collect([]);

        $this->emit('msg-error', $idm);

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

        if ($documento) {
            $this->labodega     = $documento->bodega;
            $this->tipodocumento = $documento->tipodocumento;
        }

        $this->despachabod = $documento->cbodega;
        $this->eldestino   = trim($documento->cbodegadestino) . trim($documento->cdestino) . trim($documento->nombrepaciente);
        $this->lafechadoc  = trim($documento->fecha) . ' sol.por: ' . trim($documento->usuario);
        $this->bodegatrabajo = $documento->bodega;
        $this->xdocumento  = $documento->documento;

        $detalle = Detareq::leftJoin('products as p', 'p.id', 'detareqs.producto_id')
            ->leftJoin('productstocks as w', 'w.producto_id', 'detareqs.producto_id')
            ->leftJoin('documentos as dc', 'dc.documento', 'detareqs.tipodoc')
            ->select(
                "detareqs.*",
                'w.existencia as stock',
                'dc.movimiento as movimiento',
                DB::raw("CONCAT(TRIM(p.nombre),' ',TRIM(p.presentacion)) as elproducto")
            )
            ->where('w.bodega', '=', $this->labodega)
            ->where('detareqs.numerodoc', '=', $idm)
            ->get();

        $reg = 0;

        foreach ($detalle as $d) {
            if ($this->tipodocumento == "S") {
                $st = '';
                $stk = Productstock::where('producto_id', $d->producto_id)
                    ->where('bodega', $this->labodega)
                    ->first();
                if ($stk) {
                    $st = $stk->existencia;
                }

                $despacho = 0;
                $lv = 0;
                $cv = '';
                $pide = "SI";

                $vence = Vencimiento::where('producto_id', '=', $d->producto_id)
                    ->where('bodega', '=', $this->labodega)
                    ->where('total', '>', 0)
                    ->get();

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
                    "autorizado" => $d->autorizado
                ]);
                $reg += 1;

                if ($pide == "NO") {
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
                    $this->numeroreg++;
                }
            } elseif ($this->tipodocumento == "E") {
                $st = '';
                $cv = '';
                $despacho = $d->cantidad;
                $lv = $d->lotevence;
                $pide = "NO";

                $vence = Vencimiento::find($d->lotevence);
                if ($vence) {
                    $cv = $vence->mes . "/" . $vence->anno;
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
                    "autorizado" => $d->autorizado
                ]);
                $reg++;

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
                $this->numeroreg++;
            }
        }

        // Chequeos finales
        $pedido   = $this->info->sum('solicitado');
        $despacho = $this->detalle->sum('aprocesar');
        if ($pedido == $despacho) {
            $this->listo = 1;
        }
        $this->okProcesa = 0;
        $this->listo     = 0;
        $z = 0;
        $autorizados = $this->info->count();

        foreach ($this->info as $i) {
            if ($i['autorizado'] > $i['stock']) {
                $this->okProcesa = 1;
            }
            if ($i['autorizado'] > 0) {
                $z += 1;
            }
        }
        $this->listo = $this->okProcesa;
        if ($z != $autorizados && $z > 0) {
            $this->listo = 1;
        }
        if ($z == 0) {
            $this->listo = 2;
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
        $this->fecha  = date('d-m-Y');
        $this->bodega = '';
        $this->destino = '';
        $this->concepto = '';
        $this->producto = '';
        $this->elproducto   = '';
        $this->buscarProducto = '';
        $this->selected_id   = null;
        $this->action = 1;
        $this->search = '';
    }

    public function confirmaDespacho()
    {
        $usuario   = auth()->user()->id;
        $solicitud = Reqhead::find($this->idtrabajo);
        if (!$solicitud || $solicitud->despachado == "S") {
            $this->emit('msg-error', 'El pedido ' . $this->idtrabajo . ' ya fue despachado previamente.');
            $this->resetInput();
            $this->action = 1;
            return;
        }

        //validando si ya existe un InventoryHead con pedido=?
        $verpedido = Inventoryhead::where('pedido', $this->idtrabajo)->count();
        if ($verpedido > 0) {
            $this->emit('msg-error', 'El pedido ' . $this->idtrabajo . ' ya fue despachado previamente...');
            $this->action = 1;
            return;
        }

        $empresa = Generalsetup::first();
        $tasaiva = round($empresa->piva / 100, 2);

        DB::beginTransaction();
        try {
            // Ejemplo de Flow para PGAS
            if ($solicitud->tipodoc == 'PGAS') {
                // Crea InventoryHead = Salida de Gasto
                $head = InventoryHead::create([
                    'bodega'        => $solicitud->bodega,
                    'bodegadestino' => 0,
                    'pedido'        => $solicitud->id,
                    'destinogasto'  => $solicitud->destino_id,
                    'fecha'         => Carbon::now(),
                    'tipodoc'       => 'SGAS',
                    'estatus'       => 0,
                    'user_id'       => $usuario,
                    'concepto'      => $solicitud->concepto,
                ]);
                // Detalles
                foreach ($this->info as $det) {
                    $produx = Product::find($det['productoid']);
                    $miltvence = ($det['lotevence'] > 0) ? $det['lotevence'] : 0;

                    Movdetail::create([
                        'bodega'       => $head->bodega,
                        'tipodoc'      => $head->tipodoc,
                        'numerodoc'    => $head->id,
                        'fecha'        => $head->fecha,
                        'clase'        => $produx->clase,
                        'linea'        => $produx->linea,
                        'lineafac'     => $produx->lineafac,
                        'producto_id'  => $det['productoid'],
                        'producto'     => $det['producto'],
                        'cantidad'     => $det['autorizado'],
                        'movimiento'   => 'S',
                        'precosto'     => $produx->costo,
                        'subtotal'     => $det['autorizado'] * $produx->costo,
                        'gravado'      => $det['autorizado'] * $produx->costo,
                        'lotevence'    => $miltvence,
                        'estatus'      => 0
                    ]);
                    // Actualizar Vencimiento
                    if ($miltvence > 0) {
                        $ltvc = Vencimiento::find($miltvence);
                        if ($ltvc) {
                            $ltvc->salidas += $det['autorizado'];
                            $ltvc->total    = $ltvc->entradas - $ltvc->salidas;
                            $ltvc->save();
                        }
                    }
                }
            } elseif ($solicitud->tipodoc == 'PTRB') {
                // ... tu lógica de traslado
                // ...
            }

            // Actualizamos cabecera
            $solicitud->despacha_id =  $usuario;
            $solicitud->despacha_mov =  $head->id;
            $solicitud->despachado  = "S";
            $solicitud->despachaen  = Carbon::now();
            $solicitud->save();

            $this->action = 1;
            DB::commit();

            $this->emit('msgok', 'La Solicitud ha sido despachada...');
            $this->emit('print-mov', $head->id);
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
        }
    }

    protected $listeners = [
        'deleteRow'        => 'destroy',
        'deleteRow2'       => 'destroy2',
        'deleteDoc'        => 'destroyAll',
        'grabaItem'        => 'grabaItem',
        'confirmaDespacho' => 'confirmaDespacho'
    ];

    //método para eliminar un registro de la coleccion $this->info
    public function destroy($id)
    {
        $nuevodet = collect([]);
        foreach ($this->info as $rs) {
            if ($rs['reg'] != $id) {
                $nuevodet->push([
                    "reg" => $rs['reg'],
                    "id" => $rs['id'],
                    "productoid" => $rs['productoid'],
                    "producto" => $rs['producto'],
                    "solicitado" => $rs['solicitado'],
                    "stock" => $rs['stock'],
                    "despachado" => $rs['despachado'],
                    "movimiento" => $rs['movimiento'],
                    "clotevence" => $rs['clotevence'] ?? '',
                    "lotevence" => $rs['lotevence'],
                    "pedir" => $rs['pedir']
                ]);
            }
        }
        $this->info = $nuevodet;
    }

    // Eliminar un registro de la coleccion $this->detalle
    public function destroy2($id)
    {
        $nuevodet = collect([]);
        foreach ($this->detalle as $rs) {
            if ($rs['reg'] != $id) {
                $nuevodet->push([
                    "reg" => $rs['reg'],
                    "id" => $rs['id'],
                    "productoid" => $rs['productoid'],
                    "producto" => $rs['producto'],
                    "aprocesar" => $rs['aprocesar'],
                    "cvence" => $rs['cvence'],
                    "lotevence" => $rs['lotevence']
                ]);
            }
        }
        $this->detalle = $nuevodet;
    }

    // Eliminar todo el documento de reqhead + detareq
    public function destroyAll($id)
    {
        if ($id) {
            DB::beginTransaction();
            try {
                $head = Reqhead::find($id);
                $elnumero = $head->id;
                $head->delete();

                Detareq::where('numerodoc', '=', $elnumero)->delete();

                DB::commit();
                $this->emit('msgok', "La Solicitud ha sido Eliminada por Completo!!!");
            } catch (\Exception $e) {
                DB::rollback();
                dd($e);
            }
        }
    }
}

<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Livewire\WithPagination;
use Livewire\Component;
use App\Manufacturer;
use App\Warehouse;
use App\Productclass;
use App\Productline;
use App\Product;
use App\Paciente;
use App\Referente;
use App\Room;
use App\Documento;
use App\Productsstocks;
use App\Productclassinvoice;
use App\ClienteProveedor;
use App\Inventoryhead;
use App\Invoicedetail;
use App\Generalsetup;
use App\Movdetail;
use App\Destino;
use App\Vencimiento;
use App\User;

class MovimientosController extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $tipodoc, $bodega, $desde, $hasta, $tipomovs, $bodegas, $detamov;
    public $action = 1;
    public $pagination = 10;
    public $eldocumento, $lafecha, $labodega, $elconcepto, $listamov;
    public $sujeto, $destinobod, $destinogasto, $paciente, $refecompra, $usuario;

    public function mount()
    {
        $this->tipodoc = "";
        $this->bodega = "";

        $desde = date('Y-m-d');
        $hasta = date('Y-m-d');
        $this->desde = $desde;
        $this->hasta = $hasta;
        $this->listamov = [];

        $tipomovs = Documento::select("documentos.*")
            ->where('tipo', '=', 'BOD')->orderby('movimiento')
            ->where('estatus', '=', 0)
            ->get();

        $bodegas = Warehouse::select("warehouses.*")
            ->where('estatus', '=', 0)->orderby('id')->get();

        $this->tipomovs = $tipomovs;
        $this->bodegas = $bodegas;
    }

    //método que se ejecuta al inciar el componente
    public function render()
    {
        $listamov = Inventoryhead::leftjoin('documentos  as dc', 'dc.documento', 'inventoryhead.tipodoc')
            ->leftjoin('warehouses  as bd', 'bd.id', 'inventoryhead.bodega')
            ->leftjoin('users  as us', 'us.id', 'inventoryhead.user_id')
            ->select(
                "inventoryhead.*",
                'dc.nombre as cdocumento',
                'bd.nombre as cbodega',
                'us.name as nusuario'
            )
            ->where('inventoryhead.tipodoc', '=', $this->tipodoc)
            ->where('inventoryhead.bodega', '=', $this->bodega)
            ->whereBetween('inventoryhead.fecha', [$this->desde, $this->hasta])
            ->paginate($this->pagination);

        foreach ($listamov as $lm) {
            switch ($lm->tipodoc) {
                case 'CPRA':
                    //$lm->concepto = TRIM($lm->sujeto).' '.$lm->tpdocufiscal.' - '.$lm->numdocufiscal;
                    break;
                case 'SCAR':
                    $ccb = Paciente::find($lm->paciente_id);
                    $lm->concepto = "Cargo Paciente No." . TRIM($ccb->nombres) . ', ' . TRIM($ccb->apellidos); //$lm->paciente_id;
                    break;
                case 'SERV':
                    $ccb = Paciente::find($lm->paciente_id);
                    $lm->concepto = "Cargo Paciente No." . TRIM($ccb->nombres) . ', ' . TRIM($ccb->apellidos); //$lm->paciente_id;
                    break;
                case 'STRA':
                    $ccb = Warehouse::find($lm->bodegadestino);
                    $lm->concepto = "Traslado a Bodega: " . $ccb->nombre; //lm->bodegadestino;
                    break;
            }
        }

        //$this->listamov=$listamov;
        //asignamos a la propiedad clientes el resultado dela búsqueda
        return view('livewire.movimientos.component', [
            'info' => $listamov,
        ]);
    }

    public function updatingSearch()
    {
        $this->gotoPage(1);
    }

    public function muestraInfoMov($id)
    {
        $elmov = Inventoryhead::find($id);
        $labodega = Warehouse::find($elmov->bodega);
        $eldocumento = Documento::where('documento', '=', $elmov->tipodoc)
            ->select('documentos.*')->first();
        //llenando variables
        $sujeto = '';
        if ($elmov->sujeto_id > 0) {
            $lsujeto = Clienteproveedor::find($elmov->sujeto_id);
            $sujeto = "Cl: " . $elmov->sujeto_id . " - " . TRIM($lsujeto->razon);
        }

        if ($elmov->bodegadestino > 0) {
            $lsujeto = Warehouse::find($elmov->bodegadestino);
            $sujeto = "Bod.Destino: " . $elmov->bodegadestino . " - " . TRIM($lsujeto->nombre);
        }
        if ($elmov->destinogasto > 0) {
            $lsujeto = Destino::find($elmov->destinogasto);
            $sujeto = "Bod.Destino: " . $elmov->destinogasto . " - " . TRIM($lsujeto->nombre);
        }
        $refecompra = '';
        $refecompra = $elmov->tpdocufiscal . ' - ' . $elmov->numdocufiscal . ' . ' . $elmov->serie;
        $sujeto = TRIM($sujeto) . '  ' . $refecompra;
        $usuario = '';
        if ($elmov->user_id > 0) {
            $usuario = User::find($elmov->user_id);
            $usuario = $usuario->name;
        }



        $lafecha = $elmov->fecha;
        $elconcepto = $elmov->concepto;
        $labodega = $labodega->nombre;

        $eldocumento = $eldocumento->nombre;


        $this->eldocumento = $eldocumento;
        $this->labodega = $labodega;
        $this->lafecha = $lafecha;
        $this->elconcepto = $elconcepto;
        $this->sujeto = $sujeto;
        $this->refecompra = $refecompra;
        $this->usuario = $usuario;


        $detamov = Movdetail::leftjoin('documentos  as dc', 'dc.documento', 'movdetails.tipodoc')
            ->leftjoin('warehouses  as bd', 'bd.id', 'movdetails.bodega')
            ->leftjoin('users  as us', 'us.id', 'movdetails.user_id')
            ->select(
                "movdetails.*",
                'dc.nombre as cdocumento',
                'bd.nombre as cbodega',
                'us.name as nusuario'
            )
            ->where('movdetails.numerodoc', '=', $id)->get();

        $this->detamov = $detamov;
        $this->action = 2;
    }

    //activa la vista edición o creación
    public function doAction($action)
    {
        $this->action = $action;
    }

    public function reversaMovimiento($idp)
    {
        $elmov = Inventoryhead::find($idp);
        //
        if ($elmov) {
            //
            $tipodocu = Documento::where('documento', '=', $elmov->tipodoc)
                ->select("documentos.*")->first();

            if ($elmov->reversado == "SI") {
                $this->emit('msg-error', 'El documento ya fue reversado anteriormente!!!');
                return;
            }

            if ($elmov->tipodoc == 'CPRA') {
                $this->emit('msg-error', 'La reversión de Compras esta temporlmente Des-habilitada!!!');
                return;
            }

            if ($elmov->tipodoc == 'ETRA') {
                $this->emit('msg-error', 'Para Revertir una Entrada x Traslado, debe Reversar La Salida!!!');
                return;
            }


            $eldetalle = Movdetail::where('numerodoc', '=', $idp)->select("movdetails.*")
                ->get();

            //Definiendo Locales
            if ($tipodocu->movimiento == "E") {
                $xmovimiento = "S";
                $xfactor = 1;
                $xtipo = "ANUE";
            }
            if ($tipodocu->movimiento == "S") {
                $xmovimiento = "E";
                $xfactor = 1;
                $xtipo = "ANUS";
            }
            //

            //iniciamos transacción
            DB::beginTransaction();
            try {
                //GRABANDO DOCUMENTO HEAD
                $head = InventoryHead::create([
                    'bodega'        => $elmov->bodega,
                    'docucontra'    => $elmov->id,
                    'bodegadestino' => $elmov->bodegadestino,
                    'fecha'         => date('Y-m-d'),
                    'tipodoc'       => $xtipo,
                    'estatus'       => 0,
                    'flete'         => $elmov->flete * $xfactor,
                    'gravado'       => $elmov->gravado * $xfactor,
                    'iva'           => $elmov->iva * $xfactor,
                    'retencion'     => $elmov->retencion * $xfactor,
                    'percepcion'    => $elmov->percepcion * $xfactor,
                    'total'         => $elmov->total * $xfactor,
                    'fiscal'        => $elmov->fiscal,
                    'user_id'       => auth()->user()->id,
                    'concepto'      => "REVERSA DOCUMENTO: " . $elmov->tipodoc . ":" . $elmov->id . " de fecha: " . Carbon::parse($elmov->fecha),
                ]);
                //upd mov original
                $elmov->reversado = "SI";
                $elmov->save();
                ///
                ///
                foreach ($eldetalle as $det) {
                    $detabod = Movdetail::create([
                        'bodega'        => $head->bodega,
                        'tipodoc'       => $head->tipodoc,
                        'numerodoc'     => $head->id,
                        'fecha'         => $head->fecha,
                        'clase'         => $det->clase,
                        'linea'         => $det->linea,
                        'lineafac'      => $det->lineafac,
                        'producto_id'   => $det->producto_id,
                        'producto'      => $det->producto,
                        'cantidad'      => $det->cantidad * $xfactor,
                        'movimiento'    => $xmovimiento,
                        'tasaiva'       => $det->tasaiva,
                        'user_id'       => $head->user_id,
                        'precosto'      => $det->precosto,
                        'preventa'      => $det->preventa,
                        'subtotal'      => $det->subtotal * $xfactor,
                        'gravado'       => $det->gravado * $xfactor,
                        'iva'           => $det->iva * $xfactor,
                        'total'         => $det->total * $xfactor,
                        'lotevence'     => $det->lotevence,
                        'admongp'       => $det->admongp,
                        'admonval'      => $det->admonval * $xfactor,
                        'estatus'       => 0
                    ]);
                    //reversa vencimiento
                    if ($det->lotevence > 0) {
                        $ltvc = Vencimiento::find($det->lotevence);
                        if ($ltvc) {
                            if ($xmovimiento == "S") {
                                $ltvc->salidas = $ltvc->salidas + $det->cantidad;
                            }
                            if ($xmovimiento == "E") {
                                $ltvc->entradas = $ltvc->entradas + $det->cantidad;
                            }
                            $ltvc->total = $ltvc->entradas - $ltvc->salidas;
                            $ltvc->save();
                        }
                    }
                }
                ///
                //validando anulacion traslado
                if ($elmov->tipodoc == "STRA") {
                    $xmovimiento2 = "S";
                    $xfactor = 1;
                    $xtipo = "ANUE";
                    //
                    $eldetalle2 = Movdetail::where('numerodoc', '=', $elmov->docucontra)->select("movdetails.*")
                        ->get();
                    /////
                    $elmov2 = Inventoryhead::find($elmov->docucontra);
                    if ($elmov2) {
                        //GRABANDO DOCUMENTO HEAD
                        $head2 = InventoryHead::create([
                            'bodega'        => $elmov2->bodega,
                            'docucontra'    => $elmov2->id,
                            'bodegadestino' => $elmov2->bodega,
                            'fecha'         => date('Y-m-d'),
                            'tipodoc'       => $xtipo,
                            'estatus'       => 0,
                            'user_id'       => auth()->user()->id,
                            'concepto'      => "REVERSA ENTRASDA x TRASLADO: " . $elmov2->id . " de fecha: " . Carbon::parse($elmov2->fecha),
                        ]);
                        //upd mov original (ETRA)
                        $elmov2->estatus = 0;
                        $elmov2->reversado = "SI";
                        $elmov2->save();
                        //
                        foreach ($eldetalle2 as $det) {
                            $detabod2 = Movdetail::create([
                                'bodega'        => $head2->bodega,
                                'tipodoc'       => $head2->tipodoc,
                                'numerodoc'     => $head2->id,
                                'fecha'         => $head2->fecha,
                                'clase'         => $det->clase,
                                'linea'         => $det->linea,
                                'lineafac'      => $det->lineafac,
                                'producto_id'   => $det->producto_id,
                                'producto'      => $det->producto,
                                'cantidad'      => $det->cantidad * $xfactor,
                                'movimiento'    => $xmovimiento2,
                                'tasaiva'       => $det->tasaiva,
                                'user_id'       => $head2->user_id,
                                'precosto'      => $det->precosto,
                                'preventa'      => $det->preventa,
                                'subtotal'      => $det->subtotal * $xfactor,
                                'gravado'       => $det->gravado * $xfactor,
                                'iva'           => $det->iva * $xfactor,
                                'total'         => $det->total * $xfactor,
                                'lotevence'     => $det->lotevence,
                                'admongp'       => $det->admongp,
                                'admonval'      => $det->admonval * $xfactor,
                                'estatus'       => 0
                            ]);
                            //reversa vencimiento
                            if ($det->lotevence > 0) {
                                $ltvc = Vencimiento::find($det->lotevence);
                                if ($ltvc) {
                                    if ($xmovimiento2 == "S") {
                                        $ltvc->salidas = $ltvc->salidas + $det->cantidad;
                                    }
                                    if ($xmovimiento2 == "E") {
                                        $ltvc->entradas = $ltvc->entradas + $det->cantidad;
                                    }
                                    $ltvc->total = $ltvc->entradas - $ltvc->salidas;
                                    $ltvc->save();
                                }
                            }
                        }
                    }
                }
                //
                //confirmamos transacción
                DB::commit();
                $this->emit('msgok', 'El Movimiento ha sido reversado. . .');
            } catch (\Exception $e) {
                //para no tener inconsistencia de la info, deshacemos los cambios en caso de que algo falle
                DB::rollback();
                $status = $e->getMessage();
                dd($e);
            }
        }
    }


    //escuchar eventos y ejecutar acción solicitada
    protected  $listeners = [
        'deleteRow'         => 'destroy',
        'reversaMovimiento' => 'reversaMovimiento',
    ];
}

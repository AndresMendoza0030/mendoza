<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Carbon\Carbon;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportExisteBodega;

use Livewire\WithPagination;
use Livewire\Component;
use App\Warehouse;
use App\Product;
use App\Productstock;
use App\Movdetail;
use App\Productclass;


class WarehousesController extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    //properties
    public  $nombre, $cuenta, $estatus, $cbodega;           //campos de la tabla/modelo
    public  $mesvence, $annovence, $bodegavence;
    public  $selected_id, $search;               //para búsquedas y fila seleccionada
    public  $action = 1;                         //manejo de ventanas
    public  $labodega = 2, $fecharepo;
    private $pagination = 6;                     //paginación de tabla
    private $existe = [], $resexiste = [];

    public function mount()
    {
        $this->mesvence = '';
        $this->annovence = date('Y');
        $this->fecharepo = date('Y-m-d');
    }

    //método que se ejecuta al inciar el componente
    public function render()
    {

        $data = Warehouse::where('nombre', 'like', '%' .  $this->search . '%')
            ->where('estatus', '<>', 1)
            ->paginate($this->pagination);

        if (strlen($this->search) > 0) {

            $this->existe = Productstock::leftjoin('products as p', 'p.id', 'productstocks.producto_id')
                ->leftjoin('productclasses as c', 'c.id', 'p.clase')
                ->select(
                    'producto_id',
                    'existencia',
                    'bodega',
                    DB::raw("CONCAT(TRIM(p.nombre),' ',TRIM(p.presentacion)) as nombreproducto"),
                    DB::raw("TRIM(c.nombre) as nombreclase")
                )
                ->where('bodega', '=', $this->labodega)
                ->where(function ($consulta) {
                    $consulta->where('c.nombre', 'like', '%' . $this->search . '%')
                        ->orwhere('p.nombre', 'like', '%' . $this->search . '%')
                        ->orwhere('producto_id', 'like', '%' . $this->search . '%');
                })
                ->orderBy('nombreclase')->orderBy('nombreproducto')
                ->paginate($this->pagination);
        } else {
            $this->existe = Productstock::leftjoin('products as p', 'p.id', 'productstocks.producto_id')
                ->leftjoin('productclasses as c', 'c.id', 'p.clase')
                ->where('bodega', '=', $this->labodega)
                ->select(
                    'producto_id',
                    'existencia',
                    'bodega',
                    DB::raw("CONCAT(TRIM(p.nombre),' ',TRIM(p.presentacion)) as nombreproducto"),
                    DB::raw("TRIM(c.nombre) as nombreclase")
                )
                ->orderBy('nombreclase')->orderBy('nombreproducto')
                ->paginate($this->pagination);
        }

        return view('livewire.warehouses.component', [
            'info' => $data,
            'existe' => $this->existe,
        ]);
    }

    public function verExistencia($bodega)
    {

        $this->action = 2;
        $this->cbodega = Warehouse::find($bodega)->nombre;
        $this->labodega = $bodega;
    }

    public function asgMesVence($valor)
    {
        $this->mesvence = $valor;
    }

    public function rpExistenciaBodega($bodega)
    {
        $cbodega = Warehouse::find($bodega)->nombre;


        $data = Productstock::leftjoin('products as pr', 'productstocks.producto_id', '=', 'pr.id')
            ->leftjoin('productclasses as cl', 'pr.clase', '=', 'cl.id')
            ->leftjoin('productlines as ln', 'pr.linea', '=', 'ln.id')
            ->where('productstocks.bodega', '=', $bodega)->where('productstocks.existencia', '<>', 0)
            ->select(
                'productstocks.*',
                'pr.costo as costo',
                'cl.nombre as cclase',
                'ln.nombre as clinea',
                DB::raw("CONCAT(TRIM(pr.nombre)) as producto"),
                DB::raw("pr.costo*productstocks.existencia as montot")
            )
            ->orderby('pr.clase')->orderby('pr.linea')->orderby('pr.nombre')
            ->get();

        return Excel::download(new ExportExisteBodega($data, $bodega, $cbodega), 'detalle.xlsx');
    }

    public function rpExisteFecha($bodega) //Exportacion
    {
        $fecha = $this->fecharepo;
        $xbod = Warehouse::find($bodega);
        if ($xbod)
            $cbodega = $xbod->nombre;
        ////////
        //
        if ($fecha == date('Y-m-d')) {
            $data = Productstock::leftjoin('products as pr', 'productstocks.producto_id', '=', 'pr.id')
                ->leftjoin('productclasses as cl', 'pr.clase', '=', 'cl.id')
                ->leftjoin('productlines as ln', 'pr.linea', '=', 'ln.id')
                ->where('productstocks.bodega', '=', $bodega)->where('productstocks.existencia', '<>', 0)
                ->select(
                    'productstocks.*',
                    'pr.costo as costo',
                    'cl.nombre as cclase',
                    'ln.nombre as clinea',
                    DB::raw("CONCAT(TRIM(pr.nombre)) as producto")
                )
                ->orderby('pr.clase')->orderby('pr.linea')->orderby('pr.nombre')
                ->get();
            //
        } else {
            //
            $data = Productstock::leftjoin('products as pr', 'productstocks.producto_id', '=', 'pr.id')
                ->leftjoin('productclasses as cl', 'pr.clase', '=', 'cl.id')
                ->leftjoin('productlines as ln', 'pr.linea', '=', 'ln.id')
                ->where('productstocks.bodega', '=', $bodega)->where('productstocks.existencia', '<>', 0)
                ->select(
                    'productstocks.*',
                    'pr.costo as costo',
                    'cl.nombre as cclase',
                    'ln.nombre as clinea',
                    'pr.costo as costoprod',
                    DB::raw("CONCAT(TRIM(pr.nombre)) as producto")
                )
                ->orderby('pr.clase')->orderby('pr.linea')->orderby('pr.nombre')
                ->get();
            //
            $this->resexiste = collect([]);
            $r = 0;
            //
            foreach ($data as $d) {
                $cp = 0;
                //tomando ultima existenca
                $dp = Movdetail::where('estatus', 0)->where('bodega', $bodega)->where('producto_id', $d->producto_id)
                    ->where('fecha', '<=', $fecha)
                    ->select('bodega', 'producto_id', 'producto', 'cantidad', 'posterior', 'precosto as costodet', 'tipodoc', 'fecha')
                    ->orderby('fecha', 'desc')->orderby('id', 'desc')->first();

                //
                if ($dp) {
                    if ($dp->tipodoc == "CPRA") {
                        $dpe = Movdetail::where('estatus', 0)->where('bodega', $bodega)->where('producto_id', $d->producto_id)
                            ->where('fecha', '<=', $fecha)->where('tipodoc', '!=', 'CPRA')
                            ->select('bodega', 'producto_id', 'producto', 'cantidad', 'posterior', 'precosto as costodet', 'tipodoc', 'fecha')
                            ->orderby('fecha', 'desc')->orderby('id', 'desc')->first();
                        //
                        if ($dpe) {
                            $cp = $dpe->costodet;
                        }
                        if ($cp == 0 || $cp == null) {
                            $cp = $d->costoprod;
                        }
                    } else {
                        $cp = $d->costo;
                        //
                        if ($cp == 0 || $cp == null) {
                            $cp = $d->costoprod;
                        }
                    }
                    $ct = $cp;
                    $mt = round($ct * $dp->posterior, 4);
                    //

                    $this->resexiste->push([
                        "rg" => $r,
                        "producto_id" => $d->producto_id,
                        "producto" => $d->producto,
                        "idclase" => $d->clase,
                        "idlinea" => $d->linea,
                        "cclase" => $d->cclase,
                        "clinea" => $d->clinea,
                        "costoprod" => $d->costo,
                        "existencia" => $dp->posterior,
                        "costodet" => $dp->costodet,
                        "costo" => $ct,
                        "montot" => $mt
                    ]);
                    $r += 1;
                }
            }
            $data = ($this->resexiste);
        }



        return Excel::download(new ExportExisteBodega($data, $bodega, $cbodega, $fecha), 'detalle.xlsx');
        //

    }


    //permite la búsqueda cuando se navega entre el paginado
    public function updatingSearch(): void
    {
        $this->gotoPage(1);
    }

    //activa la vista edición o creación
    public function doAction($action)
    {
        $this->resetInput();
        $this->action = $action;
    }

    //método para reiniciar variables
    private function resetInput()
    {
        $this->nombre  = '';
        $this->estatus = 0;
        $this->cuenta  = '';
        $this->selected_id = null;
        $this->action  = 1;
        $this->search  = '';
    }

    //buscamos el registro seleccionado y asignamos la info a las propiedades
    public function edit($id)
    {
        $record = Warehouse::find($id);
        $this->selected_id = $id;
        $this->nombre = $record->nombre;
        $this->cuenta = $record->cuenta;
        $this->estatus = $record->estatus;
        $this->action = 2;
    }


    //método para registrar y/o actualizar registros
    public function CreateOrUpdate()
    {
        $this->validate([
            'nombre' => 'required',
        ]);

        if ($this->selected_id > 0) {
            $existe = Warehouse::where('nombre', $this->nombre)
                ->where('id', '<>', $this->selected_id)
                ->select('nombre')->get()
                ->count();
        } else {
            $existe = Warehouse::where('nombre', $this->nombre)
                ->select('nombre')->get()
                ->count();
        }

        if ($existe > 0) {
            $this->emit('msg-error', 'Ya existe la bodega');
            $this->resetInput();
            return;
        }

        if ($this->selected_id <= 0) {
            $class =  Warehouse::create([
                'nombre' => $this->nombre,
                'estatus' => 0,
            ]);
        } else {
            $class = Warehouse::find($this->selected_id);
            $class->update([
                'nombre' => $this->nombre,
                'cuenta'  => $this->cuenta,
            ]);
        }

        if ($this->selected_id)
            $this->emit('msg-ok', 'Bodega Actualizada');
        else
            $this->emit('msg-ok', 'Bodega Creada');

        $this->resetInput();
    }

    public function rpVencimientos($mesvence, $labodegavence)
    {
        $this->bodegavence = $labodegavence;
        $this->mesvence = $mesvence;
        // Si no se ha asignado un año, usamos el año actual
        if (empty($this->annovence)) {
            $this->annovence = date('Y');
        }
        $this->emit('print-rpvence', [
            'bodega' => $this->bodegavence,
            'mes'    => $this->mesvence,
            'anno'   => $this->annovence
        ]);
    }



    //escuchar eventos y ejecutar acción solicitada
    protected  $listeners = [
        'deleteRow'     => 'destroy',
        'createModal'   => 'createModal',
        'doAction'      => 'doAction',
        'rpVencimientos' => 'rpVencimientos',
        'rpExisteFecha' => 'rpExisteFecha'
    ];

    //metodo create from modal

    public function createModal($info)
    {
        $data = json_decode($info);

        $this->selected_id = $data->id;
        $this->nombre = $data->nombre;
        $this->cuenta = $data->cuenta;

        $this->CreateOrUpdate();
    }

    //método para eliminar un registro dado
    public function destroy($id)
    {
        if ($id) {
            $record = Warehouse::where('id', $id);
            /* $record->delete();*/
            $record->update([
                'estatus' => 1,
            ]);

            $this->resetInput();
            $this->emit('msgok', "Bodega ha sido desactivada del sistema");
        }
    }
}

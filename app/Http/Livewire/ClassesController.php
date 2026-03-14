<?php

namespace App\Http\Livewire;

use App\Exports\ExportExisteClase;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Livewire\WithPagination;
use Livewire\Component;
use App\Productclass;
use App\Productstock;

class ClassesController extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    //properties
    public  $nombre, $cuenta, $estatus;           //campos de la tabla/modelo
    public  $selected_id, $search;               //para búsquedas y fila seleccionada
    public  $action = 1;                         //manejo de ventanas
    private $pagination = 10;                     //paginación de tabla

    public function mount() {}

    //método que se ejecuta al inciar el componente
    public function render()
    {

        if (strlen($this->search) > 0) {
            $classes = Productclass::where('nombre', 'like', '%' .  $this->search . '%')
                ->where('estatus', '<>', 1)
                ->paginate($this->pagination);

            return view('livewire.classes.component', [
                'info' => $classes,
            ]);
        } else {
            $classes = Productclass::where('estatus', '<>', 1)
                ->select('id', 'nombre', 'cuenta', 'estatus')
                ->orderBy('id')
                ->paginate($this->pagination);


            return view('livewire.classes.component', [
                'info' => $classes,
            ]);
        }
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
        $this->nombre = '';
        $this->cuenta = '';
        $this->estatus = 0;
        $this->selected_id = null;
        $this->action = 1;
        $this->search = '';
    }

    //buscamos el registro seleccionado y asignamos la info a las propiedades
    public function edit($id)
    {
        $record = Productclass::find($id);
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
            $existe = Productclass::where('nombre', $this->nombre)
                ->where('id', '<>', $this->selected_id)
                ->select('nombre')->get()
                ->count();
        } else {
            $existe = Productclass::where('nombre', $this->nombre)
                ->select('nombre')->get()
                ->count();
        }

        if ($existe > 0) {
            $this->emit('msg-error', 'Ya existe la clase');
            //session()->flash('msg-error', 'Ya existe la clase');
            $this->resetInput();
            return;
        }

        if ($this->selected_id <= 0) {
            $class =  Productclass::create([
                'nombre' => $this->nombre,
                'estatus' => 0,
                'cuenta' => $this->cuenta,
            ]);
        } else {
            $class = Productclass::find($this->selected_id);
            $class->update([
                'nombre' => $this->nombre,
                'cuenta' => $this->cuenta,
            ]);
        }

        if ($this->selected_id)
            //session()->flash('message', 'Clase Actualizada');
            $this->emit('msg-ok', 'Clase Actualizada');
        else
            $this->emit('msg-ok', 'Clase Creada');
        //session()->flash('message', 'Clase Creada');

        $this->resetInput();
    }


    //escuchar eventos y ejecutar acción solicitada
    protected  $listeners = [
        'deleteRow'     => 'destroy',
        'createModal' => 'createModal',
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
            $record = Productclass::where('id', $id);
            /* $record->delete();*/
            $record->update([
                'estatus' => 1,
            ]);

            $this->resetInput();
            $this->emit('msgok', "Clase ha sido desactivada del sistema");
        }
    }


    public function rpExistenciaClase($clase)
    {
        $cclase = Productclass::find($clase)->nombre;


        $data = Productstock::leftjoin('products as pr', 'productstocks.producto_id', '=', 'pr.id')
            ->leftjoin('productlines as ln', 'pr.linea', '=', 'ln.id')
            ->where('pr.clase', '=', $clase)->where('productstocks.existencia', '<>', 0)
            ->select(
                'productstocks.*',
                'pr.costo as costo',
                'ln.nombre as clinea',
                DB::raw("CONCAT(TRIM(pr.nombre)) as producto"),
                'pr.precioventa as precioventa',
                'pr.registrosanitario as registro',
                'pr.barra as barra',
                'pr.manejopor as manejopor',
                DB::raw("SUM(productstocks.existencia) as existenciat")
            )->groupby('productstocks.producto_id')
            ->orderby('pr.linea')->orderby('pr.nombre')
            ->get();
        return Excel::download(new ExportExisteClase($data, $clase, $cclase), 'detalle.xlsx');
    }
}

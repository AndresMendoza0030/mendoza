<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Carbon\Carbon;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportExisteBodega;

use Livewire\WithPagination;
use Livewire\Component;
use App\Destino;


class DestinosController extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    
	//properties
	public  $nombre,$cuenta,$estatus,$search;           //campos de la tabla/modelo
    public  $action = 1;             			//manejo de ventanas
    private $pagination = 6;         			//paginación de tabla

    
    public function mount()
    {
    }

    //método que se ejecuta al inciar el componente
    public function render()
    {

        $data = Destino::where('nombre', 'like', '%' .  $this->search . '%')
        ->where('estatus','<>',1)
        ->paginate($this->pagination);

                
        return view('livewire.destinos.component', [
        'info' => $data, ]);
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
        $this->cuenta  ='';
        $this->selected_id = null;       
        $this->action  = 1;
        $this->search  = '';
    }

    //buscamos el registro seleccionado y asignamos la info a las propiedades
    public function edit($id)
    {
        $record = Destino::find($id);
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
        'nombre' => 'required',]);
                                   
        if($this->selected_id > 0)
        {
            $existe = Destino::where('nombre', $this->nombre)
            ->where('id', '<>', $this->selected_id)
            ->select('nombre')->get()
            ->count();
        }
        else 
        {
            $existe = Destino::where('nombre', $this->nombre)
            ->select('nombre')->get()
            ->count();
        }    

        if( $existe > 0)
        {
            $this->emit('msg-error', 'Ya existe el Destino');
            $this->resetInput();
            return;
        }

        if($this->selected_id <= 0)
        {        
            $class =  Destino::create([
            'nombre' => $this->nombre, 
            'estatus'=> 0,           
            ]);
        }
        else 
        {
            $class = Destino::find($this->selected_id);
            $class->update([
            'nombre' => $this->nombre, 
            'cuenta'  => $this->cuenta,
            ]);                    
        }

        if($this->selected_id) 
            $this->emit('msg-ok', 'Destino Actualizado');
        else
            $this->emit('msg-ok', 'Destino Creado');

        $this->resetInput();
    }


    //escuchar eventos y ejecutar acción solicitada
    protected  $listeners = [
        'deleteRow'     => 'destroy' ,   
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
        $this->cuenta= $data->cuenta;
 
        $this->CreateOrUpdate();
    }

   //método para eliminar un registro dado
    public function destroy($id)
    {
        if ($id)
        {
            $record = Destino::where('id', $id);
            /* $record->delete();*/
            $record->update([
                'estatus' => 1, ]);                    
    
            $this->resetInput();
            $this->emit('msgok', "Bodega ha sido desactivada del sistema");    
        }
    }

}

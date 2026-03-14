<?php

namespace App\Http\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use App\Productclass;
use App\Productline;


class LinesController extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    
	//properties
	public  $nombre,$cuenta,$estatus,$clase_id;           //campos de la tabla/modelo
	public  $selected_id, $search;   			//para búsquedas y fila seleccionada
    public  $action = 1;             			//manejo de ventanas
    private $pagination = 4;         			//paginación de tabla
    public  $clases,$clase ='Seleccionar';
    
    public function mount()
    {
    }

    //método que se ejecuta al inciar el componente
    public function render()
    {

        $this->clases = Productclass::all();

        if(strlen($this->search) > 0)
        {
            $lines = Productline::leftjoin('productclasses as cl', 'cl.id', 'productlines.clase_id')
            ->select('productlines.*', 'cl.nombre as clase')
            ->where('productlines.nombre', 'like', '%' .  $this->search . '%')
            ->where('productlines.estatus','<>',1)
            ->paginate($this->pagination);

            return view('livewire.lines.component', [
                'info' => $lines,
            ]);
        }
        else
        {
            $lines = Productline::leftjoin('productclasses as cl', 'cl.id', 'productlines.clase_id')
            ->select('productlines.*', 'cl.nombre as clase')
            ->where('productlines.estatus','<>',1)
            ->orderBy('productlines.id', 'desc')
            ->orderBy('cl.nombre')
            ->paginate($this->pagination);
       
            return view('livewire.lines.component', [
            'info' => $lines, ]);
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
        $this->clase_id= 0;
        $this->clase = 'Seleccionar';
        $this->estatus = 0;
        $this->selected_id = null;       
        $this->action = 1;
        $this->search = '';
    }

    
    //buscamos el registro seleccionado y asignamos la info a las propiedades
    public function edit($id)
    {
        $record = Productline::find($id);
        $this->selected_id = $id;
        $this->nombre = $record->nombre;
        $this->cuenta = $record->cuenta;
        $this->estatus = $record->estatus;
        $this->action = 2;
        $this->clase = $record->clase->id;

    }


    //método para registrar y/o actualizar registros
    public function CreateOrUpdate()
    {         	
        $this->validate([
        'nombre' => 'required',
        'clase' =>'required',
        'clase' => 'not_in:Seleccionar',
        ]);

        if($this->selected_id > 0)
        {
            $existe = Productline::where('nombre', $this->nombre)
            ->where('id', '<>', $this->selected_id)
            ->where('clase_id', $this->clase)
            ->select('nombre')->get()
            ->count();
        }
        else 
        {
            $existe = Productline::where('nombre', $this->nombre)
            ->where('clase_id', $this->clase)
            ->select('nombre')->get()
            ->count();
        }    

        if( $existe > 0)
        {
            $this->emit('msg-error', 'Ya existe el nombre de línea');
            $this->resetInput();
            return;
        }

        if($this->selected_id <= 0)
        {        
            $linea =  Productline::create([
            'nombre' => $this->nombre, 
            'clase_id'=> $this->clase, 
            'estatus'=> 0,           
            'cuenta' => $this->cuenta,]);
        }
        else 
        {
            $class = Productline::find($this->selected_id);
            $class->update([
            'nombre' => $this->nombre,            
            'clase_id'=> $this->clase, 
            'cuenta' => $this->cuenta, ]);                    
        }

        if($this->selected_id) 
            $this->emit('msg-ok', 'Línea Actualizada');
        else
            $this->emit('msg-ok', 'Línea Creada');

        $this->resetInput();
    }


    //escuchar eventos y ejecutar acción solicitada
    protected  $listeners = [
        'deleteRow'     => 'destroy' ,   
        'createModal' => 'createModal',
    ];  

    //metodo create from modal

    public function createModal($info)
    {
        $data = json_decode($info);

        $this->selected_id = $data->id;
        $this->clase  =$data->clase;
        $this->nombre = $data->nombre;
        $this->cuenta = $data->cuenta;

        $this->CreateOrUpdate();
    }

   //método para eliminar un registro dado
    public function destroy($id)
    {
        if ($id)
        {
            $record = Productline::where('id', $id);
            /* $record->delete();*/
            $record->update([
                'estatus' => 1, ]);                    
    
            $this->resetInput();
            $this->emit('msgok', "Línea ha sido desactivada del sistema");    
        }
    }

}

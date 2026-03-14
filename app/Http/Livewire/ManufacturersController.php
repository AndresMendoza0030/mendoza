<?php

namespace App\Http\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use App\Manufacturer;

class ManufacturersController extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    
	//properties
	public  $nombre,$cuenta,$estatus;           //campos de la tabla/modelo
	public  $selected_id, $search;   			//para búsquedas y fila seleccionada
    public  $action = 1;             			//manejo de ventanas
    private $pagination = 4;         			//paginación de tabla
    
    public function mount()
    {
    }

    //método que se ejecuta al inciar el componente
    public function render()
    {

        if(strlen($this->search) > 0)
        {
            $data = Manufacturer::where('nombre', 'like', '%' .  $this->search . '%')
            ->where('estatus','<>',1)
            ->paginate($this->pagination);

            return view('livewire.manufacturers.component', [
                'info' => $data,
            ]);
        }
        else
        {
            $data = Manufacturer::where('estatus','<>',1)
            ->select('id','nombre','estatus')
            ->orderBy('id')
            ->paginate($this->pagination);

          
            return view('livewire.manufacturers.component', [
            'info' => $data, ]);
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
        $this->estatus = 0;
        $this->selected_id = null;       
        $this->action = 1;
        $this->search = '';
    }

    //buscamos el registro seleccionado y asignamos la info a las propiedades
    public function edit($id)
    {
        $record = Manufacturer::find($id);
        $this->selected_id = $id;
        $this->nombre = $record->nombre;
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
            $existe = Manufacturer::where('nombre', $this->nombre)
            ->where('id', '<>', $this->selected_id)
            ->select('nombre')->get()
            ->count();
        }
        else 
        {
            $existe = Manufacturer::where('nombre', $this->nombre)
            ->select('nombre')->get()
            ->count();
        }    

        if( $existe > 0)
        {
            $this->emit('msg-error', 'Ya existe el fabricante');
            //session()->flash('msg-error', 'Ya existe la clase');
            $this->resetInput();
            return;
        }

        if($this->selected_id <= 0)
        {        
            $class =  Manufacturer::create([
            'nombre' => $this->nombre, 
            'estatus'=> 0,           
            ]);
        }
        else 
        {
            $class = Manufacturer::find($this->selected_id);
            $class->update([
            'nombre' => $this->nombre,            
            ]);                    
        }

        if($this->selected_id) 
            //session()->flash('message', 'Clase Actualizada');
            $this->emit('msg-ok', 'Fabricante Actualizado');
        else
            $this->emit('msg-ok', 'Fabricante Creado');
        //session()->flash('message', 'Clase Creada');

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
        $this->nombre = $data->nombre;
 
        $this->CreateOrUpdate();
    }

   //método para eliminar un registro dado
    public function destroy($id)
    {
        if ($id)
        {
            $record = Manufacturer::where('id', $id);
            /* $record->delete();*/
            $record->update([
                'estatus' => 1, ]);                    
    
            $this->resetInput();
            $this->emit('msgok', "Fabricante ha sido desactivado del sistema");    
        }
    }

}

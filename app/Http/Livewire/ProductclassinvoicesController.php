<?php

namespace App\Http\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use App\Productclassinvoice;


class ProductclassinvoicesController extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    
	//properties
	public  $nombre,$cuenta,$estatus;           //campos de la tabla/modelo
	public  $selected_id, $search;   			//para búsquedas y fila seleccionada
    public  $action = 1;             			//manejo de ventanas
    private $pagination = 4;         			//paginación de tabla
    public  $admon ='Seleccionar';
    
    public function mount()
    {
    }

    //método que se ejecuta al inciar el componente
    public function render()
    {

        $this->clases = Productclassinvoice::all();

        if(strlen($this->search) > 0)
        {
            $lines = Productclassinvoice::where('nombre', 'like', '%' .  $this->search . '%')
            ->where('estatus','<>',1)
            ->paginate($this->pagination);

            return view('livewire.productclassinvoices.component', [
                'info' => $lines,
            ]);
        }
        else
        {
            $lines = Productclassinvoice::where('estatus','<>',1)
            ->select('id','nombre','cuenta','estatus','admon')
            ->orderBy('id')
            ->paginate($this->pagination);
       
            return view('livewire.productclassinvoices.component', [
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
        $this->admon  = 'Seleccionar';
        $this->estatus = 0;
        $this->selected_id = null;       
        $this->action = 1;
        $this->search = '';
    }

    //buscamos el registro seleccionado y asignamos la info a las propiedades
    public function edit($id)
    {
        $record = Productclassinvoice::find($id);
        $this->selected_id = $id;
        $this->nombre = $record->nombre;
        $this->cuenta = $record->cuenta;
        $this->estatus = $record->estatus;
        $this->action = 2;
        $this->admon = $record->admon;

    }


    //método para registrar y/o actualizar registros
    public function CreateOrUpdate()
    {         	
        $this->validate([
        'nombre' => 'required',
        'admon' =>'required',
        'admon' => 'not_in:Seleccionar',
        ]);

        if($this->selected_id > 0)
        {
            $existe = Productclassinvoice::where('nombre', $this->nombre)
            ->where('id', '<>', $this->selected_id)
            ->select('nombre','admon','estatus')->get()
            ->count();
        }
        else 
        {
            $existe = Productclassinvoice::where('nombre', $this->nombre)
            ->select('nombre')->get()
            ->count();
        }    

        if( $existe > 0)
        {
            $this->emit('msg-error', 'Ya existe el nombre de Clase de Facturación');
            $this->resetInput();
            return;
        }

        if($this->selected_id <= 0)
        {        
            $linea =  Productclassinvoice::create([
            'nombre' => $this->nombre, 
            'admon'=> $this->admon, 
            'estatus'=> 0,           
            'cuenta' => $this->cuenta,]);
        }
        else 
        {
            $class = Productclassinvoice::find($this->selected_id);
            $class->update([
            'nombre' => $this->nombre,            
            'admon'=> $this->admon, 
            'cuenta' => $this->cuenta, ]);                    
        }

        if($this->selected_id) 
            $this->emit('msg-ok', 'Clase de Facturación Actualizada');
        else
            $this->emit('msg-ok', 'Clase de Facturación Creada');

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
        $this->admon  =$data->admon;
        $this->nombre = $data->nombre;
        $this->cuenta = $data->cuenta;

        $this->CreateOrUpdate();
    }

   //método para eliminar un registro dado
    public function destroy($id)
    {
        if ($id)
        {
            $record = Productclassinvoice::where('id', $id);
            /* $record->delete();*/
            $record->update([
                'estatus' => 1, ]);                    
    
            $this->resetInput();
            $this->emit('msgok', "Clase de facturación desactivada del sistema");    
        }
    }

}

<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Livewire\WithPagination;
use Livewire\Component;
use App\Ciudad;
use App\Clienteproveedor;

class ClienteController extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    //properties
    public  $razon,$nit,$nrc,$dui,$giro,$email,$direccion,$telefono,$idreg,$cuentacontable,$credito='NO';
    public  $contacto;
    public  $tipo='CLIENTE';

    public  $selected_id=0;
    public  $search='';   
    public  $action = 1; 
    private $pagination = 5;
    private $clientes;
   
    public function mount()
    {
        $this->idreg=0;
    }


    //método que se ejecuta al inciar el componente
    public function render()
    {
      

        if(strlen($this->search) > 0)
        {
            $clientes = Clienteproveedor::select('cliente_proveedores.*')
            ->where('tipo','CLIENTE')
            ->where('cliente_proveedores.razon', 'like', '%' .  $this->search . '%')
            ->orwhere('cliente_proveedores.nit', 'like', '%' .  $this->search . '%')            
            ->orwhere('cliente_proveedores.nrc', 'like', '%' .  $this->search . '%')
            ->orwhere('cliente_proveedores.dui', 'like', '%' .  $this->search . '%')
            ->orderby('cliente_proveedores.id')->paginate($this->pagination);
            $this->clientes = $clientes;
    
            return view('livewire.clientes.component', [
                'info' => $clientes]);
        }
        else
        {
            $clientes = Clienteproveedor::select('cliente_proveedores.*')
            ->where('tipo','CLIENTE')           
            ->orderby('cliente_proveedores.id')->paginate($this->pagination);
            $this->clientes = $clientes;

            return view('livewire.clientes.component', [
                'info' => $clientes]);
        }
    }


    //buscamos el registro seleccionado y asignamos la info a las propiedades
    public function edit($id)
    {
    $record = Clienteproveedor::find($id);
    
    $this->selected_id = $id;
    $this->action = 2;

    $this->razon        =$record->razon;
    $this->nit          =$record->nit;
    $this->nrc          =$record->nrc;
    $this->dui          =$record->dui;
    $this->giro         =$record->giro;
    $this->email        =$record->email;
    $this->direccion    =$record->direccion;
    $this->telefono     =$record->telefono;
    $this->contacto     =$record->contacto;
    $this->tipo         =$record->tipo;
    $this->credito      =$record->credito;
    $this->cuentacontable = $record->cuentacontable;
    $this->idreg        =$record->id;
    }

    public function updatingSearch()
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
        $this->razon         ='';
        $this->nit           ='';
        $this->cuentacontable ='';
        $this->nrc           ='';
        $this->dui          ='';
        $this->giro         ='';
        $this->email        ='';
        $this->direccion     ='';
        $this->telefono     ='';
        $this->credito ='NO';
        $this->contacto      ='';
        $this->idreg=0;
        $this->selected_id = null;       
        $this->action = 1;
        $this->search = '';
    }

    //escuchar eventos y ejecutar acción solicitada
    protected  $listeners = [
        'deleteRow'     => 'destroy' ,   
        'cambiaAccion' => 'cambiaAccion',
        'grabarDatos' => 'grabarDatos',
        'edit'          => 'edit',
    ];  

    //cambiando accion desde form
    public function cambiaAccion($miaccion)
    {
        $this->action = $miaccion;
    }

    //método para registrar y/o actualizar registros
    public function grabarDatos()
    {

        if($this->idreg == 0)
          {        
           //iniciamos transacción
           DB::beginTransaction();
            try
            { 
            $cont=0;
                $paciente = Clienteproveedor::create([
                'razon'           => $this->razon,
                'cuentacontable'  => $this->cuentacontable,
                'nit'             => $this->nit,
                'dui'              => $this->dui,
                'nrc'               => $this->nrc,
                'giro'               => $this->giro,
                'email'                => $this->email,
                'direccion'            => $this->direccion,
                'telefono'             => $this->telefono,
                'contacto'             => $this->contacto,
                'credito'               =>$this->credito,
                'tipo'                 => 'CLIENTE',
                'estatus'              => 0
                ]);
                //
            $this->action = 1;
            $this->resetInput();
            //confirmamos transacción
            DB::commit();
            $this->emit('msgok', 'Información del Cliente ha sido guardada. . .');    
            }
             catch (\Exception $e)
            {
            //para no tener inconsistencia de la info, deshacemos los cambios en caso de que algo falle
            DB::rollback();
            $status = $e->getMessage();       
            dd($e);     
            }
        }
        else
        {        
            //iniciamos transacción
            
            DB::beginTransaction();
             try
             { 
               
                $paciente = Clienteproveedor::find($this->idreg);
                if ($paciente)
                {
                    $paciente->update([
                        'razon'           => $this->razon,
                        'cuentacontable'  => $this->cuentacontable,
                        'nit'             => $this->nit,
                        'dui'              => $this->dui,
                        'nrc'               => $this->nrc,
                        'giro'               => $this->giro,
                        'email'                => $this->email,
                        'direccion'            => $this->direccion,
                        'telefono'             => $this->telefono,
                        'credito'               => $this->credito,
                        'contacto'             => $this->contacto,
                         ]);
                    //
                    $this->action = 1;
                    $this->resetInput();
                    //confirmamos transacción
                    DB::commit();
                    $this->emit('msgok', 'Información del Cliente ha sido actualizada. . .');    
                }
             }
              catch (\Exception $e)
             {
             //para no tener inconsistencia de la info, deshacemos los cambios en caso de que algo falle
             DB::rollback();
             $status = $e->getMessage();       
             dd($e);     
             }
        }
 
    }

   //método para eliminar un registro dado
    public function destroy($id)
    {
        if ($id)
        {

            $record = Clienteproveedor::find($id);
            /* $record->delete();*/
            if ($record)
            {
                $record->estatus = 1;
                $record->save();
                $this->resetInput();
                $this->emit('msgok', "Registro ha sido desactivado del sistema");    
    
            }
        }
    }

}

<?php

namespace App\Http\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\user;


class ClaveController extends Component
{
	//properties
	public  $usuario,$nombre,$actual;           //campos de la tabla/modelo
	public  $nueva,  $confirma, $search;   			//para búsquedas y fila seleccionada
    
    public function mount()
    {
    }

    //método que se ejecuta al inciar el componente
    public function render()
    {
            return view('livewire.clave.component');
    }


   	//método para reiniciar variables
    private function resetInput()
    {
        $actual = '';
        $this->nueva = '';
        $this->confirma = '';
    }

    //método para registrar y/o actualizar registros
    public function salvaNuevaClave()
    {         	
        $usuario = auth()->user();
        $email   = auth()->user()->email;
        $actual  =$this->actual;
        $check   = auth()->validate([
            'email'    => $email,
            'password' => $actual
        ]);
    
        if ($check)
        {
            if (TRIM($this->nueva) != TRIM($this->confirma))
            {
                $this->emit('msg-error', "La confirmación de la nueva contraseña es inválida. . . ");    
            }
            else
            {
                $usuario->password = bcrypt($this->nueva);
                $usuario->save();
                $this->emit('msgok', "Combio de clave Confirmado!!!... ");                
                return redirect('home');
            }
        }
        return;
    }


   //método para eliminar un registro dado
    public function destroy($id)
    {
        if ($id)
        {
            $record = Productclass::where('id', $id);
            /* $record->delete();*/
            $record->update([
                'estatus' => 1, ]);                    
    
            $this->resetInput();
            $this->emit('msgok', "Clase ha sido desactivada del sistema");    
        }
    }

}

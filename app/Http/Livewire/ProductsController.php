<?php

namespace App\Http\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use App\Manufacturer;
use App\Productclass;
use App\Productline;
use App\Product;
use App\Warehouse;
use App\Movdetail;
use App\Productstock;
use App\Productclassinvoice;


class ProductsController extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    
	//properties
    public  $preciosugerido,$nombre,$presentacion,$manejo,$existe,$totalexiste,$elproducto,$mibodega;
    public  $desde,$hasta,$idproducto,$descuento;
    public  $tipo='PRODUCTO';
    public  $clase='Elegir';           //campos de la tabla/modelo
    public  $linea='Elegir';
    public  $lineafac='Elegir';
    public  $fabricante='Elegir';
    public  $manejopor='PRESENTACION';           //campos de la tabla/modelo
    public  $costo,$ultimocosto,$precioventa,$barra,$cambioprecio,$registrosanitario;           //campos de la tabla/modelo
    public  $ultimacompra,$ultimaventa,$bodegas;           //campos de la <tabla>
    public  $movimi=[];

    public  $selected_id, $search;   			//para búsquedas y fila seleccionada
    public  $action = 1;             			//manejo de ventanas
    private $pagination = 10;         			//paginación de tabla
    public  $clases, $lineas, $lineasfac, $fabricantes;


    public function mount()
    {
        $this->desde = date("Y-m-d");
        $this->hasta = date("Y-m-d");
        $mibodega= 1;
    }

    //método que se ejecuta al inciar el componente
    public function render()
    {

        $this->clases       = Productclass::where('estatus','=','0')->get();
        $this->bodegas      = Warehouse::where('estatus','=','0')->get();
        $this->lineasfac    = Productclassinvoice::where('estatus','=','0')->get();
        $this->lineas       = Productline::select('id','clase_id','nombre','cuenta')
        ->where('estatus','=','0')->get();
        $this->fabricantes  = Manufacturer::where('estatus','=','0')->get();



        if(strlen($this->search) > 0)
        {
            $data = Product::where('products.estatus','=',0)
            ->leftjoin('productclasses', 'clase', '=', 'productclasses.id')
            ->leftjoin('productlines',   'linea', '=', 'productlines.id')
            ->select('products.*', 'productclasses.nombre as laclase',
            'productlines.nombre as lalinea')
            ->where('products.nombre', 'like', '%' .  $this->search . '%')
            ->orwhere('productclasses.nombre', 'like', '%' .  $this->search . '%')
            ->orwhere('productlines.nombre', 'like', '%' .  $this->search . '%')
            ->orwhere('products.barra', '=' , TRIM($this->search))
            ->orderby('products.clase')->orderby('products.linea')->orderby('products.nombre')
            ->paginate($this->pagination);

            return view('livewire.products.component', [
                'info' => $data,
            ]);
        }
        else
        {
            $data = Product::where('products.estatus','=',0)
            ->leftjoin('productclasses', 'clase', '=', 'productclasses.id')
            ->leftjoin('productlines',   'linea', '=', 'productlines.id')
            ->select('products.*', 'productclasses.nombre as laclase',
            'productlines.nombre as lalinea')
            ->orderby('products.clase')->orderby('products.linea')->orderby('products.nombre')
            ->paginate($this->pagination);
       
            return view('livewire.products.component', [
            'info' => $data, ]);
        }
    }

    // Actualizar tupla de lineas en base a valor de clase
    public function getLineas()
    {
        $lineas = Productline::where('productlines.clase_id','=',$this->clase)->get();
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

    public function verExistencia($elid)
    {
        $this->action = 3;
        $this->idproducto=$elid;
        $this->elproducto=Product::find($elid);
        $this->elproducto = $this->elproducto->nombre." ".$this->elproducto->presentacion;
        $existe = Productstock::where('producto_id',$elid)
        ->where('existencia','!=',0)->leftjoin('warehouses','warehouses.id','=','productstocks.bodega')
        ->leftjoin('products','products.id','=','productstocks.producto_id')
        ->select('productstocks.*', 'warehouses.nombre as labodega')->orderby('bodega')->get();
        if ($existe)
        {
            $this->existe=$existe;
            $this->totalexiste=$existe->sum('existencia');
            if ($this->totalexiste<=0)
            {
                $this->emit('msg-error', 'No Hay Existencias que Mostrar....');
                $this->action=1;
            }
            else
            {
                $this->emit('mostrarExistencia');
            }
        }
    }


    public function mostrarMovimiento()
    {
        $tipo=Product::find($this->idproducto);
        $tipo=$tipo->tipo;
        if ($tipo=="PRODUCTO")
        {
            $movimi = Movdetail::where('producto_id',$this->idproducto)
            ->where('movdetails.bodega',$this->mibodega)
            ->whereDate('movdetails.fecha','>=',$this->desde)->whereDate('movdetails.fecha','<=',$this->hasta)
            ->leftjoin('warehouses as wr','wr.id','=','movdetails.bodega')
            ->leftjoin('users as us','us.id','movdetails.user_id')
            ->leftjoin('inventoryhead as ih', 'ih.id','=','movdetails.numerodoc')
            ->select('movdetails.*','wr.nombre as cbodega','us.name as nusuario','ih.concepto as concepto')
            ->orderby('movdetails.id')->get();
        }
        else
        {
            $movimi = Movdetail::where('producto_id',$this->idproducto)
            ->whereBetween('fecha',[$this->desde,$this->hasta])
            ->leftjoin('users as us','us.id','movdetails.user_id')
            ->select('movdetails.*','us.name as nusuario')->orderby('movdetails.id')->get();
        }


        if ($movimi)
        {
            $this->movimi=$movimi;
        }
        else
        {
            $this->emit('msg-error', 'No Hay Movimientos que Mostrar....');
            $this->action=1;
        }


    }

    public function verMovimientoProd($elid)
    {
        $this->idproducto=$elid;

        $this->elproducto=Product::find($elid);
        $this->elproducto = $this->elproducto->nombre." ".$this->elproducto->presentacion;
        $this->action=4; 
    }

    public function regresaMaster()
    {
        $this->action=1;
        $this->movimi=[];
    }

    //método para reiniciar variables
    private function resetInput()
    {
        $this->nombre = '';
        $this->presentacion = '';
        $this->tipo='PRODUCTO';
        $this->clase='Elegir';          
        $this->linea='Elegir';
        $this->lineafac='Elegir';
        $this->fabricante='Elegir';
        $this->manejopor='PRESENTACION';       
        $this->costo=0;
        $this->descuento='';
        $this->ultimocosto=0;
        $this->precioventa= '';
        $this->barra='';           
        $this->ultimacompra = null;
        $this->preciosugerido=0;
        $this->ultimaventa = null;           
        $this->estatus = 0;
        $this->cambioprecio='NO';
        $this->registrosanitario='';

        $this->selected_id = null;       
        $this->action = 1;
    }

    //buscamos el registro seleccionado y asignamos la info a las propiedades
    public function edit($id)
    {
        $this->resetInput();
        $record = Product::find($id);
        $this->selected_id = $id;
        $this->action = 2;

        $this->nombre           = $record->nombre;
        $this->presentacion     = $record->presentacion;
        $this->manejopor        = $record->manejopor;       
        $this->clase            = $record->clase;          
        $this->linea            = $record->linea;
        $this->fabricante       = $record->fabricante;

        $this->registrosanitario = $record->registrosanitario;
      
        $this->costo            = $record->costo;
        $this->ultimocosto      = $record->ultimocosto;
        $this->barra            = $record->barra;           
        $this->ultimacompra     = $record->ultimacompra;
        $this->estatus          = $record->estatus;
       
    }

    //método para registrar y/o actualizar registros
    public function CreateOrUpdate()
    {         	
        $this->validate([
        'nombre' => 'required',
        'clase' =>'required',
        'clase' => 'not_in:Elegir',
        ]);

        if ($this->costo == null || $this->costo == 0)
        { $this->costo = 0;  }

        if($this->selected_id > 0)
        {
            $existe = Product::where('nombre', $this->nombre)
            ->where('presentacion', $this->presentacion)
            ->where('id', '<>', $this->selected_id)
            ->select('nombre')->get()
            ->count();
        }
        else 
        {
            $existe = Product::where('nombre', $this->nombre)
            ->where('presentacion', $this->presentacion)
            ->select('nombre')->get()
            ->count();
        }    

        if( $existe > 0)
        {
            $this->emit('msg-error', 'Ya existe un registro con esa descripción');
            $this->resetInput();
            return;
        }

        if($this->selected_id <= 0)
        {        
            $linea =  Product::create([
            'nombre'        =>$this->nombre,
            'presentacion'  =>$this->presentacion,
            'tipo'          =>$this->tipo,
            'clase'         =>$this->clase,
            'linea'         =>$this->linea,
            'lineafac'      =>$this->lineafac,
            'fabricante'    =>$this->fabricante,
            'manejopor'     =>$this->manejopor,       
            'costo'         =>$this->costo,
            'ultimocosto'   =>$this->ultimocosto,
            'precioventa'   =>$this->precioventa,
            'descuento'     =>$this->descuento,
            'barra'         =>$this->barra,           
            'ultimacompra'  =>$this->ultimacompra,
            'ultimaventa'   =>$this->ultimaventa,
            'cambioprecio'  =>$this->cambioprecio,
            'registrosanitario'=> $this->registrosanitario,
            'estatus'       =>0,
            ]);
        }
        else 
        {
            $class = Product::find($this->selected_id);
            $class->update([
            'nombre'        =>$this->nombre,
            'presentacion'  =>$this->presentacion,
            'tipo'          =>$this->tipo,
            'clase'         =>$this->clase,
            'linea'         =>$this->linea,
            'lineafac'      =>$this->lineafac,
            'fabricante'    =>$this->fabricante,
            'manejopor'     =>$this->manejopor,       
            'costo'         =>$this->costo,
            'ultimocosto'   =>$this->ultimocosto,
            'precioventa'   =>$this->precioventa,
            'descuento'     =>$this->descuento,
            'barra'         =>$this->barra,           
            'ultimacompra'  =>$this->ultimacompra,
            'ultimaventa'  =>$this->ultimaventa,
            'cambioprecio'  =>$this->cambioprecio,
            'registrosanitario'=> $this->registrosanitario,

             ]);                    
        }

        if($this->selected_id) 
            $this->emit('msg-ok', 'Producto Actualizado');
        else
            $this->emit('msg-ok', 'Producto Creado');

        $this->resetInput();
    }


    //escuchar eventos y ejecutar acción solicitada
    protected  $listeners = [
        'deleteRow'     => 'destroy' ,   
        'createModal' => 'createModal',
        'doAction'    => 'doAction',
    ];  

    //metodo create from modal

    public function createModal($info)
    {
        $record = json_decode($info);

        $this->selected_id = $data->id;

        $this->nombre           = $record->nombre;
        $this->presentacion     = $record->presentacion;
        $this->tipo             = $record->tipo;
        $this->clase            = $record->clase;          
        $this->linea            = $record->linea;
        $this->lineafac         = $record->lineafac;
        $this->fabricante       = $record->fabricante;
        $this->manejopor        = $record->manejopor;       
        $this->costo            = $record->costo;
        $this->ultimocosto      = $record->ultimocosto;
        $this->precioventa      = $record->precioventa;
        $this->preciosugerido   = $record->preciosugerido;
        $this->descuento        = $record->descuento;
        $this->barra            = $record->barra;           
        $this->ultimacompra     = $record->ultimacompra;
        $this->$ultimaventa     = $record->ultimaventa;           
        $this->estatus          = $record->estatus;

        $this->CreateOrUpdate();
    }

   //método para eliminar un registro dado
    public function destroy($id)
    {
        if ($id)
        {
            $record = Product::where('id', $id);
            /* $record->delete();*/
            $record->update([
                'estatus' => 1, ]);                    
    
            $this->resetInput();
            $this->emit('msgok', "Registro ha sido desactivado del sistema");    
        }
    }

}

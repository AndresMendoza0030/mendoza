<?php

namespace App\Http\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

use App\Productclass;
use App\Productline;
use App\Product;
use App\Estudioslb;

use App\Kits;
use App\Kitdetalle;

use App\Sysparam;
use App\Generalsetup;


class PerfilesController extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    
	//properties
    public  $nombre,$estudioid=0, $productoid=0,$perfilid=0,$modonuevo=0,$elperfil='', $tprecio=0, $modoagregar=0;
    public  $mdprecio=0, $mddescuento=0, $mdperfil='', $precioprod=0, $descuentoprod=0;
    public  $selected_id, $search;   			//para búsquedas y fila seleccionada
    public  $action = 1;             			//manejo de ventanas
    private $pagination = 12;         			//paginación de tabla
    public  $datos=[],$detalle;
    

    public function mount()
    {
        //
        $this->detalle=collect([]);

        $empresa = Generalsetup::first();
        $mitasaiva = round($empresa->piva/100,2);
        $this->tasaiva = $mitasaiva;

        $parametros=Sysparam::first();
        $this->laclase=$parametros->claselaboratorio;
        //

    }



    //método que se ejecuta al inciar el componente
    public function render()
    {

        if(strlen($this->search) > 0)
        {
            $data = Kits::leftjoin('products as pr', 'pr.id','kits_service.producto_id')
            ->where('kits_service.estatus',0)->select('kits_service.*','pr.precioventa as preventa')
            ->select('kits_service.*')
            ->where('kits_service.nombre', 'like', '%' .  $this->search . '%')
            ->orderby('kits_service.nombre')
            ->paginate($this->pagination);
        }
        else
        {
            $data = Kits::leftjoin('products as pr', 'pr.id','kits_service.producto_id')
            ->where('kits_service.estatus',0)->select('kits_service.*','pr.precioventa as preventa')
            ->orderby('kits_service.nombre')
            ->paginate($this->pagination);
  
        }

        return view('livewire.perfiles.component', [
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
        $this->action = $action;
    }

    public function editarPerfil($id)
    {
        $this->perfilid=$id;
        //
        $perfil=Kits::find($this->perfilid);
        if (isset($perfil))
        {
            $this->action=2;
            $this->detalle=collect([]);
        }
    }

    public function nuevoPerfil()
    {
        $this->datos = Product::select("products.*",
        DB::raw("CONCAT(TRIM(products.nombre),' ',TRIM(products.presentacion)) as nombreproducto"))
        ->where('esperfil',"S")->where('products.estatus','=',0)
        ->orderby('products.nombre')->get();
        $this->emit('select2');
        $this->modonuevo=1;
        //
        
    }

    public function datosPerfil($id)
    {
        $this->productoid=$id;
        $existe=Kits::where('producto_id',$id)->where('estatus',0)->count();
        if ($existe>0)
        {
            $this->emit('msg-error','Producto ya existe en Perfiles....');
            $this->modonuevo=0;
            return;
        }
        $estudio=Product::find($id);
        if (isset($estudio))
        {
            $this->idestudio=0;
            $mdperfil = $estudio->nombre;
            //
            $this->emit('abre-Modal-Perfil',["perfil"=>$mdperfil]);
        }
    }


    public function resetEstudio()
    {
        $this->modonuevo=0;
    }

    public function updPerfil()
    {
        //
        if ($this->modonuevo==1)
        {
            $producto=Product::find($this->productoid);
            //
            if (isset($producto) && $producto->estatus==0)
            {
                $perfil=Kits::create([
                    'producto_id'           =>$this->productoid,
                    'nombre'                =>$producto->nombre,
                    'estatus'               => 0,
                    'user_id'               => auth()->user()->id,
                ]);
                //
                $this->perfilid=0;
                $this->productoid=0;
                $this->modonuevo=0;
                //
                $this->emit('msgok', 'El Nombre del Perfil  ha sido creado con Exito. . .');
    
            }
        }
        $this->modonuevo=0;        
    }

    public function detallarPerfil($id)
    {
        $perfil=Kits::find($id);
        if (isset($perfil))
        {
            $this->elperfil=$perfil->nombre;
            $this->perfilid=$perfil->id;
            //
            $this->detalle=Kitdetalle::where('idhead',$id)->where('estatus',0)
            ->orderby('producto_id')->get();
            $this->tprecio=0;
            $this->tprecio=$this->detalle->sum('precio');
            //

        }

        $this->action=2;

    }

    public function nuevoEstudio()
    {
        $this->datos = Product::select("products.*",
        DB::raw("CONCAT(TRIM(products.nombre),' ',TRIM(products.presentacion)) as nombreproducto"))
        ->where('esperfil',"N")->where('products.estatus','=',0)
        ->orderby('products.nombre')->get();
        $this->emit('select2');
        //
        $this->estudioid=0;
        $this->precioprod=0;
        $this->descuentoprod=0;
        $this->modoagregar=1;
    }


    public function datosProducto($id)
    {
        $this->estudioid=$id;
        $existe=Kitdetalle::where('producto_id',$id)->where('idhead',$this->perfilid)->where('estatus',0)->count();
        if ($existe>0)
        {
            $this->emit('msg-error','Producto/Estudio ya existe en detalle....');
            $this->precioprod=0;
            $this->descuentoprod=0;
            $this->estudioid=0;
            $this->modoagregar=0;
            return;
        }
        //
        $elestudio=Product::find($id);
        if (isset($elestudio))
        {
            $this->precioprod=$elestudio->precioventa;
        }
    }

    public function saveProd()
    {
        if ($this->modoagregar==1)
        {
            $existe=Kitdetalle::where('producto_id',$this->estudioid)->where('idhead',$this->perfilid)->where('estatus',0)->count();
            if ($existe>0)
            {
                $this->emit('msg-error','Producto/Estudio ya existe en detalle....');
                $this->precioprod=0;
                $this->descuentoprod=0;
                $this->estudioid=0;
                $this->modoagregar=0;
                return;
            }
            $datosestudio = Product::find($this->estudioid);
            //
            $estudio=Kitdetalle::create([
                'idhead'        => $this->perfilid,
                'producto_id'   => $this->estudioid,
                'nombre'        => $datosestudio->nombre,
                'precio'        => round($this->precioprod,4),
                'descuento'     => round($this->descuentoprod,4),
                'estatus'       => 0,
                'user_id'       => auth()->user()->id,
            ]);
            //
            $this->emit('msgok','Producto/Estudio ha sido Grabado con Exito....');
            //
            $this->precioprod=0;
            $this->descuentoprod=0;
            $this->estudioid=0;
            $this->modoagregar=0;
            //
            $this->detalle=Kitdetalle::where('idhead',$this->perfilid)->where('estatus',0)
            ->orderby('producto_id')->get();
            //
            $this->tprecio=0;
            $this->tprecio=$this->detalle->sum('precio');
            //
        }
    }


    public function descartaProd()
    {
        $this->precioprod=0;
        $this->descuentoprod=0;
        $this->estudioid=0;
        $this->modoagregar=0;
    }

    public function quitarPerfil($id)
    {
        $aborrar = Kits::find($id);
        if ($aborrar)
        {
            $aborrar->estatus=1;
            $aborrar->save();
            $this->emit('msgok','El Pérfil ha sido deshabilitado...');
        }
    }

    public function quitarEstudio($id)
    {
        $aborrar = Kitdetalle::find($id)->delete();
        $this->detalle=Kitdetalle::where('idhead',$this->perfilid)->where('estatus',0)
        ->orderby('producto_id')->get();
        $this->tprecio=0;
        $this->tprecio=$this->detalle->sum('precio');
        //
        $this->emit('msgok','El Estudio fue desvinculado del Pérfil...');

    }

    //escuchar eventos y ejecutar acción solicitada
    protected  $listeners = [
        'deleteRow'         => 'destroy' ,   
        'grabaLineaF'       => 'grabaLineaF',
        'doAction'          => 'doAction',
        'updPerfil'         => 'updPerfil',
        'datosPerfil'      => 'datosPerfil',
        'datosProducto'     => 'datosProducto',
        'grabaReferenciaF'  => 'grabaReferenciaF',
        'quitarEstudio'     => 'quitarEstudio',
        'quitarPerfil'      => 'quitarPerfil',
        'resetEstudio'      => 'resetEstudio'
    ];  

}

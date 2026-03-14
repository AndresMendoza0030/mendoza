<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Livewire\WithPagination;
use Livewire\Component;
use App\Manufacturer;
use App\Warehouse;
use App\Productclass;
use App\Productline;
use App\Product;
use App\Paciente;
use App\Referente;
use App\Room;
use App\Productsstocks;
use App\Productclassinvoice;
use App\ClienteProveedor;
use App\Inventoryhead;
use App\Movdetail;
use App\Invoicedetail;
use App\Generalsetup;
use App\Vencimiento;
use App\User;
use Spatie\Permission\Models\Role;

class DevolucionesController extends Component
{
    use WithPagination;


	//properties
    public  $selected_id, $search,$buscarPaciente;   			//para búsquedas y fila seleccionada
    public  $action = 1;             			//manejo de ventanas
    public  $section= 1;
    private $pagination = 4;         			//paginación de tabla

    public  $midetalle =[];

    public  $bodegas, $pacientes, $vales, $tipopaciente; //listas para select

    public  $fecha;

    public  $info=[];

    public  $elmedico, $elcuarto, $lafechahora, $pacienteid,$lotesprodsum,$elvale,$labodega,$fechadoc,$reg,$nombrepaciente;
    public  $mdcantidad, $mdadevolver, $mdproducto, $mddevuelto, $mdregistro, $condevo,$bodegaop,$datospaciente;


    public function mount()
    {
        $fecha= date('Y-m-d');
        $this->fecha = $fecha;
        $this->vales=collect([]);
        $this->elvale=0;
        $this->condevo=0;
        $this->reg=0;
        $this->bodegaop=0;
        $this->datospaciente='';

        //
        $idrol=0;
        $nombrerol=auth()->user()->getRoleNames();
        //
        if ($nombrerol->count() ==1)
        {
            $rol=Role::where('name','=',$nombrerol)->first();
            $this->tipopaciente=$rol->perfil_paciente;
            $idrol=$rol->id;

        }
        else
        {
            foreach ($nombrerol as $nr)
            {
                if ($nr=="jefes" || $nr=="supervisores" || $nr=="gerente" || $nr=="jefe_enfermeria") 
                {
                    $this->tipopaciente="TODOS";
                    break;
                }
                else
                {
                    $rol=Role::where('name','=',$nombrerol)->first();
                    $this->tipopaciente=$rol->perfil_paciente;
                    $idrol=$rol->id;
        
                }
            }
        }
    }

    //método que se ejecuta al inciar el componente
    public function render()
    {
        $pacientes = null;
        $this->crearArreglos();

        if ($this->tipopaciente=="TODOS")
        {
            $this->pacientes  = Paciente::leftjoin('rooms as r', 'r.id', 'pacientes.room')
            ->leftjoin('referentes as md', 'md.id', 'pacientes.referente')
            ->select("pacientes.*", 'r.nombre as elcuarto', 
            DB::raw("CONCAT(TRIM(md.apellidos),' ',TRIM(md.nombres)) as elmedico"),
            DB::raw("CONCAT(TRIM(pacientes.fecha),' - ',TRIM(pacientes.hora)) as fechayhora"),        
            DB::raw("CONCAT(TRIM(pacientes.nombres),' ',TRIM(pacientes.apellidos)) as nombrepaciente"))        
            ->where('abierto','=','SI')->where('pacientes.estatus','=','0')->get();  
        }
        else
        {
            $this->pacientes  = Paciente::leftjoin('rooms as r', 'r.id', 'pacientes.room')
            ->leftjoin('referentes as md', 'md.id', 'pacientes.referente')
            ->select("pacientes.*", 'r.nombre as elcuarto', 
            DB::raw("CONCAT(TRIM(md.apellidos),' ',TRIM(md.nombres)) as elmedico"),
            DB::raw("CONCAT(TRIM(pacientes.fecha),' - ',TRIM(pacientes.hora)) as fechayhora"),        
            DB::raw("CONCAT(TRIM(pacientes.nombres),' ',TRIM(pacientes.apellidos)) as nombrepaciente"))        
            ->where('tipopaciente','=',$this->tipopaciente)->where('abierto','=','SI')->where('pacientes.estatus','=','0')->get();

        }

        //
        if(($this->pacienteid>0))
        {
            $this->vales= Inventoryhead::where('paciente_id','=',$this->pacienteid)
            ->where(function($prodserv)
            {
                $prodserv->where('tipodoc','=','SCAR')
                ->where('pedido','=',null)
                ->orwhere('tipodoc','=','SERV');
            })
            ->leftjoin('warehouses as bod', 'bod.id','=','inventoryhead.bodega')
            ->select('inventoryhead.id as documento','inventoryhead.bodega','inventoryhead.fecha as fechadoc',
            'bod.nombre as labodega',
            DB::raw("CONCAT('Bod: ',TRIM(bod.nombre),' Mov.No.: ',inventoryhead.id,' Fecha: ', inventoryhead.fecha) as infovale"))
            ->orderby('inventoryhead.id')->get();
            //
            if ($this->elvale>0)
            {
                $devu = $this->info->where('condevo','=','SI')->count();

                if ($devu >= 1)
                {
                    $this->condevo=1;
                }
                else
                {
                    $this->condevo=0;                
                }   
            }

        }   

        return view('livewire.devoluciones.component' , ['info' => $this->info,]);
    }


    public function asgDatosPaciente($llave)
    {
        $datospac=Paciente::find($llave);
        $this->pacienteid=$llave;
        $this->datospaciente=TRIM($datospac->nombres)." ".TRIM($datospac->apellidos);
        
        $elmedico = Referente::find($datospac->referente);
        if ($elmedico)
        {
            $this->elmedico = TRIM($elmedico->apellidos)." ".TRIM($elmedico->apellidos);
        }
        if ($datospac->room > 0)
        {
            $cuarto = Room::find($datospac->room);
            if ($cuarto)
            {
                $this->elcuarto = $cuarto->nombre;
            }
            $this->datospaciente=TRIM($datospac->nombres)." ".TRIM($datospac->apellidos);
        }
        $this->lafechayhora = date("d-m-Y", strtotime($datospac->fecha))." ".$datospac->hora;

        //vales
    }

    public function asgElVale($valor)
    {
        $this->elvale=$valor;
        $vale=Inventoryhead::find($valor);
        if ($vale)
        {
            $this->documento = $vale->id;
            $this->fechadoc = $vale->fecha;
            //
            $bodega = Warehouse::find($vale->bodega);
            $this->labodega = $bodega->nombre;
            $this->bodegaop=$vale->bodega;

            //
            $info=collect([]);
            $contenido= Movdetail::where('movdetails.numerodoc','=',$valor)
            ->leftjoin('products as prod', 'prod.id','=','movdetails.producto_id')
            ->leftjoin('vencimientos as v', 'v.id','=','movdetails.lotevence')            
            ->select('movdetails.id','movdetails.producto_id','movdetails.bodega','movdetails.numerodoc',
            'movdetails.producto','prod.manejopor as manejopor','movdetails.cantidad','movdetails.preventa',
            'movdetails.precosto','movdetails.gravado','movdetails.devuelto','movdetails.lotevence',
            'movdetails.paciente_id','movdetails.fecha','v.mes as mes', 'v.anno as anno')
            ->orderby('movdetails.id')->get();
            //
            $cnt=$contenido->sum('cantidad');
            $dev=$contenido->sum('devuelto');            
            
            if ($cnt==$dev)
            {
                $this->emit('msg-error',"Este vale no posee producto a devolver");
                $this->info=$info;
                $this->elvale=0;
                $this->documento = '';
                $this->fechadoc = '';
                $this->labodega = '';
                $this->bodegaop='';
                return;
            }

            foreach($contenido as $c)
            {
                $info->push(["reg"=>$this->reg, "productoid"=>$c->producto_id, "producto"=>$c->producto,"yadevuelto"=>$c->devuelto,
                "cantidad"=>$c->cantidad, "devuelto"=>0,"mes"=> $c->mes, "anno"=>$c->anno, "lotevence"=>$c->lotevence,
                "condevo"=>'NO',"preventa"=>$c->preventa,"unico"=>$c->id]);
                $this->reg+=1;
            }
            $this->info=$info;

        }
    }

    public function editarDevolver($registro)
    {
        $this->mdproducto=$this->info[$registro]['producto'];
        $this->mdcantidad=$this->info[$registro]['cantidad'];
        $this->mdadevolver=$this->info[$registro]['devuelto'];
        $this->mddevuelto=$this->info[$registro]['yadevuelto'];
        $this->mdregistro = $registro;
        //
        
        $this->emit('abrir-modal');
    }    
  
    public function devuelveTodo($registro)
    {
        $this->mdcantidad=$this->info[$registro]['cantidad'];
        $this->mddevuelto=$this->info[$registro]['yadevuelto'];
        $this->mdadevolver=$this->info[$registro]['cantidad']-$this->info[$registro]['yadevuelto'];
        $this->mdregistro = $registro;
        $rg=$this->mdregistro;
        $dv=$this->mdadevolver;
        $this->info->transform(function ($item) use($rg,$dv)
        {
            if ($item['reg'] == $rg)
            {
                $item['devuelto'] = $dv;
                $item['condevo'] = "SI";
            }
            return $item;
        });
    }

    public function desHaceDevolucion($registro)
    {
        $this->mdcantidad=$this->info[$registro]['cantidad'];
        $this->mdregistro = $registro;
        $rg=$this->mdregistro;
        $dv=0;
        $this->info->transform(function ($item) use($rg,$dv)
        {
            if ($item['reg'] == $rg)
            {
                $item['devuelto'] = '';
                $item['condevo'] = "NO";                
            }
            return $item;
        });

    }

    public function saveLine()
    {
        $rg=$this->mdregistro;
        $dv=$this->mdadevolver;
        $this->info->transform(function ($item) use($rg,$dv)
        {
            if ($item['reg'] == $rg)
            {
                $item['devuelto'] = $dv;
                $item['condevo'] = "SI";                
            }
            return $item;
        });
        $this->emit('cerrar-modal');
    }

    public function devuelveTodoVale()
    {
        $info=collect([]);
        $contenido= Movdetail::where('movdetails.numerodoc','=',$this->elvale)
        ->leftjoin('products as prod', 'prod.id','=','movdetails.producto_id')
        ->leftjoin('vencimientos as v', 'v.id','=','movdetails.lotevence')            
        ->select('movdetails.id','movdetails.producto_id','movdetails.bodega','movdetails.numerodoc',
        'movdetails.producto','prod.manejopor as manejopor','movdetails.cantidad','movdetails.preventa',
        'movdetails.precosto','movdetails.gravado','movdetails.devuelto','movdetails.lotevence',
        'movdetails.paciente_id','movdetails.fecha','v.mes as mes', 'v.anno as anno')
        ->orderby('movdetails.id')->get();
        //
        foreach($contenido as $c)
        {
            if ($c->cantidad != $c->devuelto)
            {
                $info->push(["reg"=>$this->reg, "productoid"=>$c->producto_id, "producto"=>$c->producto,"yadevuelto"=>$c->devuelto,
                "cantidad"=>$c->cantidad, "devuelto"=>$c->cantidad-$c->devuelto,"mes"=> $c->mes, "anno"=>$c->anno,
                "lotevence"=>$c->lotevence,
                "condevo"=>'SI',"preventa"=>$c->preventa,"unico"=>$c->id]);
                $this->reg+=1;
            }
        }
        $this->info=$info;
    }


    public function descartarOperacion()
    {
        $info=collect([]);
        $this->info=$info;
        $this->limpiarOperacion();
    }

    public function crearArreglos()
    {
        $this->bodegas      = Warehouse::where('estatus','=','0')->get();        

        $this->productos= Product::select("products.*",
        DB::raw("CONCAT(TRIM(products.nombre),' ',TRIM(products.presentacion)) as nombreproducto"))
        ->where('estatus','=','0')->where('tipo','=','PRODUCTO')
        ->orderby('nombreproducto')->get();

    }


    //limpiar pacientes
    public function limpiarOperacion()
    {
    $fecha= date('Y-m-d');
    $this->fecha = $fecha;
    $this->elvale=0;
    $this->condevo=0;
    $this->reg=0;
    //
    $this->elmedico         = '';
    $this->datospaciente    ='';
    $this->elcuarto         = '';
    $this->lafechahora      = '';
    $this->pacienteid       = 0;

    $this->documento        = '';
    $this->fechadoc         = '';
    $this->labodega         = 0;
    $this->bodegaop         =0;


    $this->mdcantidad       ='';
    $this->mdadevolver      ='';    
    $this->mdproducto       ='';
    $this->mddevuelto       ='';
    $this->mdregistro       ='';
    $this->condevo          =0;    
    $this->vales=[];
    $this->info=[];
    }


    public function grabarOperacion()
    {
        $empresa = Generalsetup::first();
        $tasaiva = round($empresa->piva/100,2);
        
        $elpaciente = Paciente::find($this->pacienteid);
        if ($elpaciente->abierto=="NO")
        {
            $this->emit('msg-error', 'El Paciente ya esta cerrado!!! Imposible Grabar. . .');    
            return;
        }
        $this->idusuario = auth()->user()->id;
        //iniciamos transacción
        DB::beginTransaction();
        try { 
            //Head Registrando Salida
            $head = InventoryHead::create([
                'bodega'        => $this->bodegaop,
                'paciente_id'   => $this->pacienteid,
                'bodegadestino' => 0,
                'docucontra'    => $this->elvale,
                'fecha'         => Carbon::now(),
                'tipodoc'       => 'DEVO',
                'estatus'       => 0,
                'user_id'       => auth()->user()->id,
                'concepto'      => "Devolución Pac: ".$this->datospaciente
            ]);      

            //Detalle Registrando Salida
            foreach($this->info as $det)
            {
                if ($det['devuelto']>0)
                {
                    $produx = Product::find($det['productoid']);
                    $dtcfac = ProductclassInvoice::find($produx->lineafac);                  
                    $detabod = Movdetail::create([
                    'bodega'        => $head->bodega,
                    'tipodoc'       => $head->tipodoc,
                    'numerodoc'     => $head->id,
                    'paciente_id'   => $this->pacienteid,
                    'fecha'         => $head->fecha,
                    'clase'         => $produx->clase,
                    'linea'         => $produx->linea,
                    'lineafac'      => $produx->lineafac,
                    'producto_id'   => $det['productoid'],
                    'producto'      => $det['producto'],
                    'cantidad'      => $det['devuelto'] * -1,
                    'movimiento'    => "E",
                    'user_id'       => $head->user_id,
                    'tasaiva'       => $tasaiva*100,
                    'precosto'      => $produx->costo,
                    'preventa'      => $det['preventa'],
                    'subtotal'      => round(($det['devuelto'] * -1)*$det['preventa'],4),
                    'gravado'       => round((($det['devuelto'] * -1)*$det['preventa']),4),
                    'iva'           => round((($det['devuelto'] * -1)*$det['preventa'])*$tasaiva,4),
                    'total'         => round((round(($det['devuelto'] * -1)*$det['preventa'],4) +
                                       round((($det['devuelto'] * -1)*$det['preventa'])*$tasaiva,4)),2),
                    'admongp'       => $dtcfac->admongp,
                    'admonval'      => round(round((($det['devuelto'] * -1)*$det['preventa']),4)*($dtcfac->comision/100),4),
                    'lotevence'     => $det['lotevence'],
                    'estatus'       => 0
                    ]);
                    if ($det['lotevence']>0)
                    {
                        $ltvc= Vencimiento::find($det['lotevence']);
                        $el=$ltvc->entradas + $det['devuelto'];
                        $ltvc->entradas=$el;
                        $ltvc->total=$ltvc->entradas-$ltvc->salidas; 
                        $ltvc->save();                   
                    }
                }
            }
            //upd en vale
            foreach($this->info as $det)
            {
                $detabod = Movdetail::where('id',$det['unico'])->first();      
                $dvz=$detabod->devuelto;
                if ($dvz==null)
                    {$dvz=0;}

                $dvax=( $dvz + $det['devuelto']);

                $detabod->update(['devuelto'=>$dvax]);
                $detabod->save();
            }

            //confirmamos transacción
            DB::commit();
            $this->emit('msgok', 'Devolución ha sido guardada. . .');
            $this->limpiarOperacion();
            $this->emit('print-mov',  $head->id);            

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

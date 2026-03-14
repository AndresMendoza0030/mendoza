<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Livewire\WithPagination;
use Livewire\Component;
use App\Warehouse;
use App\Product;
use App\Paciente;
use App\Referente;
use App\Room;
use App\Productstock;
use App\Inventoryhead;
use App\Reqhead;
use App\Detareq;
use App\Movdetail;
use App\Vencimiento;
use App\Generalsetup;
use App\User;

class SolicitadevolucionController extends Component
{
    use WithPagination;


	//properties
    public  $selected_id, $search,$buscarPaciente;   			//para búsquedas y fila seleccionada
    public  $action = 1;             			//manejo de ventanas
    public  $section= 1;
    private $pagination = 4;         			//paginación de tabla

    public $vales,$info,$detalle,$ladevolucion,$pacientes,$convales,$fmodal,$nombrepaciente;
    public $pacienteid, $elreferente, $elcuarto,$lafechayhora, $labodegaop,$labodega, $elvale,$eldocumento,$fechadoc;
    public $numeroreg,$tregistros,$mdproducto, $mdcantidad, $mdadevolver,$mdindice, $todo;



    public function mount()
    {
        $fecha= date('Y-m-d');
        $this->fecha = $fecha;

        $this->vales=[];
        $this->info=[];        
        $this->detalle=collect([]);
        $this->ladevolucion=collect([]);
        $this->tregistros=0;
        $this->numeroreg=0;
        $this->todo=0;
        $this->fmodal=false;
        $this->nombrepaciente='';
        $this->labodega=0;
    }

    //método que se ejecuta al inciar el componente
    public function render()
    {
            $this->bodegas      = Warehouse::where('estatus','=','0')->get();        
            //
            $this->pacientes= Paciente::where('pacientes.estatus','=','0')
            ->where('pacientes.abierto','=','SI')
            ->leftjoin('referentes as md','pacientes.referente','=','md.id')
            ->leftjoin('rooms as rm','pacientes.room','=','rm.id')
            ->select(DB::raw("CONCAT(TRIM(pacientes.nombres),' ',TRIM(pacientes.apellidos)) as nombrepaciente"),
            DB::raw("CONCAT(TRIM(pacientes.fecha),' - ',TRIM(pacientes.hora)) as fechayhora"),
            DB::raw("CONCAT(TRIM(md.apellidos),' ',TRIM(md.nombres)) as elmedico"),
            'rm.nombre as elcuarto','pacientes.id as idpaciente')
            ->orderby('pacientes.id')->get();
            //asignamos a la propiedad clientes el resultado dela búsqueda
            if($this->pacienteid>0)
            {
                $this->vales= Inventoryhead::where('paciente_id','=',$this->pacienteid)
                ->where('tipodoc','=','SCAR')
                ->leftjoin('warehouses as bod', 'bod.id','=','inventoryhead.bodega')
                ->select('inventoryhead.id as documento','inventoryhead.bodega','inventoryhead.fecha as fechadoc',
                'bod.nombre as labodega',
                DB::raw("CONCAT(tipodoc,' Bod: ',TRIM(bod.nombre),' Mov.No.: ',inventoryhead.id,' Fecha: ', inventoryhead.fecha) as infovale"))
                ->orderby('inventoryhead.id')->get();     
            }   
            //
        //    where(function($prodserv)
        //    {
        //        $prodserv->where('tipodoc','=','SCAR')
        //        ->orwhere('tipodoc','=','SERV');
        //    })
        
        return view('livewire.solicitadevolucion.component');
    }

    //buscar pacientes
    public function asgPaciente($llave)
    {
        $this->pacienteid=$llave;
        $datospac=Paciente::find($llave);
        //
        $this->nombrepaciente=TRIM($datospac->nombres)." ".TRIM($datospac->apellidos);
        //
        $elmedico = Referente::find($datospac->referente);
        if ($elmedico)
        {
            $this->elreferente = TRIM($elmedico->apellidos)." ".TRIM($elmedico->apellidos);
        }
        if ($datospac->room > 0)
        {
            $cuarto = Room::find($datospac->room);
            if ($cuarto)
            {
                $this->elcuarto = $cuarto->nombre;
            }
        }
        $this->lafechayhora = date("d-m-Y", strtotime($datospac->fecha))." ".$datospac->hora;
        //
    }

    public function seleVale($dato2)
    {
        $this->info=collect([]);
        $this->detalle=collect([]); 
        $this->numeroreg=0;
        $this->mdindice=0;
        $this->tregistros=0;
        $this->todo=0;

        $headinfo=Inventoryhead::find($dato2);
        $info= Movdetail::where('movdetails.numerodoc','=',$dato2)->get();
        //        
        if ($info->count()>0)
        {
            //
            $reg=0;
            foreach ($info as $i)
            {
                //
                $cvence='';
                if ($i->lotevence>1)
                {
                    $vc = Vencimiento::find($i->lotevence);
                    if ($vc)
                    {
                        $cvence= $vc->mes . "/" . $vc->anno;
                    }
                }
                $procesa="NO";

                if ($i->cantidad==$i->devuelto)
                {
                    $procesa="SI";
                }
                else
                {
                    $procesa="NO";                    
                }

                $this->info->push(["reg"=>$reg,"id"=>$i->id, "productoid"=>$i->producto_id, "producto"=>$i->producto,
                "cantidad"=>$i->cantidad, "devuelto"=>$i->devuelto, "lotevence"=>$i->lotevence, "cvence"=>$cvence,
                "disponible"=>$i->cantidad-$i->devuelto,"procesado"=>$procesa, "unico"=>$i->id, "solicitado"=>'',
                "preventa"=>$i->preventa]);
                //
                $reg+=1;
            }

            $this->elvale       = $dato2;
            $this->eldocumento  = $dato2;
            $this->fechadoc     = $headinfo->fecha;
            $this->labodega     = $headinfo->bodega;
            $wf=Warehouse::find($headinfo->bodega);
            $this->labodegaop   = $wf->nombre;
            //
        }
    }

    public function devolverParte($id)
    {
        if ($this->info[$id]['procesado']=="SI")
        {
            $this->emit('msg-error', 'El registro ya esta en Documento a Devolver..o Ya fue solicitado previamente.!!!!');
            return;
        }

        $this->mdproducto = $this->info[$id]['producto'];
        $this->mdcantidad = $this->info[$id]['cantidad'];
        $this->mdadevolver = $this->info[$id]['disponible'];
        $this->mdindice = $id;
        $this->fmodal=true;
        //
        $this->emit('abre-edicion',$this->mdadevolver);
        //

    }

    public function devolverTodoRegistro($reg)
    {
        if ($this->info[$reg]['procesado']=="SI")
        {
            $this->emit('msg-error', 'El registro ya esta en Documento a Devolver..o Ya fue solicitado previamente.!!!!');
            return;
        }

        $this->mdproducto = $this->info[$reg]['producto'];
        $this->mdcantidad = $this->info[$reg]['cantidad'];
        $this->mdadevolver = $this->info[$reg]['disponible'];
        $this->mdindice = $reg;
        $this->fmodal=false;
        //
        $this->grabaEdicion();
        //
    }

    public function descartarOperacion()
    {
        $this->resetInput();
    }

    public function todoDocumento()
    {
        $error=0;
        $this->todo=1;
        $this->detalle=collect([]);
        foreach($this->info as $i)
        {
            if ($i['procesado']=="NO")
            {  
                $this->mdproducto = $i['producto'];
                $this->mdcantidad = $i['cantidad'];
                $this->mdadevolver =$i['disponible'];
                $this->mdindice    =$i['reg'];

                $this->fmodal=false;
                //
                $this->grabaEdicion();
                //
            }
            else
            {
                $error+=1;
            }
        }
        if ($error>0)
        {
            $this->emit('msg-error', 'Los registros no pasados ya estaban en la Solicitud y/o Se solicitó previamente.!!!!');
        }

    }



    public function grabaEdicion()
    {
        $this->detalle->push(["reg"=>$this->numeroreg, "id"=>$this->info[$this->mdindice]['id'],
        "productoid"=>$this->info[$this->mdindice]['productoid'], "producto"=>$this->info[$this->mdindice]['producto'],
        "adevolver"=>$this->mdadevolver, "lotevence"=>$this->info[$this->mdindice]['lotevence'],
        "cvence"=>$this->info[$this->mdindice]['cvence'],"unico"=>$this->info[$this->mdindice]['unico'],
        "preventa"=>$this->info[$this->mdindice]['preventa']]);
        //

        $this->numeroreg+=1;
        $this->tregistros+=1;
        //actualizando principal

        if ($this->todo=0)
        {
            $rg=$this->mdindice;
            $this->info->transform(function ($item) use($rg)
            {
                if ($item['reg'] == $rg)
                {
                    $item['procesado'] = "SI";
                }
                return $item;
            });   
        }
    }

    //método para reiniciar variables
    public function resetInput()
    {
        $this->info=collect([]);
        $this->detalle=collect([]); 
        $this->numeroreg=0;
        $this->mdindice=0;
        $this->pacienteid=0;
        $this->tregistros=0;
        //
        $fecha= date('Y-m-d');
        $this->fecha = $fecha;

        $this->vales=[];
        $this->info=[];        
        $this->detalle=collect([]);
        $this->fmodal=false;
        $this->nombrepaciente='';
        $this->labodega=0;
        $this->elreferente='';
        $this->elcuarto='';
        $this->lafechayhora='';
        $this->labodega='';
        $this->bodegaop='';
        $this->elvale='';
        $this->eldocumento='';
        $this->fechadoc='';
        $this->mdproducto='';
        $this->mdcantidad='';
        $this->mdadevolver='';


    }

    //escuchar eventos y ejecutar acción solicitada
    protected  $listeners = [
        'deleteRow'         => 'destroy' ,   
        'grabaEdicion'      => 'grabaEdicion',
    ];  


    public function grabarBd()
    {
        if ($this->pacienteid<1)
        {
            return;
        }

        $elpaciente = Paciente::find($this->pacienteid);
        if ($elpaciente->abierto=="NO")
        {
            $this->emit('msg-error', 'El Paciente ya esta cerrado!!! Imposible Grabar. . .');    
            return;
        }


        //iniciamos transacción
        DB::beginTransaction();
        try { 
            //Head Registrando Salida
            $head = Reqhead::create([
                'bodega'        => $this->labodega,
                'paciente_id'   => $this->pacienteid,
                'bodegadestino' => 0,
                'docucontra'    => $this->eldocumento,
                'fecha'         => Carbon::now(),
                'tipodoc'       => 'PDEV',
                'estatus'       => 0,
                'user_id'       => auth()->user()->id,
                'concepto'      => "Solicitud Devolución Pac: ".$this->nombrepaciente
            ]);      

            //Detalle Registrando Salida
            foreach($this->detalle as $det)
            {
                $produx = Product::find($det['productoid']);
                
                $detabod = Detareq::create([
                    'bodega'        => $head->bodega,
                    'tipodoc'       => $head->tipodoc,
                    'numerodoc'     => $head->id,
                    'paciente_id'   => $this->pacienteid,
                    'fecha'         => $head->fecha,
                    'producto_id'   => $det['productoid'],
                    'producto'      => $det['producto'],
                    'cantidad'      => $det['adevolver'],
                    'idoriginal'    => $det['unico'],
                    'lotevence'     => $det['lotevence'],
                    'user_id'       => $head->user_id,
                    'precosto'      => $produx->costo,
                    'preventa'      => $det['preventa'],
                    'subtotal'      => round($det['adevolver']*$det['preventa'],4),
                    'estatus'       => 0
                ]);
                //
                $updmov=Movdetail::find($det['unico']);
                if ($updmov)
                {
                    $updmov->solicitado+=$det['adevolver'];
                    $updmov->save();
                }
            }
            //confirmamos transacción
            DB::commit();
            $this->resetInput();            
            $this->emit('msgok', 'Solicitud ha sido guardada. . .'); 
            $this->emit('confirmaOrden', $head->id);
        }
         catch (\Exception $e)
         {
            //para no tener inconsistencia de la info, deshacemos los cambios en caso de que algo falle
            DB::rollback();
            $status = $e->getMessage();       
            dd($e);     
         }

    }

    //método para eliminar un registro dado
    public function destroy($id)
    {

        $nuevodet=collect([]);
        foreach($this->detalle as $rs)
        {
          
            if ($rs['reg'] != $id)
            {
                $nuevodet->push(["reg"=>$rs['reg'], "id"=>$rs['id'],
                "productoid"=>$rs['productoid'], "producto"=>$rs['producto'],
                "adevolver"=>$rs['adevolver'], "lotevence"=>$rs['lotevence'],
                "cvence"=>$rs['cvence'],"preventa"=>$rs['preventa'], "unico"=>$rs['unico']]);
            }
        }
        $this->detalle=$nuevodet;

        $this->emit('msgok', "Registro ha sido eliminado del Documento de Devolución");    
    }
}

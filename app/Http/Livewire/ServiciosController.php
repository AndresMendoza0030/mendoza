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
use App\User;
use Spatie\Permission\Models\Role;

class ServiciosController extends Component
{
    use WithPagination;


	//properties
    public  $selected_id, $datos, $search='';   			//para búsquedas y fila seleccionada
    public  $action = 1;             			//manejo de ventanas
    public  $section= 1;
    private $pagination = 4;         			//paginación de tabla

    public  $detalle =[];

    public $pacienteid,$elcuarto,$elreferente,$lafechayhora,$producto_id,$lacantidad,$monto,$preciou,$nombrepaciente;
    public $numeroreg,$lproducto,$tasaiva,$cambioprecio,$registros,$preciog,$ivau,$gravado;
    public $mdproducto, $mdcantidad, $mdpreciou,$mdpreciog, $mdmonto,$mdreg, $tipopaciente;


    public function mount()
    {
        $this->numeroreg=0;
        $this->registros=0;
        $this->mdreg;
        $this->pacienteid=0;
        $this->lproducto='';
        $this->cambioprecio='NO';
        $this->detalle=collect([]);
        //
        $empresa = Generalsetup::first();
        $this->tasaiva = round($empresa->piva/100,2);
        //
        //
        $idrol=0;
        $nombrerol=auth()->user()->getRoleNames();
        //
        if ($nombrerol->count() ==1)
        {
            $rol=Role::where('name','=', $nombrerol)->first();
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
 
        if(strlen($this->lproducto) > 0)
        {
            $productos= Product::select("products.*",
            DB::raw("CONCAT(TRIM(nombre)) as producto")) 
            ->where('tipo','=','SERVICIO')->where('estatus','=','0')
            ->where('nombre', 'like', '%'. $this->lproducto .'%')
            ->orwhere('presentacion', 'like', '%'. $this->lproducto .'%')
            ->orwhere('products.id', 'like', '%'. $this->lproducto .'%')
            ->get();
        }
        else
        {
            $productos= [];
        }

        $this->registros=$this->detalle->count();
        if ($this->registros==0)
        {
            $this->numeroreg=0;
        }

        $this->productos=$productos;

        $this->crearArreglos();

        //asignamos a la propiedad clientes el resultado dela búsqueda
        return view('livewire.servicios.component');
    }

    public function crearArreglos()
    {
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

    }

    public function asgDatosPaciente($llave)
    {
        $datospac=Paciente::find($llave);
        $elmedico = Referente::find($datospac->referente);
        if ($elmedico)
        {
            $this->elreferente = TRIM($elmedico->apellidos)." ".TRIM($elmedico->apellidos);
        }
        if ($datospac->room > 0)
        {
            $this->nombrepaciente= $datospac->nombrepaciente;
            //
            $cuarto = Room::find($datospac->room);
            if ($cuarto)
            {
                $this->elcuarto = $cuarto->nombre;
            }
        }
        $this->lafechahora = date("d-m-Y", strtotime($datospac->fecha))." ".$datospac->hora;
        $this->pacienteid=$llave;
        //
        $this->emit('activa-select2');
    }

    public function asgDatosProducto($reg)
    {
        $prodJson = json_decode($reg);
        $this->lproducto=TRIM($prodJson->producto); //." ".TRIM($prodJson->presentacion);
        $this->productoid=$prodJson->id;
        $this->preciou=$prodJson->precioventa;
        $this->preciog=round($prodJson->precioventa * (1+$this->tasaiva),2);
        //
        $this->cambioprecio=$prodJson->cambioprecio;

        $this->monto=0;

       
        if ($prodJson->cambioprecio=="SI")
        {

            $this->emit('enable-unitario');
        }
        else
        {
            $this->emit('disable-unitario');
        }
    }

    public function validaCantidad($valor)
    {
        $this->lcantidad=$valor;
        $gravado = round($this->lcantidad * $this->preciou,4);
        $iva= round($gravado * $this->tasaiva,4);
        $this->monto = round($gravado+$iva,2);

        if ($this->cambioprecio=="SI")
        {

            $this->emit('enable-unitario');
        }
        else
        {
            $this->emit('disable-unitario');
        }

    }
    public function validaPrecioU($valor)
    {
        $this->preciog = round(floatval($valor),2);
        $pu = round($valor / (1+$this->tasaiva),4);
        $iv = round($valor - $pu,4);

        $this->preciou=$pu;

        $gravado = round($this->lcantidad * $this->preciou,4);
        $iva= round($gravado * $this->tasaiva,4);
        $this->monto = round($gravado+$iva,2);

        if ($this->cambioprecio=="SI")
        {

            $this->emit('enable-unitario');
        }
        else
        {
            $this->emit('disable-unitario');
        }

    }

    public function editarRegistro($reg)
    {
        $this->mdproducto  = $this->detalle[$reg]['producto'];
        $this->mdcantidad  = $this->detalle[$reg]['cantidad'];
        $this->mdpreciou   = $this->detalle[$reg]['unitario'];
        $this->mdpreciog   = $this->detalle[$reg]['unitariog'];
        $this->mdmonto     = $this->detalle[$reg]['total'];
        $this->cambioprecio= $this->detalle[$reg]['cambioprecio']; 
        $this->mdreg       = $this->detalle[$reg]['reg']; 
        //
        $this->emit('abrir-modal',['tasaiva'=> $this->tasaiva,'cambio'=>$this->cambioprecio]);

    }

    public function saveLine()
    {
        if (floatval($this->lacantidad) <=0)
        {
            $this->emit('msg-error', 'La Cantidad no puede quedar en cero....');
            return;
        }

        $this->detalle->push(["reg"=>$this->numeroreg, "productoid"=>$this->productoid, "producto"=>$this->lproducto,
        "unitario"=> $this->preciou, "unitariog"=> $this->preciog, "cvence" => '', "lotevence"=>'',"cantidad"=>$this->lacantidad, 
        "gravado"=>round($this->preciou * $this->lacantidad,4), "total"=>$this->monto, "cambioprecio"=>$this->cambioprecio]);
        $this->numeroreg+=1;
        //
        $this->lproducto='';
        $this->lacantidad='';
        $this->productoid='';
        $this->preciou=0;
        $this->preciog=0;
        $this->monto=0;
        $this->cambioprecio='NO';
        $this->emit('enable-grabar');
        //        

    }



    //método para reiniciar variables
    public function resetInput()
    {
        $this->crearArreglos();

        $this->numeroreg=0;
        $this->registros=0;
        $this->mdreg;
        $this->pacienteid=0;
        $this->lproducto='';
        $this->cambioprecio='NO';
        $this->detalle=collect([]);

        $this->selected_id = null;       
        $this->action = 1;
        $this->search = '';
        $this->elcuarto = '';
        $this->elreferente = '';
        $this->lafechahora = '';
        $this->productoid = 0;
        $this->lacantidad = '';
        $this->monto = 0;
        $this->preciou = '';
        $this->preciog='';
        $this->mdcantidad = '';
        $this->mdproducto = '';
        $this->mdmonto = '';
        $this->mdpreciou = '';
        $this->mdpreciog = '';
    }

    //escuchar eventos y ejecutar acción solicitada
    protected  $listeners = [
        'deleteRow'         => 'destroy' ,   
        'createModal'       => 'createModal',
        'resetInput'        => 'resetInput',
        'actualizaProducto' => 'actualizaProducto',
        'updLine'           => 'updLine',
    ];  


    public function updLine()
    {
        $this->preciog = $this->mdpreciog;
        $this->lcantidad=$this->mdcantidad;
        //
        $pu = round($this->preciog / (1+$this->tasaiva),4);
        $iv = round($this->preciog - $pu,4);
        $this->preciou=$pu;
        //


        $gravado = round($this->lcantidad * $this->preciou,4);
        $iva= round($gravado * $this->tasaiva,4);
        $this->monto = round($gravado+$iva,2);
        //
        $rg=$this->mdreg;
        $cn=$this->lcantidad;
        $gr=$gravado;
        $pu=$this->preciou;
        $pg=$this->preciog;
        $mt=$this->monto;
        $this->detalle->transform(function ($item) use($rg,$cn,$pu,$mt,$gr,$pg)
        {
            if ($item['reg'] == $rg)
            {
                $item['unitario'] = $pu;
                $item['unitariog'] = $pg;
                $item['cantidad'] = $cn;
                $item['gravado'] = $gr;
                $item['total'] = $mt;

            }
            return $item;
        });
        $this->emit('msgok','La línea ha sido actualizada!!!');
    }

    public function grabarBd()
    {

        $this->idusuario = 

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
            $head = InventoryHead::create([
                'bodega'        => 1,
                'paciente_id'   => $this->pacienteid,
                'bodegadestino' => 0,
                'fecha'         => Carbon::now(),
                'tipodoc'       => 'SERV',
                'estatus'       => 0,
                'user_id'       => auth()->user()->id,
                'concepto'      => "CARGO X SERVICIOS AL PACIENTE: ".$this->nombrepaciente,
            ]);      

            //Detalle Registrando Salida
            foreach($this->detalle as $det)
            {
                $produx = Product::find($det['productoid']);
                $dtcfac = ProductclassInvoice::find($produx->lineafac);

                $detabod = Movdetail::create([
                'bodega'        => $head->bodega,
                'tipodoc'       => $head->tipodoc,
                'numerodoc'     => $head->id,
                'paciente_id'   => $head->paciente_id,
                'fecha'         => $head->fecha,
                'clase'         => $produx->clase,
                'linea'         => $produx->linea,
                'lineafac'      => $produx->lineafac,
                'producto_id'   => $det['productoid'],
                'producto'      => $det['producto'],
                'cantidad'      => $det['cantidad'],
                'movimiento'    => "S",
                'tasaiva'       => $this->tasaiva*100,
                'user_id'       => $head->user_id,
                'precosto'      => $produx->costo,
                'preventa'      => $det['unitario'],
                'subtotal'      => round($det['cantidad']*$det['unitario'],4),
                'gravado'       => round($det['cantidad']*$det['unitario'],4),
                'iva'           => round(($det['cantidad']*$det['unitario'])*$this->tasaiva,4),
                'total'         => round((round($det['cantidad']*$det['unitario'],4) + round(($det['cantidad']*$det['unitario'])*$this->tasaiva,4)),2),
                'admongp'       => $dtcfac->admongp,
                'admonval'      => round(round($det['cantidad']*$det['cantidad'],4)*($dtcfac->comision/100),4),
                'estatus'       => 0
                ]);
                //
            }
                //
                
            //confirmamos transacción
            DB::commit();
            $this->resetinput();
            $this->emit('msgok', 'El Servicio ha sido guardado...');    
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

    public function descartarOperacion()
    {
        $this->resetInput();
    }

    //método para eliminar un registro dado
    public function destroy($id)
    {
        $nuevodet=collect([]);
            
        foreach($this->detalle as $rs)
        {
          
            if ($rs['reg'] != $id)
            {
                $nuevodet->push(["reg"=>$rs['numeroreg'], "productoid"=>$rs['productoid'],"producto"=>$rs['producto'],
                "unitario"=>$rs['unitario'], "unitariog"=>$rs['unitariog'],"cvence" => '', "lotevence"=>'',"cantidad"=>$rs['cantidad'], 
                "gravado"=>$rs['gravado'], "total"=>$rs['total'], "cambioprecio"=>$rs['cambioprecio']]);
              
            }
        }
        $this->detalle=$nuevodet;

        $this->emit('msgok', "Registro ha sido eliminado del Documento");    
    }
}

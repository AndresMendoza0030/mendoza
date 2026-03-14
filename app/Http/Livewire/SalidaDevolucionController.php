<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Livewire\WithPagination;
use Livewire\Component;
use App\Documento;
use App\Warehouse;
use App\Product;
use App\Productstock;
use App\ClienteProveedor;
use App\Inventoryhead;
use App\Movdetail;
use App\Vencimiento;
use App\Generalsetup;
//

use App\Sysparam;
use App\Provisionbook;
use App\Seriesbyprov;
use App\Catalogocuentas;
use App\TempoAsientos_head;
use App\TempoAsientos_book;
use App\Comprasbook;

use App\Asientos_book;
use App\Asientos_head;
use App\Asientos_corr;

class SalidaDevolucionController extends Component
{
    use WithPagination;

	//properties
    public  $selected_id, $search;   			//para búsquedas y fila seleccionada
    private $pagination = 4;         			//paginación de tabla

    public $numeroreg, $tregistros,$lotesprodsum,$mcuentacon;
    public $lproducto, $lcantidad, $lotevence, $newlote, $lpreventa,$lprecosto,$lmonto,$montot, $corrpartida; 
    public $loteid,$lstock,$tasaiva,$annoactual, $tipodoc, $tipodocumento,$mcuentaco='';
    //
    //banderas
    public $etapa=0,$todobien,$generandopda=0, $iniciado=0,$paso=0;
    //campos componente
    public $lafecha, $sujetoid, $bodega, $lsujeto, $tpdcfiscal,$fechadoc,$numerofiscal,$unicofiscal,$serie,$sujeto, $laserie;
    public $gravado, $percibido, $renta, $iva,$total,$productoid, $totalescargos,$totalesabonos,$flete,$vdescuento,$fechaprograma;
    //cursores
    public $bodegas, $productos,$proveedores,$detalle, $docuprov, $seriesprov=[], $cuentascc,$diario,$micoleccion,$reg,$cuentaprovee; 
    public $docxinv, $lacuentacc,$idcuenta,$rgcuenta,$rgcargo,$rgabono,$rgconcepto;



    public function mount()
    {
        $lafecha= date('Y-m-d');
        $this->lafecha = $lafecha;
        $this->sujetoid=0;
        $this->lsujeto='';
        $this->lproducto='';
        $this->numeroreg=0;
        $this->tregistros=0;
        $this->montot=0;
        $this->detalle=collect([]);
        $this->micoleccion=collect([]);
        $this->docxinv=collect([]);              
        $this->lotevence=[];
        $this->lotesprodsum=0;
        $this->todobien=0;
        //
        //
        $empresa = Generalsetup::first();
        $this->tasaiva = round($empresa->piva/100,2);
        //
        $Date = date("d-m-Y");  
        $this->annoactual = date("Y");
        //

    }

    //método que se ejecuta al inciar el componente
    public function render()
    {
        //
        $this->bodegas = Warehouse::where('estatus','=','0')->get();

        $this->docuprov=Documento::where('tipo','=','PRV')->select('documentos.*')->get();

        $this->cuentascc= Catalogocuentas::select("catalogocuentas_book.*") 
        ->where('estatus','=','0')->orderby('cuenta')->get();
       
        if($this->sujetoid > 0)
        {
            $this->seriesprov = Seriesbyprov::where('proveedor_id','=',$this->sujetoid)
            ->select('seriesbyprov.*')->get();
        }

        //
        if(strlen($this->lsujeto) > 0)
        {
            $this->proveedores= Clienteproveedor::select("cliente_proveedores.*") 
            ->where('tipo','=','PROVEEDOR')->where('estatus','=','0')
            ->where('razon', 'like', '%'. $this->lsujeto .'%')
            ->orwhere('nrc', 'like', '%'. $this->lsujeto .'%')
            ->orwhere('nit', 'like', '%'. $this->lsujeto .'%')
            ->get();
        }
        else
        {
            $this->proveedores= [];
        }
        //
        if(strlen($this->lproducto) > 0)
        {
            $this->productos= Product::select("products.*", DB::raw("CONCAT(TRIM(nombre)) as producto")) 
            ->where('tipo','=','PRODUCTO')->where('estatus','=','0')
            ->where('nombre', 'like', '%'. $this->lproducto .'%')
            ->orwhere('presentacion', 'like', '%'. $this->lproducto .'%')
            ->orwhere('products.id', 'like', '%'. $this->lproducto .'%')
            ->get();
        }
        else
        {
            $this->productos= [];
        }
        //asignamos a la propiedad clientes el resultado dela búsqueda
        return view('livewire.salidasdevo.component');
    }

    public function grabaMovInventario()
    {
        //validando proveedor
        if ($this->sujetoid <=0)
        {
            $this->emit('msg-error', 'Problemas con Id Proveedor');
            return;
        }
        //validando contenido de inventario
        if ($this->detalle->count()<=0)
        {
            $this->emit('msg-error', 'No HAY detalle de inventario para Grabar!!!');
            return;
        }
        //agrupando x bodeg
        //
        $tempo=collect([]);
        $ordendato=collect([]);
        $tempo=$this->detalle;
        $agrupado = $tempo->groupBy('bodega')->map(function ($group)
        {
            return
            [
                'bodega'     => $group->first()['bodega'],
                'gravado'    => $group->sum('gravado'),
            ];
        });
        //iniciamos transacción
        DB::beginTransaction();
        try
        { 
            if ($this->sujetoid=='nuevo')
            {
                $suplidor = ClienteProveedor::create([
                    'razon'                 => $this->mrazon,
                    'nit'                   => $this->mnit,
                    'dui'                   => $this->mdui,
                    'nrc'                   => $this->mnrc,
                    'giro'                  => $this->mgiro,
                    'cuentacontable'        => $this->mcuentaco,                    
                    'tipo'                  => 'PROVEEDOR',
                    'user_id'               =>auth()->user()->id,
                    'estatus'               => 0
                    ]);
                $this->sujetoid = $suplidor->id;
            }
            else
            {
                $suplidor = ClienteProveedor::find($this->sujetoid);
            }
            //
            $dcs=collect([]);
            //Head Inventario
            foreach ($agrupado as $dc)
            {
                $head = InventoryHead::create([
                'bodega'        => $dc['bodega'],
                'bodegadestino' => 0,
                'fecha'         => $this->lafecha,
                'tipodoc'       => 'CPRA',
                'sujeto_id'     => $this->sujetoid,
                'sujeto'        => $this->lsujeto,
                'estatus'       => 0,
                'concepto'      => "COMPRA A: ".$this->lsujeto." de fecha ".date("d/m/Y",strtotime($this->lafecha)),
                'user_id'       => auth()->user()->id,
                ]);
                //
                $dcs->push(["numero"=>$head->id]);
                //
    
                //Detalle Inventario
                foreach($this->detalle as $det)
                {
                    if ($det['bodega']==$dc['bodega'])
                    {
                        $milotevence=$det['loteid'];
                        //
                        if ($det['condicionlote']=='nuevo')
                        {

                            if (floatval(substr($det['lotenuevo'],0,2))>=1 && floatval(substr($det['lotenuevo'],0,2))<=12
                            && floatval(substr($det['lotenuevo'],3,4)) >= $this->annoactual)
                            {
                                $ltvc=Vencimiento::create([
                                    'bodega'        => $dc['bodega'],
                                    'producto_id'   => $det['productoid'],
                                    'mes'           => floatval(substr($det['lotenuevo'],0,2)),
                                    'anno'          => floatval(substr($det['lotenuevo'],3,4)),
                                    'entradas'      => $det['cantidad'],
                                    'total'         => $det['cantidad']
                                ]);
                                $milotevence=$ltvc->id;
                            }
                        }
                        if ($det['condicionlote']=='viejo' && $det['loteid']>0)
                        {
                            $ltvc= Vencimiento::find($det['loteid']);
                            if($ltvc)
                            {
                                $ltvc->entradas+=$det['cantidad'];
                                $ltvc->total = $ltvc->entradas - $ltvc->salidas;
                                $ltvc->save();
                                $milotevence=$ltvc->id;
                            }
                        }
                        //
    
                        $produx = Product::find($det['productoid']);
                        $detabod = Movdetail::create([
                        'bodega'        => $dc['bodega'],
                        'tipodoc'       => $head->tipodoc,
                        'numerodoc'     => $head->id,
                        'fecha'         => $head->fecha,
                        'clase'         => $produx->clase,
                        'linea'         => $produx->linea,
                        'lineafac'      => $produx->lineafac,
                        'producto_id'   => $det['productoid'],
                        'producto'      => $det['producto'],
                        'cantidad'      => $det['cantidad'],
                        'movimiento'    => "E",
                        'precosto'      => $det['pcostou'],
                        'preventa'      => $det['pventau'],
                        'subtotal'      => $det['subtotal'],
                        'gravado'       => $det['subtotal'],
                        'iva'           => $det['iva'],
                        'total'         => $det['total'],
                        'tasaiva'       => $this->tasaiva * 100,
                        'user_id'       => auth()->user()->id,
                        'lotevence'     => $milotevence, //$ltvc->id,
                        'estatus'       => 0
                        ]);
                        //
                    }
                }
            }

            //confirmamos transacción
            DB::commit();
            $this->resetinput();
            $this->emit('msgok', 'Compra ha sido guardada. . .');   
            //
            foreach ($dcs as $d)
            {
                $this->emit('print-mov',  $d['numero']);
            }

            $this->emit('confirmaOrden',  $head->id);
            //

        }
        catch (\Exception $e)
        {
            //para no tener inconsistencia de la info, deshacemos los cambios en caso de que algo falle
            DB::rollback();
            $status = $e->getMessage();       
            dd($e);     
        }
    }


    public function iniciaFiscal()
    {
        $this->etapa=1;
        $this->generandopda=1;
        $this->gravado=round($this->detalle->sum('gravado'),4);
        $this->iva=round($this->detalle->sum('iva'),2);
        $this->calculaT();
        $this->emit('noInventarioDiv',0);        
    }

    public function descartaInventario()
    {
        $this->lproducto  ='';
        $this->productoid ='';
        $this->lprecosto  ='';
        $this->lpreventa  ='';
        $this->lstock='';
        $this->lotesprodsum=0;
        $this->lotevence=[];
        $this->newlote='';
        $this->detalle=collect([]);
        $this->lsujeto='';
        $this->sujetoid='';
        $this->cuentaprovee='';
        //
        $this->etapa=0;
        $this->generandopda=0;

    }

    public function descartaFiscal()
    {
        $this->tpdcfiscal='';
        $this->fechadoc='';
        $this->numerofiscal='';
        $this->unicofiscal='';
        $this->serie='';
        $this->gravado='';
        $this->iva='';
        $this->percibido='';
        $this->renta='';
        $this->total=0;
        //
        $this->etapa=0;
        $this->generandopda=0;
        //
        $this->emit('noInventarioDiv',1);        

    }

    public function seleProducto($reg)
    {
        $prodJson = json_decode($reg);
        $this->lproducto  =TRIM($prodJson->producto); //." ".TRIM($prodJson->presentacion)
        $this->productoid =$prodJson->id;
        $this->lprecosto  =$prodJson->costo;
        $this->lpreventa  =$prodJson->precioventa;
        //
        $this->lstock='';
        $stock = Productstock::where('producto_id','=',$this->productoid)
        ->where('bodega','=',$this->bodega)->first();
        if ($stock)
        {
            $this->lstock=$stock->existencia;
        }

        //
        $this->lotesprodsum=0;
        $this->lotevence=[];

        $this->lotevence = Vencimiento::where('producto_id','=',$this->productoid)
        ->where('bodega','=',$this->bodega)->where('total','>',0)
        ->orderby('anno','asc')->orderby('mes','asc')->get();
        $this->lotesprodsum = $this->lotevence->count();
        //
        if ($this->lotesprodsum ==1)
        {
            $this->loteid = $this->lotevence[0]->id;
        }
        //
        $this->newlote='';
        //$this->emit('enable-cajas',["lote"=>$this->lotesprodsum, "muestrac"=>1]);

    }


    public function cambiaDoc($valor)
    {
        $this->tpdcfiscal=$valor;

        $documento=Documento::find($valor);
        $this->tipodoc=$documento->documento;
        $this->tipodocumento=$documento->movimiento;
        //
        if ($documento->iva=="SI")
        {
            $this->tasaiva=Generalsetup::first()->piva/100;
            $this->documentoiva=1;
        }
        if ($documento->iva=="NO")
        {
            $this->tasaiva=0.00;
            $this->documentoiva=0;
        }

    }

    public function confirmaSerie($valor)
    {
        $this->serie=$valor;
    }

    public function mostrarProveedor($id)
    {
        $reg=ClienteProveedor::find($id);
        //
        $parametros=Sysparam::first();

        if ($reg)
        {
            $this->sujetoid = $reg->id;
            $this->lsujeto  = $reg->razon;
            if ($reg->cuentacontable == null || $reg->cuentacontable=='')
            {
                $this->cuentaprovee =$parametros->cuentaprovee;
            }
            else
            {
                $this->cuentaprovee =$reg->cuentacontable;
            }
        }
        $this->emit('loteVenceMask');
    }

    public function asgBodega($valor)
    {
        $this->bodega=$valor;
    }

    public function validaNuevoLote($valor)
    {
        $this->newlote=$valor;

        $mes=floatval(substr($valor,0,2));
        $anno=floatval(substr($valor,3,4));
        if ($mes==0 && $anno==0)
        {
            $this->todobien=4;
            //$this->emit('enable-cajas',["lote"=>$this->lotesprodsum, "muestrac"=>0]);
            return;
        }

        if ($mes<1 || $mes>12)
        {
            $this->emit('msg-error','El mes del lote esta fuera de rango...');
            //$this->emit('enable-cajas',["lote"=>$this->lotesprodsum, "muestrac"=>0]);
            $this->todobien=false;
            return false;
        }
        if ($anno < $this->annoactual)
        {
            $this->emit('msg-error','El año del lote esta fuera de rango...');
            //$this->emit('enable-cajas',["lote"=>$this->lotesprodsum, "muestrac"=>0]);
            $this->todobien=false;
            return false;
        }
        $this->todobien=4;
        //this->emit('enable-cajas',["lote"=>$this->lotesprodsum, "muestrac"=>0]);        
    }


    public function validaCantidad($valor1,$valor2)
    {
        $this->lcantidad=$valor1;
        $this->lprecosto=$valor2;
        $this->lmonto= round(floatval($this->lcantidad) * floatval($this->lprecosto),4);
        //$this->emit('enable-cajas',["lote"=>$this->lotesprodsum, "muestrac"=>0]);        
    }


    public function asgLote($valor)
    {
        $this->loteid=$valor;
        
        //$this->emit('enable-cajas',["lote"=>$this->lotesprodsum, "muestrac"=>0]);        
    }

    public function saveLine()
    {
        //
        $cvence='';

        $cond="nuevo";
        if ($this->loteid == '' && $this->newlote == '')
        {
            $cvence='';
            $cond="";            
        }

        if ($this->loteid=='' && $this->newlote!='')
        {
            $cvence=$this->newlote;
            $cond="nuevo";            
        }

        if ($this->loteid > 0 && $this->newlote=='')
        {
            $vence=Vencimiento::find($this->loteid);
            $cvence=$vence->mes."/".$vence->anno;
            $cond="viejo";            
        }

        if ($this->loteid != '' && $this->newlote!='')
        {
            $cvence=$this->newlote;
            $cond="nuevo";            
        }

        //
        $sb =round($this->lprecosto * $this->lcantidad,4);
        $iv = round($sb * ($this->tasaiva),4);
        $tt = round($sb + $iv,2);
        $cb = Warehouse::find($this->bodega);
        //
        $this->detalle->push(["reg"=>$this->numeroreg, "productoid"=>$this->productoid, "producto"=>$this->lproducto,
        "cantidad"=>$this->lcantidad, "pventau"=> $this->lpreventa, "pcostou"=> $this->lprecosto,
        "subtotal"=>$sb, "iva"=>$iv, "gravado"=>$sb, "total"=>$tt,"bodega"=>$this->bodega, "cbodega"=>$cb->nombre,
        "loteid"=> $this->loteid, "lotenuevo"=>$this->newlote,"cvence" => $cvence, "condicionlote"=>$cond]);
        //
    
        $this->montot=$this->detalle->sum('monto');
        $this->numeroreg+=1;
        //
        $this->lproducto='';
        $this->bodega='';
        $this->lcantidad='';
        $this->newlote='';
        $this->loteid=0;
        $this->elprecio=0;
        $this->lmonto=0;
        $this->productoid=0;
        $this->todobien=0;
        //
    }

    //método para reiniciar variables
    public function resetInput()
    {
        $this->mrazon='';
        $this->mdui='';
        $this->mnit='';
        $this->mnrc='';
        $this->mgiro='';
        $this->cuentaco='';
        //
        $this->sujetoid='';
        $this->lsujeto='';
        $lafecha= date('Y-m-d');
        $this->lafecha = $lafecha;
        $this->bodega ='';
        $this->todobien=0;
        //
        $this->generandopda=0;
        //
        $this->gravado = 0;
        $this->iva =0;
        $this->percibido=0;
        $this->renta =0;
        $this->vdescuento =0;
        $this->flete=0;
        $this->total =0;
        //
        
        $this->lproducto='';
        $this->lcantidad='';
        $this->newlote='';
        $this->loteid=0;
        $this->elprecio=0;
        $this->lmonto=0;
        $this->etapa=0;
        $this->paso=0;
        //
        $this->totalescargos=0;
        $this->totalesabono=0;
        //
        
        $this->detalle=collect([]);
        $this->diario=collect([]);
        //
        $this->numeroreg = 0;
        $this->montot=0;
    
        $this->selected_id = null;       
        $this->action = 1;
        $this->search = '';
    }

    //escuchar eventos y ejecutar acción solicitada
    protected  $listeners = [
        'deleteRow'            => 'destroy' ,   
        'iniciarProveedor'     => 'iniciarProveedor',
        'grabaProveedor'       => 'grabaProveedor',
        'grabarRegistro'       => 'grabarRegistro',
        'validaCantidad'       => 'validaCantidad',
        'saveSerie'            => 'saveSerie' ,   
        'saveResolucion'       => 'saveResolucion' , 
        'grabaProceso'         => 'grabarBd',
        'calculaT'             => 'calculaT',
        'agregaRegistro'       => 'agregaRegistro',
        'validaCargo'          => 'validaCargo',
        'validaAbono'          => 'validaAbono',
        'editaRegistro'        => 'editaRegistro',
        'descartarOperacion'   => 'descartarOperacion',
        'asgBodega'            => 'asgBodega',
    ];  


    public function saveSerie()
    {
        $laserie=$this->laserie;

        $hayserie=Seriesbyprov::where('proveedor_id','=',$this->sujetoid)->where('serie','=',$laserie)
        ->select('seriesbyprov.*')->get();

        if ($hayserie->count()>0)
        {
            $this->emit('msg-error', "La Serie ya Existe y no puede ser Grabada!!!!!");    
        }
        else
        {
            $seriegrabada = Seriesbyprov::create ([
                'serie'         => $laserie,
                'proveedor_id'  => $this->sujetoid,
                'user_id'       => auth()->user()->id,
                'estatus'       => 0
                ]);
                //
            $this->laserie=$laserie;
            $this->serie = $seriegrabada->id;
            $this->seriesprov=Seriesbyprov::where('proveedor_id','=',$this->sujetoid)->select('seriesbyprov.*')->get();
        }
    }

    public function calculaT()
    {
        if ($this->gravado != 0)
        {
            $this->iva =round(($this->gravado- $this->vdescuento) * $this->tasaiva,4);
        }
        else
        {
            $this->iva = round(0.0000,2);
        }
        $this->totaln= round((floatval($this->gravado) + floatval($this->iva) + floatval($this->percibido) + floatval($this->flete)) - (floatval($this->renta)+floatval($this->vdescuento)) ,2);
        //
        $this->gravado=round(floatval($this->gravado),4);
        $this->iva=round(floatval($this->iva),4);
        $this->percibido=round(floatval($this->percibido),2);
        $this->renta=round(floatval($this->renta),2);
        $this->vdescuento =round(floatval($this->vdescuento),4);
        $this->flete =round(floatval($this->flete),2);        
        $this->total=round(floatval($this->totaln),2);
    }
    
    public function calculaG($valor)
    {
        $this->gravado=round(floatval($valor),4);
        $this->calculaT();
    }

    public function calculaP($valor)
    {
        $this->percibido=round(floatval($valor),2);
        $this->calculaT();
    }

    public function calculaFt($valor)
    {
        $this->flete=round(floatval($valor),2);
        $this->calculaT();
    }

    public function calculaDs($valor)
    {
        $this->vdescuento=round(floatval($valor),2);
        $this->calculaT();
    }

    public function calculaR($valor)
    {
        $this->renta=round(floatval($valor),2);
        $this->calculaT();
    }

    public function calculaI($valor)
    {
        $this->iva=round(floatval($valor),4);
        $this->calculaT();
    }

    public function iniciarProveedor()
    {
        $this->mrazon='';
        $this->mdui='';
        $this->mnit='';
        $this->mnrc='';
        $this->mgiro='';
        $this->mcuentaco='';
        $this->emit('nuevoProveedor');
    }

    public function grabaProveedor()
    {
        if ($this->mnit!='')
        {
            $validanit=ClienteProveedor::where('nit','=',$this->mnit)->where('tipo','=','PROVEEDOR')
            ->where('nit','!=','')->where('estatus','=',0)->first();
            if ($validanit)
            {
                $this->emit('msg-error','Ya hay un Proveedor: '.$validanit->razon.' con este NIT..IMPOSIBLE GRABAR');
                return;
            }
        }

        if ($this->mdui != '')
        {
            $validadui=ClienteProveedor::where('dui','=',$this->mdui)->where('tipo','=','PROVEEDOR')
            ->where('dui','!=','')->where('estatus','=',0)->first();
            if ($validadui->count()>0)
            {
                $this->emit('msg-error','Ya hay un Proveedor: '.$validadui->razon.' con este DUI..IMPOSIBLE GRABAR');
                return;
            }
        }

        $this->sujetoid='nuevo';
        $this->lsujeto = $this->mrazon;
        $this->emit('msgok', 'Datos Guardados Temporalmente.(Se Almacenaran al final del proceso de Compra)');
    }

    public function grabarBd()
    {
        //valida suma cargos/abonos
        $this->totalescargos=0;
        $this->totalesabonos=0;
        $comprobando=TempoAsientos_book::where('numero','=',$this->corrpartida)
        ->select('tempoasientos_book.*')->orderby('id')->get();
        $this->totalescargos=round($comprobando->sum('debe'),2);
        $this->totalesabonos=round($comprobando->sum('haber'),2); 
        if ($this->totalescargos <> $this->totalesabonos)
        {
            $this->emit("msgok","Partida no cuadrada, Imposible Guardar!!!");
            return ;
        }
        //validando proveedor
        if ($this->sujetoid <=0)
        {
            $this->emit('msg-error', 'Problemas con Id Proveedor');
            return;
        }
        //validando contenido de inventario
        if ($this->detalle->count()<=0)
        {
            $this->emit('msg-error', 'No HAY detalle de inventario para Grabar!!!');
            return;
        }


        //iniciamos transacción
        DB::beginTransaction();
        try { 
            if ($this->sujetoid=='nuevo')
            {
                $suplidor = ClienteProveedor::create([
                    'razon'                 => $this->mrazon,
                    'nit'                   => $this->mnit,
                    'dui'                   => $this->mdui,
                    'nrc'                   => $this->mnrc,
                    'giro'                  => $this->mgiro,
                    'cuentacontable'        => $this->mcuentaco,                    
                    'tipo'                  => 'PROVEEDOR',
                    'user_id'               =>auth()->user()->id,
                    'estatus'               => 0
                    ]);
                $this->sujetoid = $suplidor->id;
            }
            else
            {
                $suplidor = ClienteProveedor::find($this->sujetoid);
            }
            //
            //provision
            //
            $newprovision = Provisionbook::create
            ([
                'sujeto_id'     => $this->sujetoid,
                'fecha_prov'    => $this->lafecha,
                'fecha_doc'     => $this->fechadoc,
                'fechaprograma' => $this->fechaprograma,
                'serie'         => $this->serie,
                'tpdcfiscal'    => $this->tpdcfiscal,
                'numerofiscal'  => $this->numerofiscal,
                'unicofiscal'   => $this->unicofiscal,
                'gravado'       => $this->gravado,
                'iva'           => $this->iva,
                'percibido'     => $this->percibido,
                'descuento'     => $this->vdescuento,
                'flete'         => $this->flete,
                'renta'         => $this->renta,
                'total'         => $this->total,
                'saldo'         => $this->total,
                'tasaiva'       => $this->tasaiva*100,
                'estatus'       => 0,
                'user_id'       => auth()->user()->id,
            ]);
            //
            $dcs=collect([]);
            //Head Inventario
            foreach ($this->docxinv as $dc)
            {
                $head = InventoryHead::create([
                'bodega'        => $dc['bodega'],
                'bodegadestino' => 0,
                'fecha'         => $this->lafecha,
                'tipodoc'       => 'CPRA',
                'tpdocufiscal'  => $this->tipodoc,
                'numdocufiscal' => $this->numerofiscal,
                'no_provision'  => $newprovision->id,
                'sujeto_id'     => $this->sujetoid,
                'sujeto'        => $this->lsujeto,
                'estatus'       => 0,
                'concepto'      => $this->lsujeto." ".$this->tipodoc."-".$this->numerofiscal." de fecha ".date("d/m/Y",strtotime($this->fechadoc)), //$dc['concepto'],
                'user_id'       => auth()->user()->id,
                ]);
                //
                $dcs->push(["numero"=>$head->id]);
                //

                //Detalle Inventario
                foreach($this->detalle as $det)
                {
                    if ($det['bodega']==$dc['bodega'])
                    {
                        $milotevence=$det['loteid'];
                        //
                        if ($det['condicionlote']=='nuevo')
                        {

                            if (floatval(substr($det['lotenuevo'],0,2))>=1 && floatval(substr($det['lotenuevo'],0,2))<=12
                            && floatval(substr($det['lotenuevo'],3,4)) >= $this->annoactual)
                            {
                                $ltvc=Vencimiento::create([
                                    'bodega'        => $dc['bodega'],
                                    'producto_id'   => $det['productoid'],
                                    'mes'           => floatval(substr($det['lotenuevo'],0,2)),
                                    'anno'          => floatval(substr($det['lotenuevo'],3,4)),
                                    'entradas'      => $det['cantidad'],
                                    'total'         => $det['cantidad']
                                ]);
                                $milotevence=$ltvc->id;
                            }
                        }
                        if ($det['condicionlote']=='viejo' && $det['loteid']>0)
                        {
                            $ltvc= Vencimiento::find($det['loteid']);
                            if($ltvc)
                            {
                                $ltvc->entradas+=$det['cantidad'];
                                $ltvc->total = $ltvc->entradas - $ltvc->salidas;
                                $ltvc->save();
                                $milotevence=$ltvc->id;
                            }
                        }
                        //

                        $produx = Product::find($det['productoid']);
                        $detabod = Movdetail::create([
                        'bodega'        => $dc['bodega'],
                        'tipodoc'       => $head->tipodoc,
                        'numerodoc'     => $head->id,
                        'fecha'         => $head->fecha,
                        'clase'         => $produx->clase,
                        'linea'         => $produx->linea,
                        'lineafac'      => $produx->lineafac,
                        'producto_id'   => $det['productoid'],
                        'producto'      => $det['producto'],
                        'cantidad'      => $det['cantidad'],
                        'movimiento'    => "E",
                        'precosto'      => $det['pcostou'],
                        'preventa'      => $det['pventau'],
                        'subtotal'      => $det['subtotal'],
                        'gravado'       => $det['subtotal'],
                        'iva'           => $det['iva'],
                        'total'         => $det['total'],
                        'tasaiva'       => $this->tasaiva * 100,
                        'user_id'       => auth()->user()->id,
                        'lotevence'     => $milotevence, //$ltvc->id,
                        'estatus'       => 0
                        ]);
                        //
                    }
                }
            }
            //grabacion contable
            //
            $pdatemp=TempoAsientos_book::where('numero','=',$this->corrpartida)
            ->select('tempoasientos_book.*')->orderby('id')->get();
            $pdahtemp=TempoAsientos_head::find($this->corrpartida);
            //
            $lafecha  = strtotime($pdahtemp->fecha);
            $elmes = date("m", $lafecha);
            $elanno = date("Y", $lafecha);        
            $this->annodoc=$elanno;

            $corrtipo= $this->asientocorr("PROV",$elmes,$elanno);

    
            $hasiento=Asientos_head::create
            ([
                'fecha'         =>$pdahtemp->fecha,
                'tipo'          =>"PROV",
                'numero'        =>$corrtipo,
                'ejercicio'     =>$elanno, //($pdahtemp->fecha), //$this->miejercicio,
                'descripcion'   =>"PROVISION A FAVOR DE:".$this->lsujeto." / ".$this->tipodoc." ".$this->numerofiscal. " de fecha: ".$this->fechadoc,
                'user_id'       =>auth()->user()->id,
                'estatus'       =>0,
            ]);
    
            foreach ($pdatemp as $pd)
            {
                $dasiento=Asientos_book::create([
                    'tipo'          =>$hasiento->tipo,
                    'numero'        =>$hasiento->numero,
                    'patron'        =>$hasiento->id,
                    'fecha'         =>$hasiento->fecha,
                    'cuenta'        =>$pd->cuenta,
                    'nombrecuenta'  =>$pd->nombrecuenta,
                    'concepto'      =>$pd->concepto,
                    'debe'          =>$pd->debe,
                    'haber'         =>$pd->haber,
                    'ejercicio'     =>$hasiento->ejercicio, //$this->miejercicio,
                    'user_id'       =>$hasiento->user_id
                ]);
            }
            $newprovision->no_diario=$hasiento->id;
            $newprovision->save();
            //
            //
            $hdiario=TempoAsientos_head::find($this->corrpartida);
            if ($hdiario)
            {
                $hdiario->delete();
            }
            $pdas=TempoAsientos_book::where('numero','=',$this->corrpartida)->delete();
            //

            //confirmamos transacción
            DB::commit();
            $this->resetinput();
            $this->emit('msgok', 'Compra ha sido guardada. . .');   
            //
            foreach ($dcs as $d)
            {
                $this->emit('print-mov',  $d['numero']);
            }

            $this->emit('confirmaOrden',  $head->id);            
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
       
        $hdiario=TempoAsientos_head::find($this->corrpartida);
        if ($hdiario)
        {
            $hdiario->delete();
        }
        $pdas=TempoAsientos_book::where('numero','=',$this->corrpartida)->delete();

        $this->totalescargos=0;
        $this->totalesabonos=0;
        $this->etapa=1;
        $this->paso=0;

        $this->emit('msgok','Se ha descartado la información del proceso....');

        $this->emit('noFiscalDiv',1);
        $this->emit('noInventarioDiv',0);        
        
    }




    //método para eliminar un registro dado
    public function destroy($id)
    {
        $nuevodet=collect([]);
            
        foreach($this->detalle as $rs)
        {
          
            if ($rs['reg'] != $id)
            {
                $nuevodet->push(["reg"=>$rs['reg'], "productoid"=>$rs['productoid'], "producto"=>$rs['producto'],
                "cantidad"=>$rs['cantidad'], "pventau"=>$rs['pventau'], "pcostou"=>$rs['pcostou'],
                "subtotal"=>$rs['subtotal'], "iva"=>$rs['iva'], "gravado"=>$rs['gravado'], "total"=>$rs['total'],"bodega"=>$rs['bodega'],
                "cbodega"=>$rs['cbodega'], "loteid"=>$rs['loteid'], "lotenuevo"=>$rs['lotenuevo'],"cvence" =>$rs['cvence'],
                "condicionlote"=>$rs['condicionlote']]);
            }
        }
        $this->detalle=$nuevodet;
        $this->montot=$this->detalle->sum('monto');
    }

    ///
    public function preparaPda()
    {
        $parametros=Sysparam::first();
        //validaciones

        if ($this->total<=0)
        {
            $this->emit("msg-error","No Existe un monto para procesar Contablemente...");
            return;
        }
        //
        $this->micoleccion=collect([]);

        //Básicas
        $fechaprov  = strtotime($this->lafecha); //fechaprov
        $annoprov= date("Y", $fechaprov);
        $mesprov = date("m", $fechaprov);

        if ($annoprov<$parametros->annoactivoconta)
        {
            $this->emit("msg-error","El año de la provisión debe ser igual o mayor al año del ejercicio actual");
            return;
        }
        if ($mesprov<$parametros->mesctivoconta && $annoprov==$parametros->annoactivoconta)
        {
            $this->emit("msg-error","El mes de la provisión NO debe ser menor al mes contable ACTIVO!!");
            return;
        }

        if ($this->tpdcfiscal=='')
        {
            $this->emit("msg-error","El tipo de documento no puede quedar en blanco!!");
            return;
        }
        //

        if ($this->documentoiva==1)
        {
            $minumero=$this->numerofiscal;
            $miserie =$this->serie;

            if ($this->numerofiscal<=0 || $this->numerofiscal=='' || $this->numerofiscal==null)
            {
                $this->emit("msg-error","El Número del Documento no es VALIDO!!");
                return;
            }
            if ($this->serie=='' || $this->serie==null)
            {
                $this->emit("msg-error","Se requiera SERIE para documentos FISCALES!!");
                return;
            }
            $this->numerofiscal=$minumero;
            $this->serie=$miserie;

        }
        
        if ($this->documentoiva==0)
        {
            $minumero=$this->numerofiscal;
            if ($this->numerofiscal<=0 || $this->numerofiscal=='' || $this->numerofiscal==null)
            {
                $this->emit("msg-error","El Número del Documento no es VALIDO!!");
                return;
            }
            $this->numerofiscal=$minumero;
        }
        // * fin validaciones

        $this->emit('noFiscalDiv',0);
        $this->emit('noInventario',0);                

        $this->etapa=2;
        //
        $tempo=collect([]);
        $ordendato=collect([]);

        $tempo=$this->detalle;

        $agrupado = $tempo->groupBy('bodega')->map(function ($group)
        {
            return
            [
                'bodega'     => $group->first()['bodega'],
                'gravado'    => $group->sum('gravado'),
            ];
        });

       
        $concepto = $this->lsujeto." ".$this->tipodoc."-".$this->numerofiscal." de fecha ".date("d/m/Y",strtotime($this->fechadoc));


        //////////////////////////////
        //Generando Pda
        //
        $this->docxinv=$agrupado; //$cargos;

        foreach ($agrupado as $c)
        {
            $ct = Warehouse::find($c['bodega'])->cuenta;

            $lacuenta=Catalogocuentas::where('cuenta','=',$ct)->first();

            $lacuenta = $lacuenta->concepto;            
            $this->micoleccion->push(["cuenta"=>$ct,
            "nombrecuenta"=>$lacuenta, "concepto"=>$concepto,
            "cargo"=>round($c['gravado'],2),"abono"=>0,"id"=>$this->reg]);
            $this->reg=$this->reg+1;

        }

        //
        //


        if ($this->flete!=0)
        {
            $lacuenta=Catalogocuentas::where('cuenta','=',$parametros->cuentaflete)->first();
            if (!$lacuenta)
            {
                $this->emit('msg-error', "Cuenta Flete no Existe!!!");
                return;
            }
            $lacuenta = $lacuenta->concepto;            
            $this->micoleccion->push(["cuenta"=>$parametros->cuentaflete,
            "nombrecuenta"=>$lacuenta, 
            "concepto"=>"Flete en: ".$this->tipodoc.'->'.$this->numerofiscal." de ".$this->lsujeto,
            "cargo"=>$this->flete,"abono"=>0,"id"=>$this->reg]);
            $this->reg=$this->reg+1;
        }

        if ($this->vdescuento!=0)
        {
            $lacuenta=Catalogocuentas::where('cuenta','=',$parametros->cuentabodega)->first();
            if (!$lacuenta)
            {
                $this->emit('msg-error', "Cuenta Bodega Bolsón!!!");
                return;
            }
            $lacuenta = $lacuenta->concepto;            
            $this->micoleccion->push(["cuenta"=>$parametros->cuentabodega,
            "nombrecuenta"=>$lacuenta,
            "concepto"=>"Descuento a: ".$this->tipodoc.'->'.$this->numerofiscal." de ".$this->lsujeto,            
            "cargo"=>0,"abono"=>$this->vdescuento,"id"=>$this->reg]);
            $this->reg=$this->reg+1;
        }


        if ($this->iva!=0)
        {
            $lacuenta=Catalogocuentas::where('cuenta','=',$parametros->cuentacredfiscal)->first();
            if (!$lacuenta)
            {
                $this->emit('msg-error', "Cuenta Cred. Fiscal no Existe!!!");
                return;
            }
            $lacuenta = $lacuenta->concepto;            
            $this->micoleccion->push(["cuenta"=>$parametros->cuentacredfiscal,
            "nombrecuenta"=>$lacuenta, "concepto"=>$this->lsujeto.'/'.$this->tipodoc.': '.$this->numerofiscal,
            "cargo"=>$this->iva,"abono"=>0,"id"=>$this->reg]);
            $this->reg=$this->reg+1;
        }
        if ($this->percibido!=0)
        {
            $lacuenta=Catalogocuentas::where('cuenta','=',$parametros->cuentapercibecompra)->first();
            if (!$lacuenta)
            {
                $this->emit('msg-error', "Cuenta Percepción no Existe!!!");
                return;
            }
            $lacuenta = $lacuenta->concepto;            
            $this->micoleccion->push(["cuenta"=>$parametros->cuentapercibecompra,
            "nombrecuenta"=>$lacuenta, "concepto"=>$this->lsujeto.'/'.$this->tipodoc.': '.$this->numerofiscal,
            "cargo"=>$this->percibido,"abono"=>0,"id"=>$this->reg]);
            $this->reg=$this->reg+1;
        }

        if ($this->renta!=0)
        {
            $lacuenta=Catalogocuentas::where('cuenta','=',$parametros->cuentarentaretenida)->first();
            if (!$lacuenta)
            {
                $this->emit('msg-error', "Cuenta Renta no Existe!!!");
                return;
            }
            $lacuenta = $lacuenta->concepto;            
            $this->micoleccion->push(["cuenta"=>$parametros->cuentarentaretenida,
            "nombrecuenta"=>$lacuenta, "concepto"=>$this->sujeto.'/'.$this->tipodoc.': '.$this->numerofiscal,
            "cargo"=>0,"abono"=>$this->renta,"id"=>$this->reg]);
            $this->reg=$this->reg+1;
        }

        if ($this->totaln!=0)
        {
            if ($this->mcuentaco != '')
            {
                $ctprov=$this->mcuentaco;
            }
            else
            {
                $ctprov =     $parametros->cuentaprovee;       
            }
            //

            if ($this->cuentaprovee=="" || $this->cuentaprovee==null)
            {
                $lacuenta=Catalogocuentas::where('cuenta','=',$ctprov)->first();
                if (!$lacuenta)
                {
                    $this->emit('msg-error', "Cuenta Proveedor Bolsón no Existe!!!");
                    return;
                }
                $lacuenta = $lacuenta->concepto; 
                $ctprov =     $parametros->cuentaprovee;       
            }
            else
            {
                $lacuenta=Catalogocuentas::where('cuenta','=',$this->cuentaprovee)->first();
                if (!$lacuenta)
                {
                    $this->emit('msg-error', "Cuenta Proveedor no Existe!!!");
                    return;
                }
                $lacuenta = $lacuenta->concepto;   
                $ctprov = $this->cuentaprovee;         
            }

            $this->micoleccion->push(["cuenta"=>$ctprov,
            "nombrecuenta"=>$lacuenta, "concepto"=>$this->tipodoc.'/'.$this->numerofiscal,
            "cargo"=>0,"abono"=>$this->totaln,"id"=>$this->reg]);
            $this->reg=$this->reg+1;
        }

        $this->diario = json_decode($this->micoleccion);

        //
        $headtempo = TempoAsientos_head::create([
            'fecha'         =>$this->lafecha,
            'user_id'       =>auth()->user()->id,
            'descripcion'   =>$this->lsujeto." ".$this->tipodoc."/".$this->numerofiscal,
        ]);
        $this->corrpartida = $headtempo->id;

        foreach ($this->diario as $d)
        {
            $xcuenta=Catalogocuentas::where('cuenta','=',$d->cuenta)->first();
            if ($xcuenta==null)      
            {
                $micuenta="";
            }
            else
            {
                $micuenta = $xcuenta->concepto;      
            }
            //
            $regmov = TempoAsientos_book::create
            ([
                'numero'                => $headtempo->id,
                'fecha'                 => $headtempo->fecha,
                'cuenta'                => $d->cuenta,
                'nombrecuenta'          => $micuenta,
                'concepto'              => $d->concepto,
                'debe'                  => $d->cargo,
                'haber'                 => $d->abono,
                'user_id'               => $headtempo->user_id,
                'estatus'               => 0
            ]);
        }
        //
        $this->paso=1;
        //
        $this->diario=TempoAsientos_book::where('numero','=',$headtempo->id)
        ->select('tempoasientos_book.*')->orderby('id')->get();
        //
        $this->totalescargos=$this->diario->sum('debe');
        $this->totalesabonos=$this->diario->sum('haber');            
        //
    }

    public function editaRegistro($id)
    {
        $record = TempoAsientos_book::find($id);
        if ($record)
        {

            $this->idcuenta='';
            $this->lacuentacc='';
            $this->rgcuenta='';
            $lacuenta = Catalogocuentas::where('cuenta','=',$record->cuenta)->first();
            if ($lacuenta)
            {

                $this->idcuenta=$lacuenta->id;
                $this->lacuentacc=$lacuenta->cuenta;
                $this->rgcuenta=$lacuenta->concepto;
            }
            $this->rgcargo=$record->debe;
            $this->rgabono=$record->haber;
            $this->rgconcepto=$record->concepto;
            //
            $this->modoagregar=1;
            $this->editarcrear=$id;
            $this->emit('abreModalRg','');

        }
    }

    public function agregaRegistro()
    {
        $this->idcuenta='';
        $this->lacuentacc='';
        $this->rgcuenta='';
        $this->rgcargo=0;
        $this->rgabono=0;
        $this->rgconcepto='';
        //
        $this->modoagregar=1;
        $this->editarcrear=0;
        $this->emit('abreModalRg','');
    }

    public function grabarRegistro($lacuentamodal)
    {
        $this->lacuentacc=TRIM($lacuentamodal);
        $idcuenta=Catalogocuentas::where('cuenta','=',$lacuentamodal)->first();
        if ($idcuenta)
        {
            $this->idcuenta=$idcuenta->id;
            $this->rgcuenta=$idcuenta->concepto;
        }
        //

        if ($this->modoagregar==1 && $this->editarcrear==0)
        {
            $regnuevo = TempoAsientos_book::create ([
                'numero'                => $this->corrpartida,
                'fecha'                 => $this->fechaprov,
                'cuenta'                => $this->lacuentacc,
                'nombrecuenta'          => $this->rgcuenta,
                'concepto'              => $this->rgconcepto,
                'debe'                  => $this->rgcargo,
                'haber'                 => $this->rgabono,
                'user_id'               => auth()->user()->id,
                'estatus'               => 0

            ]);
            //
            $this->diario=TempoAsientos_book::where('numero','=',$this->corrpartida)
            ->select('tempoasientos_book.*')->orderby('id')->get();
            $this->modoagregar=0;
            $this->totalescargos=$this->diario->sum('debe');
            $this->totalesabonos=$this->diario->sum('haber'); 
            //$this->emit('ocultargpda');
        }
        //
        if ($this->modoagregar==1 && $this->editarcrear!=0)
        {
            $regedit = TempoAsientos_book::find($this->editarcrear);
            if ($regedit)
            {
                $regedit->cuenta=$this->lacuentacc;
                $regedit->nombrecuenta = $this->rgcuenta;
                $regedit->concepto= $this->rgconcepto;
                $regedit->debe= $this->rgcargo;
                $regedit->haber=$this->rgabono;
                $regedit->save();
                //
                $this->diario=TempoAsientos_book::where('numero','=',$this->corrpartida)
                ->select('tempoasientos_book.*')->orderby('id')->get();
                $this->modoagregar=0;
                $this->totalescargos=$this->diario->sum('debe');
                $this->totalesabonos=$this->diario->sum('haber'); 
                //
            }
        }
    }

    public function asientocorr($tipo,$mes,$an)
    {
        $corrpda=Asientos_corr::where('tipo','=',"PROV")
        ->where('ejercicio','=',$an)->first();

        switch ($mes)
        {
            case '1':
                $mipda=$corrpda->enero+1;
                $corrpda->enero=$corrpda->enero+1;
                $corrpda->save();
                break;
            case '2':
                $mipda=$corrpda->febrero+1;
                $corrpda->febrero=$corrpda->febrero+1;
                $corrpda->save();
                break;
            case '3':
                $mipda=$corrpda->marzo+1;
                $corrpda->marzo=$corrpda->marzo+1;
                $corrpda->save();
                break;
            case '4':
                $mipda=$corrpda->abril+1;
                $corrpda->abril=$corrpda->abril+1;
                $corrpda->save();
                break;
            case '5':
                $mipda=$corrpda->mayo+1;
                $corrpda->mayo=$corrpda->mayo+1;
                $corrpda->save();
                break;
            case '6':
                $mipda=$corrpda->junio+1;
                $corrpda->junio=$corrpda->junio+1;
                $corrpda->save();
                break;
            case '7':
                $mipda=$corrpda->julio+1;
                $corrpda->julio=$corrpda->julio+1;
                $corrpda->save();
                break;
            case '8':
                $mipda=$corrpda->agosto+1;
                $corrpda->agosto=$corrpda->agosto+1;
                $corrpda->save();
                break;
            case '9':
                $mipda=$corrpda->septiembre+1;
                $corrpda->septiembre=$corrpda->septiembre+1;
                $corrpda->save();
                break;
            case '10':
                $mipda=$corrpda->octubre+1;
                $corrpda->octubre=$corrpda->octubre+1;
                $corrpda->save();
                break;
            case '11':
                $mipda=$corrpda->noviembre+1;
                $corrpda->noviembre=$corrpda->noviembre+1;
                $corrpda->save();
                break;
            case '12':
                $mipda=$corrpda->diciembre+1;
                $corrpda->diciembre=$corrpda->diciembre+1;
                $corrpda->save();
                break;
        }
        $this->nuevapda=$mipda;
        return $mipda;
    }

    public function elmes($m)
    {
        switch ($m)
        {
            case '1':
                return 'ENERO';
                break;
            case '2':
                return 'FEBRERO';
                break;
            case '3':
                return 'MARZO';
                break;
            case '4':
                return 'ABRIL';
                break;
            case '5':
                return 'MAYO';
                break;
            case '6':
                return 'JUNIO';
                break;
            case '7':
                return 'JULIO';
                break;
            case '8':
                return 'AGOSTO';
                break;
            case '9':
                return 'SEPTIEMBRE';
                break;
            case '10':
                return 'OCTUBRE';
                break;
            case '11':
                return 'NOVIEMBRE';
                break;
            case '12':
                return 'DICIEMBRE';
                break;
    
        }
    }


}

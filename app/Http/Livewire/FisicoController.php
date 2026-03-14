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
use App\Productstock;
use App\Inventoryhead;
use App\Movdetail;
use App\Fisico;
use App\Vencimiento;
use App\VenceFisico;
use App\Conteo;


class FisicoController extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';


	//properties
    private $pagination = 7;         			//paginación de tabla
    private $info;
    private $elconteo,$vconteo;

    public  $desde, $hasta,$action,$mconteo,$lfecha,$movno,$productos,$solovence;
    public $fvences=[];
 
    public  $labodega,$lbodega,$bodegas,$lconcepto,$modoGrabar,$vct;

    public $search,$search2,$nbodega,$mdp,$lproducto,$idproducto,$lcantidad;


    public function mount()
    {
        $fecha= date('Y-m-d');
        $this->lfecha=$fecha;
        $this->desde = $fecha;
        $this->hasta = $fecha;
        $this->labodega='';
        $this->lbodega='';
        $this->action=1;
        $this->mconteo='';
        $this->elconteo=[];
        $this->vconteo=[];        
        $this->modoGrabar="OFF";
    }

    //método que se ejecuta al inciar el componente
    public function render()
    {
        $this->bodegas      = Warehouse::where('estatus','=','0')->get();        

        if($this->mconteo=="S")
        {
            if(strlen($this->search2) > 0)
                {
          
                $this->elconteo = Fisico::leftjoin('warehouses as bd','bodega_id','=','bd.id')
                ->leftjoin('products as pr','producto_id','=','pr.id')
                ->leftjoin('productclasses as cla','cla.id','=','pr.clase')
                ->where('pr.nombre', 'like', '%' .  $this->search2 . '%')
                ->where('conteo_id','=',$this->movno)
                ->select('inv_fisico.*','cla.nombre as laclase',DB::raw("0 as total"),
                DB::raw("CONCAT(TRIM(pr.nombre),' ',TRIM(pr.presentacion)) as producto"))
                ->orderby('pr.clase')->orderby('pr.nombre')->orderby('pr.presentacion')
                ->paginate($this->pagination);

                foreach ($this->elconteo as $cc)
                {
                    $tvence = VenceFisico::where('idconteo','=',$this->movno)
                    ->where('producto_id','=',$cc->producto_id)
                    ->select('conteo')->get();
                    $vtotal=$tvence->sum('conteo');
                    if ($vtotal>0)
                        {$cc->total=$vtotal;}
                }

                $this->fvences = VenceFisico::where('idconteo','=',$this->movno)
                ->select('vence_fisico.*')
                ->get();

                }
            else
                {
                    $this->elconteo = Fisico::leftjoin('warehouses as bd','bodega_id','=','bd.id')
                    ->leftjoin('products as pr','producto_id','=','pr.id')
                    ->leftjoin('productclasses as cla','cla.id','=','pr.clase')
                    ->where('conteo_id','=',$this->movno)
                    ->select('inv_fisico.*','cla.nombre as laclase',DB::raw("0 as total"),
                    DB::raw("CONCAT(TRIM(pr.nombre),' ',TRIM(pr.presentacion)) as producto"))
                    ->orderby('pr.clase')->orderby('pr.nombre')->orderby('pr.presentacion')
                    ->paginate($this->pagination);

                    foreach ($this->elconteo as $cc)
                    {
                        $tvence = VenceFisico::where('idconteo','=',$this->movno)
                        ->where('producto_id','=',$cc->producto_id)
                        ->select('conteo')->get();
                        $vtotal=$tvence->sum('conteo');
                        if ($vtotal>0)
                            {$cc->total=$vtotal;}
                    }

                    $this->fvences = VenceFisico::where('idconteo','=',$this->movno)
                    ->select('vence_fisico.*')
                    ->get();
                }
        }
        else
        {
            if(strlen($this->search) > 0)
            {
                $this->info = Conteo::leftjoin('warehouses as bd','conteo_head.bodega_id','=','bd.id')
                ->leftjoin('users as us','conteo_head.user_id','=','us.id')
                ->select("conteo_head.*",'bd.nombre as cbodega','us.email as usuario')
                ->where('conteo_head.estatus','=',0)
                ->where('bd.nombre', 'like', '%' .  $this->search . '%')
                ->whereBetween('fecha',[$this->desde, $this->hasta])
                ->orderby('conteo_head.id')->paginate($this->pagination);
            }
            else
            {
                $this->info = Conteo::leftjoin('warehouses as bd','conteo_head.bodega_id','=','bd.id')
                ->leftjoin('users as us','conteo_head.user_id','=','us.id')                
                ->select("conteo_head.*",'bd.nombre as cbodega','us.email as usuario')
                ->where('conteo_head.estatus','=',0)
                ->whereBetween('fecha',[$this->desde, $this->hasta])
                ->orderby('conteo_head.id')->paginate($this->pagination);
            }
        }

        if(strlen($this->lproducto) > 0)
        {
            $productos= Product::select("products.*",
            DB::raw("CONCAT(TRIM(nombre),' ',TRIM(presentacion)) as producto")) 
            ->where('tipo','=','PRODUCTO')->where('estatus','=','0')
            ->where('nombre', 'like', '%'. $this->lproducto .'%')
            ->orwhere('presentacion', 'like', '%'. $this->lproducto .'%')
            ->orwhere('id', 'like', '%'. $this->lproducto .'%')
            ->get();
        }
        else
        {
            $productos= [];
        }
        $this->productos=$productos;

        return view('livewire.fisico.component',[
            'info' => $this->info, 'elconteo' => $this->elconteo,
        'vconteo' => $this->vconteo,]);
    }

    //permite la búsqueda cuando se navega entre el paginado
    public function updatingSearch(): void
    {
        $this->gotoPage(1);
    }


    public function seleProducto($reg)
    {
        $prodJson = json_decode($reg);
        $this->lproducto=$prodJson->producto;
        $this->idproducto=$prodJson->id;
        //
        $yaexiste= Fisico::where('conteo_id',$this->movno)->where('producto_id',$prodJson->id)->get();
        if ($yaexiste && $yaexiste->count()>=1)
        {
            $this->emit('msgok',"Ya existe ese código dentro del Conteo...");
            $this->vct="";
            $this->lproducto='';
            $this->idproducto='';
            return;
        }
        //
        $this->vct="S";
    }


    public function agregaRegistro()
    {
        $this->mdp="S";
    }

    public function noAgregaRegistro()
    {
        $this->mdp="";
    }

    public function grabaRegistro()
    {
        $hd= Conteo::find($this->movno);
        if ($hd)
        {
            $miconteo = Fisico::create([
                'conteo_id'     => $this->movno,
                'bodega_id'     => $hd->bodega_id,
                'producto_id'   => $this->idproducto,
                'actual'        => 0,
                'contado'       => $this->lcantidad,
                'diferencia'     => $this->lcantidad*-1,
                'tipoajuste'    => "E",
                'estatus'       => 0,
                'user_id'       => $hd->user_id,
            ]);

/*             $this->conteo = Fisico::leftjoin('warehouses as bd','bodega_id','=','bd.id')
            ->leftjoin('products as pr','producto_id','=','pr.id')
            ->where('conteo_id','=',$this->movno)->select('inv_fisico.*', 'bd.nombre as cbodega',
            DB::raw("CONCAT(TRIM(pr.nombre),' ',TRIM(pr.presentacion)) as producto"))
            ->orderby('pr.nombre')->orderby('pr.presentacion')->get();
 */           
            $this->mconteo="S";
            $this->mdp="";
            $this->vct="";
            $this->lcantidad='';
            $this->lproducto='';
            $this->iproducto='';
                
        }
    }

    public function procesaConteo($idm)
    {
        $conteo = Conteo::find($idm);
        if ($conteo)
        {
            //vencimientos
            $entravence = VenceFisico::where('idconteo','=',$idm)
            ->where('tipomovi','=',"E")->select('vence_fisico.*')->get();
            $salevence = VenceFisico::where('idconteo','=',$idm)
            ->where('tipomovi','=',"S")->select('vence_fisico.*')->get();
            
            $entradas = Fisico::where('conteo_id','=',$idm)
            ->where('tipoajuste','=','E')->get();
            $salidas = Fisico::where('conteo_id','=',$idm)
            ->where('tipoajuste','=','S')->get();
            //iniciamos transacción
            DB::beginTransaction();
            try
            { 
            //ajuste entrada
            if ($entravence->count() >0)
            {
                foreach($entravence as $evc)
                {
    
                    if ($evc->idvence == null) //nuevo vencimiento
                    {
                        $newvence = Vencimiento::create([
                            'bodega'        => $conteo->bodega_id,
                            'producto_id'   => $evc->producto_id,
                            'mes'           => $evc->mes,
                            'anno'          => $evc->anno,
                            'entradas'      => $evc->conteo,
                            'total'         => $evc->conteo,
                        ]);      
                    }
                    else
                    {
                        $entrav = Vencimiento::find($evc->idvence);
                        if ($entrav)
                        {
                            $entrav->entradas = $entrav->entradas + abs($evc->diferencia);
                            $entrav->total = $entrav->entradas - $entrav->salidas;
                            $entrav->save();
                            $procev = VenceFisico::find($evc->id);
                            if ($procev)
                            {
                                $procev->procesado="S";
                                $procev->save();
                            }
                        }
                    }
                }
            }
            if ($salevence->count() >0)
            {
                foreach($salevence as $svc)
                {
                    $salev = Vencimiento::find($svc->idvence);
                    if ($salev)
                    {
                        $salev->salidas = $salev->salidas + abs($svc->diferencia);
                        $salev->total = $salev->entradas - $salev->salidas;
                        $salev->save();
                        $procev = VenceFisico::find($svc->id);
                        if ($procev)
                        {
                            $procev->procesado="S";
                            $procev->save();
                        }
                    }
                }
            }

            if ($entradas->count() >0)
            {
                $eltipo="EAJU";
                $elmov="E";
                //Head Registrando Entrada
                $heade = InventoryHead::create([
                'bodega'        => $conteo->bodega_id,
                'bodegadestino' => 0,
                'fecha'         => Carbon::now(),
                'tipodoc'       => $eltipo,
                'estatus'       => 0,
                'user_id'       => auth()->user()->id,
                'concepto'      => "AJUSTE INVENTARIO POR CONTEO No.".$idm,
                ]);      
                //Detalle Registrando Entrada
                foreach($entradas as $det)
                {
                    $produx = Product::find($det->producto_id);
                    $detabod = Movdetail::create([
                    'bodega'        => $heade->bodega,
                    'tipodoc'       => $heade->tipodoc,
                    'numerodoc'     => $heade->id,
                    'fecha'         => $heade->fecha,
                    'clase'         => $produx->clase,
                    'linea'         => $produx->linea,
                    'lineafac'      => $produx->lineafac,
                    'producto_id'   => $det->producto_id,
                    'producto'      => TRIM($produx->nombre)." ".TRIM($produx->presentacion),
                    'cantidad'      => abs($det->diferencia),
                    'movimiento'    => $elmov,
                    'precosto'      => $produx->costo,
                    'subtotal'      => abs($det->diferencia)*$produx->costo,
                    'gravado'       => abs($det->diferencia)*$produx->costo,
                    'user_id'       => auth()->user()->id,
                    'lotevence'     => 0,
                    'estatus'       => 0
                    ]);
                    //
                }
            } //fin de entradas

            //ajuste salidas
            if ($salidas->count() >0)
            {
                $eltipo="SAJU";
                $elmov="S";
                //Head Registrando Entrada
                $heads = InventoryHead::create([
                'bodega'        => $conteo->bodega_id,
                'bodegadestino' => 0,
                'fecha'         => Carbon::now(),
                'tipodoc'       => $eltipo,
                'estatus'       => 0,
                'user_id'       => auth()->user()->id,
                'concepto'      => "AJUSTE INVENTARIO POR CONTEO No.".$idm,
                ]);      
                //Detalle Registrando Entrada
                foreach($salidas as $det)
                {
                    $produx = Product::find($det->producto_id);
                    $detabod = Movdetail::create([
                    'bodega'        => $heads->bodega,
                    'tipodoc'       => $heads->tipodoc,
                    'numerodoc'     => $heads->id,
                    'fecha'         => $heads->fecha,
                    'clase'         => $produx->clase,
                    'linea'         => $produx->linea,
                    'lineafac'      => $produx->lineafac,
                    'producto_id'   => $det->producto_id,
                    'producto'      => TRIM($produx->nombre)." ".TRIM($produx->presentacion),
                    'cantidad'      => abs($det->diferencia),
                    'movimiento'    => $elmov,
                    'precosto'      => $produx->costo,
                    'subtotal'      => abs($det->diferencia)*$produx->costo,
                    'gravado'       => abs($det->diferencia)*$produx->costo,
                    'user_id'       => auth()->user()->id,
                    'lotevence'     => 0,
                    'estatus'       => 0
                    ]);
                    //
                }
            } //fin de salidas
            $confirma=Conteo::find($idm);
            if ($confirma)
            {
                $confirma->procesado="S";
                $confirma->save();
            }
            //confirmamos transacción
            DB::commit();
            $this->emit('msgok', 'Los Ajustes han sido generados. . .');
                if ($entradas->count() >0)
                {
                $tmov="Entrada";
                $this->emit('print-mov',  $heade->id);
                }

                if ($salidas->count() >0)
                {
                $tmov="Salida";                    
                $this->emit('print-mov',  $heads->id);
                }


                $this->emit('confirmaOrden');

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

    public function imprimeConteo($id)
    {
        $this->emit('printconteo', ['idmov' =>$id]);
        $this->emit('msgok', 'El Conteo ha sido Impreso. . .');
    }

    public function imprimeConteoVence($id)
    {
        $this->emit('printconteovence', ['idmov' =>$id]);
        $this->emit('msgok', 'El Conteo de Vencimientos ha sido Impreso. . .');
    }

    public function preparaConteo()
    {
        $okbod = Warehouse::find($this->lbodega);
        if(!$okbod)
        {
            $this->emit('msg-error', 'La Bodega Ingresada no ES Válida...');    
            return ;
        }

        $this->search2='';
        $this->search='';        
        $this->mconteo="";
        $this->emit('mascaranumero');    
        //

        //
        $detalle=collect([]);
        $prodsactivo=Product::leftjoin('productclasses as pcl', 'pcl.id','products.clase')
        ->select('products.*','pcl.nombre as cclase',
        DB::raw("CONCAT(TRIM(products.nombre),' ',TRIM(products.presentacion)) as producto"))
        ->where('activo','S')->orderby('clase')->orderby('linea')->orderby('producto')->get();
        //
        foreach ($prodsactivo as $p)
        {
            $vexi=0;
            $ex=Productstock::where('producto_id','$p->id')->where('bodega',$this->lbodega)->first();
            if ($ex)
            {
                $vexi = $ex->existencia;
            }
            //
            $detalle->push(["id"=>$p->id, "producto"=>$p->producto, "laclase"=>$p->cclase,
            "clase"=>$p->clase, "linea"=>$p->linea,"existencia"=>$vexi,"cuantos"=>'']);            
        }
        //
        $vencebod = Vencimiento::where('bodega','=',$this->lbodega)
        ->where('total','>',0)->select('vencimientos.*')
        ->get();


        //iniciamos transacción
        DB::beginTransaction();
        try
        { 
            $contehead = Conteo::create([
                'fecha'         => $this->lfecha,
                'bodega_id'     => $this->lbodega,
                'concepto'      => $this->lconcepto,
                'estatus'       => 0,
                'user_id'       => auth()->user()->id,
            ]);
            //Detalle Registrando Salida
            $this->movno=$contehead->id;
            foreach($detalle as $dt)
            {

                $miconteo = Fisico::create([
                'conteo_id'     => $contehead->id,
                'bodega_id'     => $contehead->bodega_id,
                'producto_id'   => $dt['id'],
                'actual'        => $dt['existencia'],
                'contado'       => $dt['existencia'],
                'estatus'       => 0,
                'user_id'       => $contehead->user_id,
                 ]);
            }
            //creando vencimienntos al momento
            //confirmamos transacción
            DB::commit();
        }
        catch (\Exception $e)
        {
            //para no tener inconsistencia de la info, deshacemos los cambios en caso de que algo falle
            DB::rollback();
            $status = $e->getMessage();       
            dd($e);     
        }
        $this->mconteo="S";
    }


    public function verAjuste($idm)
    {
        $this->vconteo = Fisico::leftjoin('warehouses as bd','bodega_id','=','bd.id')
        ->leftjoin('products as pr','producto_id','=','pr.id')
        ->where('conteo_id','=',$idm)
        ->select('inv_fisico.*',DB::raw("0 as total"),
        DB::raw("CONCAT(TRIM(pr.nombre),' ',TRIM(pr.presentacion)) as producto"))
        ->orderby('pr.nombre')->orderby('pr.presentacion')
        ->paginate($this->pagination);

        if ($this->vconteo->count() ==0)
        {
            //armar vista conteo solo para upd vences //
            $this->solovence="S";
            //
            $mic = Conteo::find($idm);
            $this->lbodega=$mic->bodega_id;
            //
            $this->vconteo = VenceFisico::where('idconteo','=',$idm)
            ->leftjoin('productstocks','vence_fisico.producto_id','=','productstocks.producto_id')
            ->leftjoin('products as pr','vence_fisico.producto_id','=','pr.id')
            ->where('productstocks.bodega','=',$this->lbodega)
            ->select('vence_fisico.producto_id', 'vence_fisico.mes','vence_fisico.anno',
            'productstocks.existencia as actual',
            DB::raw("CONCAT(TRIM(pr.nombre),' ',TRIM(pr.presentacion)) as producto"))
            ->orderby('pr.nombre')->orderby('pr.presentacion')
            ->paginate($this->pagination);
        
            //
        }
        else
        {
            $this->solovence="N";
            foreach ($this->vconteo as $cc)
            {
                $tvence = VenceFisico::where('idconteo','=',$idm)
                ->where('producto_id','=',$cc->producto_id)
                ->select('conteo')->get();
                $vtotal=$tvence->sum('conteo');
                if ($vtotal>0)
                    {$cc->total=$vtotal;}
            }
            $this->fvences = VenceFisico::where('idconteo','=',$this->movno)
            ->select('vence_fisico.*')
            ->get();
        }


        $mic = Conteo::find($idm);
        $this->nbodega=Warehouse::find($mic->bodega_id);
        $this->nbodega=$this->nbodega->nombre;

        $this->action=4;
    }

    public function grabaParcial()
    {
        //upd head
        $hdconteo=Conteo::find($this->movno);
        if ($hdconteo)
        {
            $hdconteo->concepto=$this->lconcepto;
            $hdconteo->save();
        }
        //
        $this->movno=0;        
        $this->mconteo="";
        $this->action=1;
        $this->lbodega='';
        $this->lconcepto='';
        $this->elconteo=[];
    }

    public function editarConteo($id)
    {
        $this->movno=$id;
        $mic = Conteo::find($id);
        if ($mic)
        {
            if ($mic->cerrado=="S")
            {
                $this->mconteo="";
                $this->action=1;
                $this->lbodega='';
                $this->lconcepto='';
                $this->emit('msg-error','El Conteo ya fue Finalizado Anteriormente');
                return;
            }

            $this->elconteo = Fisico::leftjoin('warehouses as bd','bodega_id','=','bd.id')
            ->leftjoin('products as pr','producto_id','=','pr.id')
            ->leftjoin('productclasses as cla','cla.id','=','pr.clase')
            ->where('conteo_id','=',$this->movno)
            ->select('inv_fisico.*','cla.nombre as laclase',DB::raw("CONCAT(TRIM(pr.nombre),' ',TRIM(pr.presentacion)) as producto"))
            ->orderby('pr.clase')->orderby('pr.nombre')->orderby('pr.presentacion')
            ->paginate($this->pagination);
            //
            $this->mconteo='S';
            $this->lbodega=$mic->bodega_id;
            $this->lconcepto=$mic->concepto;
            $this->action=2;
        }
        
    }


    public function grabarConteo()
    {
/*         $this->elconteo = Fisico::where('conteo_id','=',$this->movno)
        ->where('diferencia','=',null)->where('tipoajuste','=',null)
        ->delete();

        $this->fvences = VenceFisico::where('idconteo','=',$this->movno)
        ->where('diferencia','=',null)->where('tipomovi','=',null)
        ->delete();

        $this->elconteo = Fisico::where('conteo_id','=',$this->movno)
        ->where('diferencia','!=',null)->where('tipoajuste','!=',null)
        ->get();

        $this->fvences = VenceFisico::where('idconteo','=',$this->movno)
        ->where('diferencia','!=',null)->where('tipomovi','!=',null)
        ->get();


        if ($this->elconteo->count() ==0  && $this->fvences->count() ==0)
        {
            $cth = Conteo::find($this->movno)->delete();
        }
 */
    $hdconteo=Conteo::find($this->movno);
    if ($hdconteo)
    {
        $hdconteo->concepto=$this->lconcepto;
        $hdconteo->cerrado="S";
        $hdconteo->save();
    }
    $this->mconteo="";
    $this->action=1;
    $this->lbodega='';
    $this->lconcepto='';

    }

    public function descartarConteo($id)
    {
        $this->elconteo = Fisico::where('conteo_id','=',$this->movno)
        ->delete();
        $this->fvences = VenceFisico::where('idconteo','=',$this->movno)
        ->delete();

        $cth = Conteo::find($this->movno)->delete();

        $this->mconteo="";
        $this->action=1;
        $this->lbodega='';
        $this->lconcepto='';
    }

    public function borrarConteo($idc)
    {
        $this->conteo = Conteo::find($idc);

        if ($this->conteo->procesado == "S")
        {
            $this->emit('msg-error', 'Conteo ya procesado, Imposible Borrar.');    
            return;
        }

        $this->conteo->delete();
        $cth = Fisico::where('conteo_id','=',$idc)->delete();
        $this->fvences = VenceFisico::where('idconteo','=',$idc)
        ->delete();

        $this->emit('msgok', 'El Conteo ha sido Borrado. . .');    


    }


    //método para eliminar un registro dado
    public function doAction($accion)
    {
        $this->action=$accion;
    }

    public function updVencimiento($updid,$updconteo,$upddif,$updtipo)
    {
        $updvence=VenceFisico::find($updid);
        if ($updvence)
        {
            $updvence->conteo=$updconteo;
            $updvence->diferencia=$upddif;
            $updvence->tipomovi=$updtipo;
            $updvence->save();
        }
    }
    
    public function grabaVencimiento($xconteo,$xmes,$xanno,$xidprod)
    {
        $gbfecha = VenceFisico::create([
            'idconteo'      => $this->movno,
            'producto_id'   => $xidprod,
            'mes'           => $xmes,
            'anno'          => $xanno,
            'conteo'        => $xconteo,
            'diferencia'    => $xconteo,
            'tipomovi'      => "E",
            'user_id'       => auth()->user()->id,
        ]);

    }

    public function updConteo($valor,$id)
    {
        $updcontar=Fisico::find($id);
        $updcontar->contado=$valor;
        $updcontar->diferencia = $updcontar->actual - $updcontar->contado;
        if ($updcontar->diferencia >0)
        {
            $updcontar->tipoajuste="S";
        }
        else
        {
            $updcontar->tipoajuste="E";            
        }
        $updcontar->save();
        $this->modoGrabar="ON";

    }   


    //escuchar eventos y ejecutar acción solicitada
    protected  $listeners = [
        'deleteRow'         => 'borraLinea' ,   
        'updConteo'         => 'updConteo', 
        'borrarConteo'      => 'borrarConteo',
        'resetInput'        => 'resetInput',
        'grabarDatos'       => 'grabarDatos',
        'extraeVence'       => 'extraeVence',
        'updVencimiento'    => 'updVencimiento',
        'grabaVencimiento'  => 'grabaVencimiento',
        'grabarConteo'      => 'grabarConteo',
        'descartarConteo'   => 'descartarConteo'
    ];  



}

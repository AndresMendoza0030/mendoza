<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Livewire\WithPagination;
use Livewire\Component;
use App\Warehouse;
use App\Productline;
use App\Product;
use App\Productstock;
use App\Generalsetup;
use App\Productclassinvoice;
use App\Inventoryhead;
use App\Vencimiento;
use App\Movdetail;

class TrasladosController extends Component
{
    use WithPagination;


	//properties
    public  $selected_id, $search;   			//para búsquedas y fila seleccionada
    public  $action = 1;             			//manejo de ventanas
    private $pagination = 4;         			//paginación de tabla


    public  $detalle, $bodegas,$lotesprodsum,$productos;
    public  $lafecha,$destinogasto,$concepto,$bodega,$bodega2,$lproducto,$productoid,$loteid,$newlote,$tipomov;
    public  $elstock, $lacantidad, $numeroreg, $tregistros,$todobien, $annoactual, $tasaiva;
    public  $lotevence =[];

    public function mount()
    {
        $lafecha= date('Y-m-d');
        $this->lafecha = $lafecha;
        $this->lproducto='';
        $this->numeroreg=0;
        $this->lacantidad='';
        $this->lotesprodsum=0;
        $this->tregistros=0;
        $this->lotevence=[];
        $this->tipomov='';
        $this->newlote='';
        $this->todobien=0;
        //
        $this->detalle=collect([]);
        //
        $empresa = Generalsetup::first();
        $this->tasaiva = round($empresa->piva/100,2);
        //
        $Date = date("d-m-Y");  
        $this->annoactual = date("Y");
    }
    //método que se ejecuta al inciar el componente
    public function render()
    {
        $this->crearArreglos();

        if(strlen($this->lproducto) > 0)
        {
            $this->productos= Product::leftjoin('productstocks as stk', function ($join)
                    {
                    $join->on('stk.producto_id', '=', 'products.id')
                    ->where('stk.bodega', '=', DB::raw("'".$this->bodega."'"));
                    })
            ->select("products.*",'stk.existencia as stock',
            DB::raw("CONCAT(TRIM(nombre)) as producto")) 
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

        return view('livewire.traslados.component');
    }


    public function crearArreglos()
    {
        $this->bodegas      = Warehouse::where('estatus','=','0')->get();        

    }

    public function asgConcepto($valor)
    {
        $this->concepto=$valor;
    }
    
    public function asgBodega($valor)
    {
        if ($valor == $this->bodega2)
        {
            $this->emit('msg-error', 'La Bodega Origen, no puede ser la misma destino!!!!');
            $this->bodega='';
            return;
        }
        $this->bodega=$valor;
    }

    public function asgBodega2($valor)
    {
        $this->bodega2=$valor;
        if ($valor==$this->bodega)
        {
            $this->emit('msg-error', 'La Bodega Origen, no puede ser la misma destino!!!!');
            $this->bodega2='';
            return;
        }

    }

    public function seleProducto($reg)
    {
        $this->lacantidad='';
        $this->loteid=0;

        $prodJson = json_decode($reg);
        $this->lproducto=TRIM($prodJson->producto); //." ".TRIM($prodJson->presentacion);
        $this->productoid=$prodJson->id;
        
        $this->elstock='';
        $stock = Productstock::where('producto_id','=',$this->productoid)
        ->where('bodega','=',$this->bodega)->first();
        if ($stock)
        {
            $this->elstock=$stock->existencia;
        }
        //
        $this->lotesprodsum=0;
        $this->lotevence=[];

        $this->lotevence = Vencimiento::where('producto_id','=',$this->productoid)
        ->where('bodega','=',$this->bodega)->where('total','>',0)
        ->orderby('anno','asc')->orderby('mes','asc')->get();
        //
        $this->lotesprodsum = $this->lotevence->count();
        if ($this->lotesprodsum ==1)
        {
            $this->loteid = $this->lotevence[0]->id;
        }
        //
        $this->emit('enable-cajas',["muestrac"=>1, "nlotes"=>$this->lotesprodsum]);
        $this->emit('enable-grabar',$this->tregistros);        
    }

    public function validaCantidad($valor)
    {
        $this->lacantidad=$valor;

        if (floatval($this->lacantidad) < 1 || $this->lacantidad=='')
        {
            $this->emit('msg-error', "La Cantidad debe ser mayor 1... Favor Revisar");
            $this->emit('enable-cajas',["muestrac"=>0, "nlotes"=>$this->lotesprodsum]);
            return false;
        }

        if ($this->elstock < $valor)
        {
            $this->emit('msg-error', "La Cantidad es mayor a la Existencia Actual... Favor Revisar");
            $this->lacantidad='';
            $this->emit('enable-cajas',["muestrac"=>0, "nlotes"=>$this->lotesprodsum]);
            $this->emit('enable-grabar',$this->tregistros);
            return false;                
        }
        else
        {
            $this->emit('enable-grabar',$this->tregistros);
        }
    }
    
    public function asgLote($valor)
    {
        $this->loteid=$valor;
        $this->emit('enable-cajas',["muestrac"=>0, "nlotes"=>$this->lotesprodsum]);
        $this->emit('enable-grabar',$this->tregistros);        
    }

    public function saveLine()
    {
        if (floatval($this->lacantidad) < 1 || $this->lacantidad=='')
        {
            $this->emit('msg-error', "La Cantidad debe ser mayor 1... Favor Revisar");
            $this->emit('enable-cajas',["muestrac"=>0, "nlotes"=>$this->lotesprodsum]);
            return false;
        }

        //
        if ($this->lotesprodsum>0 && $this->loteid==0)
        {
            $this->emit('msg-error','Debe Seleccionar un lote de vencimiento...');
            $this->emit('enable-cajas',["muestrac"=>0, "nlotes"=>$this->lotesprodsum]);
            return;
        }

        $lote = Vencimiento::find($this->loteid);
        if ($lote)
        {
            $cvence = $lote->mes."/".$lote->anno;
        }
        else
        {
            $cvence='';
        }
        
        //
        $this->detalle->push(["reg"=>$this->numeroreg, "productoid"=>$this->productoid, "producto"=>$this->lproducto,
        "cantidad"=>$this->lacantidad, "bodega1"=>$this->bodega,"bodega2"=>$this->bodega2,"unitario"=>0.00, "cvence" => $cvence, 
        "lotevence"=> $this->loteid, "monto"=>0.00]);
        //
        $this->numeroreg+=1;
        $this->tregistros+=1;
        //
        $this->lproducto='';
        $this->lacantidad='';
        $this->loteid=0;
        //
        $this->emit('enable-bodega',1);
        //
    }

    public function descartarOperacion()
    {
        $this->resetInput();
    }


    //método para reiniciar variables
    public function resetInput()
    {
        $this->crearArreglos();
        $lafecha= date('Y-m-d');
        $this->lafecha = $lafecha;
        $this->lproducto='';
        $this->numeroreg=0;
        $this->lacantidad='';
        $this->lotesprodsum=0;
        $this->lotevence=[];
        $this->tipomov='';
        $this->newlote='';
        $this->todobien=0;
        $this->tregistros=0;
        $this->bodega='';
        $this->bodega2='';
        $this->concepto='';
        //
        $this->detalle=collect([]);
        //
    }

    //escuchar eventos y ejecutar acción solicitada
    protected  $listeners = [
        'deleteRow'         => 'destroy'
    ];  

    public function grabarBd()
    {
        if ($this->bodega=='')
        {
            return;           
        }
        if ($this->bodega2=='')
        {
            return;           
        }

        //iniciamos transacción
        DB::beginTransaction();
        try { 
            //Head Registrando Salida
            $head = InventoryHead::create([
                'bodega'        => $this->bodega,
                'bodegadestino' => $this->bodega2,
                'fecha'         => Carbon::parse($this->lafecha),
                'tipodoc'       => 'STRA',
                'estatus'       => 0,
                'user_id'       => auth()->user()->id,
                'concepto'      => $this->concepto,
            ]);      
            $idmov=$head->id;
            //Detalle Registrando Salida
            foreach($this->detalle as $det)
            {
                $produx = Product::find($det['productoid']);
                $detabod = Movdetail::create([
                'bodega'        => $head->bodega,
                'tipodoc'       => $head->tipodoc,
                'numerodoc'     => $head->id,
                'fecha'         => $head->fecha,
                'clase'         => $produx->clase,
                'linea'         => $produx->linea,
                'lineafac'      => $produx->lineafac,
                'producto_id'   => $det['productoid'],
                'producto'      => $det['producto'],
                'cantidad'      => $det['cantidad'],
                'movimiento'    => "S",
                'precosto'      => $produx->costo,
                'subtotal'      => round($det['cantidad']*$produx->costo,4),
                'gravado'       => round($det['cantidad']*$produx->costo,4),
                'iva'           => round((round($det['cantidad']*$produx->costo,4))*$this->tasaiva,4),
                'total'         => round(round($det['cantidad']*$produx->costo,4)+round((round($det['cantidad']*$produx->costo,4))*$this->tasaiva,4),2),
                'tasaiva'       => $this->tasaiva * 100,
                'user_id'       => auth()->user()->id,
                'lotevence'     => $det['lotevence'],
                'estatus'       => 0
                ]);
                //
                if ($det['lotevence']>0)
                {
                    $ltvc= Vencimiento::find($det['lotevence']);
                    $sl=$ltvc->salidas + $det['cantidad'];
                    $ltvc->salidas=$sl;
                    $ltvc->total=$ltvc->entradas-$ltvc->salidas; 
                    $ltvc->save();                   
                }
            }

            //Head Registrando Entrada
            $head2 = InventoryHead::create([
                'bodega'        => $this->bodega2,
                'bodegadestino' => 0,
                'docucontra'    => $idmov,
                'fecha'         => Carbon::parse($this->lafecha),
                'tipodoc'       => 'ETRA',
                'estatus'       => 0,
                'user_id'       => auth()->user()->id,
                'concepto'      => $this->concepto,
            ]);      
            $idmov2=$head2->id;
            //Detalle Registrando Entrada
            foreach($this->detalle as $det)
            {
                $milote='';
                $entralotevence=0;
                if ($det['lotevence']>0)
                {
                    $milote=$det['lotevence'];
                }

                if ($det['lotevence']>0)
                {
                    $lotesale=Vencimiento::find($det['lotevence']);
                    $meslote=$lotesale->mes;
                    $annolote=$lotesale->anno;
                    $bdlote = $head2->bodega;
                    $idplote = $lotesale->producto_id;
                    //
                    $entralote=Vencimiento::where('producto_id','=', $idplote)
                    ->where('mes','=',$meslote)->where('anno','=',$annolote)
                    ->where('bodega','=',$bdlote)->select('vencimientos.*')->first();

                    //
                    if ($entralote) //ya existia lote
                    {
                        $ltvc= Vencimiento::find($entralote->id);
                        $el=$ltvc->entradas + $det['cantidad'];
                        $ltvc->entradas=$el;
                        $ltvc->total=$ltvc->entradas-$ltvc->salidas; 
                        $ltvc->save();    
                        $entralotevence=$ltvc->id;               
                    }
                    else //se crea el lote
                    {
                        $ltvc=Vencimiento::create([
                            'bodega'        => $head2->bodega,
                            'producto_id'   => $det['productoid'],
                            'mes'           => $meslote,
                            'anno'          => $annolote,
                            'entradas'      => $det['cantidad'],
                            'total'         => $det['cantidad']
                        ]);
                        $entralotevence=$ltvc->id;
                    }
                }

                $produx = Product::find($det['productoid']);
                $detabod = Movdetail::create([
                'bodega'        => $head2->bodega,
                'tipodoc'       => $head2->tipodoc,
                'numerodoc'     => $head2->id,
                'fecha'         => $head2->fecha,
                'clase'         => $produx->clase,
                'linea'         => $produx->linea,
                'lineafac'      => $produx->lineafac,
                'producto_id'   => $det['productoid'],
                'producto'      => $det['producto'],
                'cantidad'      => $det['cantidad'],
                'movimiento'    => "E",
                'precosto'      => $produx->costo,
                'subtotal'      => round($det['cantidad']*$produx->costo,4),
                'gravado'       => round($det['cantidad']*$produx->costo,4),
                'iva'           => round((round($det['cantidad']*$produx->costo,4))*$this->tasaiva,4),
                'total'         => round(round($det['cantidad']*$produx->costo,4)+round((round($det['cantidad']*$produx->costo,4))*$this->tasaiva,4),2),
                'tasaiva'       => $this->tasaiva * 100,
                'lotevence'     => $entralotevence,
                'user_id'       => auth()->user()->id,
                'estatus'       => 0
                ]);
                //
            }
            $head->docucontra = $head2->id;
            $head->save();                    
            //confirmamos transacción
            DB::commit();
            $this->resetInput();
            $this->emit('msgok', 'El Traslado ha sido Grabado. . .');
            $this->emit('confirmaOrden', $head->id);
            $this->emit('print-mov',  $head->id);
            $this->emit('enable-bodega',2);
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
                $nuevodet->push(["reg"=>$rs['reg'], "productoid"=>$rs['productoid'], "producto"=>$rs['producto'],
                "cantidad"=>$rs['cantidad'], "bodega1"=>$rs['bodega1'],"bodega2"=>$rs['bodega2'],"unitario"=>$rs['unitario'],
                "cvence" =>$rs['cvence'],"lotevence"=>$rs['lotevence'],"monto"=>$rs['monto']]);           
            }
        }
        $this->detalle=$nuevodet;
        $this->tregistros=$this->detalle->count();  
    }

}

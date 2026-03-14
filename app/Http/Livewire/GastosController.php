<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Livewire\WithPagination;
use Livewire\Component;
use App\Warehouse;
use App\Productclass;
use App\Productline;
use App\Product;
use App\Productstock;
use App\Destino;
use App\Productclassinvoice;
use App\ClienteProveedor;
use App\Inventoryhead;
use App\Movdetail;
use App\Vencimiento;


class GastosController extends Component
{
    use WithPagination;

	//properties
    public  $selected_id, $search;   			//para búsquedas y fila seleccionada
    public  $action = 1;             			//manejo de ventanas
    public  $section= 1;
    private $pagination = 4;         			//paginación de tabla

    public  $detalle, $detinos, $bodegas,$lotesprodsum;
    public  $lafecha,$destinogasto,$concepto,$bodega,$lproducto,$productoid,$loteid;
    public  $elstock, $lacantidad, $numeroreg, $tregistros;
    public  $lotevence =[], $productos=[];


    public function mount()
    {
        $lafecha= date('Y-m-d');
        $this->lafecha = $lafecha;
        $this->lproducto='';
        $this->numeroreg=0;
        $this->lacantidad=0;
        $this->lotesprodsum=0;
        $this->lotevence=[];

        $this->detalle=collect([]);
    }


    //método que se ejecuta al inciar el componente
    public function render()
    {
        $this->crearArreglos();


            if ($this->bodega>0)
            {
                if(strlen($this->lproducto) > 0)
                //
                {
                    $bodega=$this->bodega;
                    //
                    $this->productos= Product::leftjoin('productstocks as stk', function ($join) use ($bodega)
                    {
                        $join->on('stk.producto_id', '=', 'products.id')
                        ->where('stk.bodega', '=', $bodega)
                        ->where('stk.existencia','>',0);
                    })
                    ->select("products.*",'stk.existencia as stock',
                    DB::raw("CONCAT(TRIM(nombre)) as producto"))
                    ->where('tipo','=','PRODUCTO')->where('estatus','=','0')
                    ->where('nombre', 'like', '%'. $this->lproducto .'%')
                    ->get();
                    //
                }
                else
                {
                    $this->productos=[];
                }
            }


        //asignamos a la propiedad clientes el resultado dela búsqueda

        return view('livewire.gastos.component');
    }


    public function crearArreglos()
    {
        $this->clases       = Productclass::where('estatus','=','0')->get();
        $this->lineas       = Productline::select('id','clase_id','nombre','cuenta')
        ->where('estatus','=','0')->get();
        $this->bodegas      = Warehouse::where('estatus','=','0')->get();        
        $this->destinos     = Destino::where('estatus','=','0')->get();        
        $this->proveedores  = ClienteProveedor::where('estatus','=','0')
        ->where('tipo','=','PROVEEDOR')->get();
        $this->productos= Product::select("products.*",
        DB::raw("CONCAT(TRIM(products.nombre),' ',TRIM(products.presentacion)) as nombreproducto"))
        ->where('estatus','=','0')->where('tipo','=','PRODUCTO')
        ->orderby('nombreproducto')->get();
    }


    public function asgDestino($valor)
    {
        $this->destinogasto=$valor;
    }


    public function asgConcepto($valor)
    {
        $this->concepto=$valor;
    }
    

    public function asgBodega($valor)
    {
        $this->bodega=$valor;
        $this->emit('asgsele2',0);
    }

    public function seleProducto($reg)
    {
        $this->lacantidad='';
        $this->loteid=0;
        /////
        $prodJson = json_decode($reg);
        $this->lproducto=TRIM($prodJson->producto); //." ".TRIM($prodJson->presentacion)
        $this->productoid=$prodJson->id;
        /////
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
        $this->emit('enable-cajas',1);
    }


    public function validaCantidad($valor)
    {
        if ($this->elstock < $valor)
        {
            $this->emit('msg-error', "La Cantidad es mayor a la Existencia Actual... Favor Revisar");
            $this->lacantidad='';
            $this->emit('enable-cajas',0);
        }
        else
        {
            $this->emit('enable-grabalinea');

        }
    }

    
    public function asgLote($valor)
    {
        $this->loteid=$valor;
        $this->emit('enable-cajas',0);        
    }

    public function saveLine()
    {
        if ($this->lotesprodsum>0 && $this->loteid==0)
        {
            $this->emit('msg-error','Debe Seleccionar un lote de vencimiento...');
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
        $this->detalle->push(["reg"=>$this->numeroreg, "productoid"=>$this->productoid, "producto"=>$this->lproducto,
        "unitario"=>0.00, "cvence" => $cvence, "lotevence"=> $this->loteid,"cantidad"=>$this->lacantidad, 
        "monto"=>0.00]);
        //
        $this->numeroreg+=1;
        $this->tregistros+=1;
        //
        $this->lproducto='';
        $this->productoid=0;
        $this->lacantidad='';
        $this->loteid=0;
        //
        $this->emit('asgsele2',0);
    }

    public function descartarOperacion()
    {
        $this->resetInput();
    }

    //método para reiniciar variables
    public function resetInput()
    {
        $this->crearArreglos();

        $this->detalle=collect([]);        
        $this->lotevence=[];
        $this->bodega ='';
        $this->concepto ='';

        $lafecha= date('Y-m-d');
        $this->lafecha = $lafecha;

        $this->numeroreg=0;
        $this->tregistros=0;

        $this->productoid='';
        $this->lproducto = '';
        $this->destinogasto = '';

    }

    //escuchar eventos y ejecutar acción solicitada
    protected  $listeners = [
        'deleteRow'         => 'destroy' ,   
        'seleProducto'      => 'seleProducto'
    ];  


    public function grabarBd()
    {

        if ($this->tregistros<1)
        {
            return;
        }

        if ($this->destinogasto<1)
        {
            $this->emit('msg-error','Debe indicar un destino válido...');
            return;
        }

        $eltipo='SGAS';
        $elmov= 'S';

        //iniciamos transacción
        DB::beginTransaction();
        try { 
            //Head Registrando Salida
            $head = InventoryHead::create([
                'bodega'        => $this->bodega,
                'bodegadestino' => 0,
                'destinogasto'  => $this->destinogasto,
                'fecha'         => Carbon::parse($this->lafecha),
                'tipodoc'       => $eltipo,
                'estatus'       => 0,
                'user_id'       => auth()->user()->id,
                'concepto'      => $this->concepto,
            ]);      
            //Detalle Registrando Salida
            foreach($this->detalle as $det)
            {
                $miltvence=$det['lotevence'];
                //
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
                'movimiento'    => $elmov,
                'precosto'      => $produx->costo,
                'subtotal'      => round($det['cantidad']*$produx->costo,4),
                'gravado'       => round($det['cantidad']*$produx->costo,4),
                'user_id'       => auth()->user()->id,
                'lotevence'     => $det['lotevence'], //det->lotevence,
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

            //confirmamos transacción
            DB::commit();
            $this->resetInput();
            $this->emit('msgok', 'La Salida al Gasto ha sido creada. . .');
            $this->emit('confirmaOrden', $head->id);
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

    //método para eliminar un registro dado
    public function destroy($id)
    {
        if ($id)
        {
            $nuevodet=collect([]);          
            foreach($this->detalle as $rs)
            {
                if ($rs['reg'] != $id)
                {
                    $nuevodet->push(["reg"=>$rs['reg'], "productoid"=>$rs['productoid'], "producto"=>$rs['producto'],
                    "unitario"=>$rs['unitario'], "cvence" =>$rs['cvence'], "lotevence"=>$rs['lotevence'],
                    "cantidad"=>$rs['cantidad'], "monto"=>$rs['monto']]);        
                }
            }
            $this->detalle=$nuevodet;
            
            $this->emit('msgok', "Registro ha sido desactivado del sistema");    
            $tregistros=$this->detalle->count();
    
        }
 
    }
}

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
use App\Productclassinvoice;
use App\Inventoryhead;
use App\Movdetail;
use App\Destino;
use App\Productsstocks;
use App\Reqhead;
use App\Detareq;
use App\Documento;
use App\Generalsetup;
use App\Vencimiento;
use App\User;


class VisordespachoController extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

	//properties
    public  $selected_id, $search, $lafecha,$tipodoc,$condetalle,$listo,$numeroreg;   			//para búsquedas y fila seleccionada
    public  $despachabod, $eldestino, $lafechadoc,$detalledoc;
    public  $elproducto, $cnsolicitada, $cnstock, $okProcesa=0;
    public  $labodega, $cuales, $cualesbod, $idtrabajo, $xdocumento, $bodegatrabajo,$tipomovimiento;
    public  $x=0;

    private $pagination = 12;         			//paginación de tabla
    public  $action =1;

    public  $detalle, $info, $bodegas, $destinos, $productos, $tipomovs; //listas para select


    public function mount()
    {
        $lafecha= date('Y-m-d');
        $this->lafecha=$lafecha;    
        $this->labodega = '';
        $this->tipodoc='';
        $this->cuales = 1;
        $this->cualesbod = 1;        
        $this->condetalle=0;
        $this->listo=0;
        $this->numeroreg=0;
        //
        $this->detalle=collect([]);
        $this->info=collect([]);
        //
    }

    //método que se ejecuta al inciar el componente
    public function render()
    {
        if ($this->labodega=='' && $this->tipodoc=='')
        {
            $pendientes = Reqhead::leftjoin('documentos as d', 'd.documento', 'reqhead.tipodoc')
            ->leftjoin('warehouses as w', 'w.id', 'reqhead.bodega')
            ->leftjoin('warehouses as w2', 'w2.id', 'reqhead.bodegadestino')
            ->leftjoin('users as u', 'u.id', 'reqhead.user_id')
            ->leftjoin('destinos as ds', 'ds.id', 'reqhead.destino_id')
            ->select("reqhead.*", 'd.nombre as documento', 'w.nombre as cbodega', 'u.email as usuario',
            'w2.nombre as cbodegadestino', 'ds.nombre as cdestino','w.nombre as cbodega','reqhead.id as idm')
            ->where('reqhead.estatus','=',0)->where('reqhead.autorizado','=','S')
            ->where('reqhead.despachado','=','N')->orderby('reqhead.bodega')
            ->orderby('reqhead.tipodoc')->paginate($this->pagination);
        }
        if ($this->labodega!='' && $this->tipodoc=='')
        {
            $pendientes = Reqhead::leftjoin('documentos as d', 'd.documento', 'reqhead.tipodoc')
            ->leftjoin('warehouses as w', 'w.id', 'reqhead.bodega')
            ->leftjoin('warehouses as w2', 'w2.id', 'reqhead.bodegadestino')
            ->leftjoin('users as u', 'u.id', 'reqhead.user_id')
            ->leftjoin('destinos as ds', 'ds.id', 'reqhead.destino_id')
            ->select("reqhead.*", 'd.nombre as documento', 'w.nombre as cbodega', 'u.name as usuario',
            'w2.nombre as cbodegadestino', 'ds.nombre as cdestino','w.nombre as cbodega','reqhead.id as idm')
            ->where('reqhead.estatus','=',0)->where('reqhead.autorizado','=','S')
            ->where('reqhead.despachado','=','N')->where('reqhead.bodega','=',$this->labodega)
            ->orderby('reqhead.bodega')->orderby('reqhead.tipodoc')->paginate($this->pagination);            
        }
        if ($this->labodega!='' && $this->tipodoc!='')
        {
            $pendientes = Reqhead::leftjoin('documentos as d', 'd.documento', 'reqhead.tipodoc')
            ->leftjoin('warehouses as w', 'w.id', 'reqhead.bodega')
            ->leftjoin('warehouses as w2', 'w2.id', 'reqhead.bodegadestino')
            ->leftjoin('users as u', 'u.id', 'reqhead.user_id')
            ->leftjoin('destinos as ds', 'ds.id', 'reqhead.destino_id')
            ->select("reqhead.*", 'd.nombre as documento', 'w.nombre as cbodega', 'u.name as usuario',
            'w2.nombre as cbodegadestino', 'ds.nombre as cdestino','w.nombre as cbodega','reqhead.id as idm')
            ->where('reqhead.estatus','=',0)->where('reqhead.tipodoc','=',$this->tipodoc)->where('reqhead.autorizado','=','S')
            ->where('reqhead.despachado','=','N')->where('reqhead.bodega','=',$this->labodega)
            ->orderby('reqhead.bodega')->orderby('reqhead.tipodoc')->paginate($this->pagination);            
        }
        if ($this->labodega=='' && $this->tipodoc!='')
        {
            $pendientes = Reqhead::leftjoin('documentos as d', 'd.documento', 'reqhead.tipodoc')
            ->leftjoin('warehouses as w', 'w.id', 'reqhead.bodega')
            ->leftjoin('warehouses as w2', 'w2.id', 'reqhead.bodegadestino')
            ->leftjoin('users as u', 'u.id', 'reqhead.user_id')
            ->leftjoin('destinos as ds', 'ds.id', 'reqhead.destino_id')
            ->select("reqhead.*", 'd.nombre as documento', 'w.nombre as cbodega', 'u.name as usuario',
            'w2.nombre as cbodegadestino', 'ds.nombre as cdestino','w.nombre as cbodega','reqhead.id as idm')
            ->where('reqhead.estatus','=',0)->where('reqhead.tipodoc','=',$this->tipodoc)
            ->where('reqhead.despachado','=','N')->where('reqhead.autorizado','=','S')
            ->orderby('reqhead.bodega')->orderby('reqhead.tipodoc')->paginate($this->pagination);            
        }



        $fecha= date('d-m-Y');
        $this->fecha=$fecha;


        //$this->pendientes = $pendientes;
        $this->crearArreglos();
       
        //asignamos a la propiedad clientes el resultado dela búsqueda
        return view('livewire.visordespacho.component', [
            'docum' => $pendientes, ]);
    }

    public function updatingSearch()
    {
        $this->gotoPage(1);
    }



    public function verDetalle($idm)
    {
        
        $this->x=0;
        $this->listo=0;
        //
        $this->detalle=collect([]);
        $this->info=collect([]);

        $this->emit('msg-error',$idm);

        $documento = Reqhead::where('reqhead.id','=',$idm)
        ->leftjoin('documentos as d', 'd.documento', 'reqhead.tipodoc')
        ->leftjoin('warehouses as w', 'w.id', 'reqhead.bodega')
        ->leftjoin('warehouses as w2', 'w2.id', 'reqhead.bodegadestino')
        ->leftjoin('users as u', 'u.id', 'reqhead.user_id')
        ->leftjoin('destinos as ds', 'ds.id', 'reqhead.destino_id')
        ->select("reqhead.*", 'd.nombre as documento', 'w.nombre as cbodega', 'u.email as usuario',
        'w2.nombre as cbodegadestino', 'ds.nombre as cdestino', 'd.movimiento as tipodocumento')
        ->first();
        //
        if ($documento)
        {
            $this->labodega=$documento->bodega;
            $this->tipodocumento=$documento->tipodocumento;
        }

        $this->despachabod = $documento->cbodega;
        $this->eldestino  = TRIM($documento->cbodegadestino).TRIM($documento->cdestino).TRIM($documento->nombrepaciente);
        $this->lafechadoc = TRIM($documento->fecha).' sol.por: '.TRIM($documento->usuario);
        $this->bodegatrabajo= $documento->bodega;

        $this->xdocumento = $documento->documento;

        $detalle = Detareq::leftjoin('products as p', 'p.id', 'detareqs.producto_id')
        ->leftjoin('productstocks as w','w.producto_id', 'detareqs.producto_id')
        ->leftjoin('documentos as dc','dc.documento', 'detareqs.tipodoc')
        ->select("detareqs.*", 'w.existencia as stock', 'dc.movimiento as movimiento',
        DB::raw("CONCAT(TRIM(p.nombre),' ',TRIM(p.presentacion)) as elproducto"))
        ->where('w.bodega','=',$this->labodega)        
        ->where('detareqs.numerodoc','=',$idm)
        ->get();
        //
        
        
        $midetalle= collect([]);

        $reg=0;

        foreach($detalle as $d)
        {
            if ($this->tipodocumento=="S")
            {
                $st='';
                $stk=Productstock::where('producto_id','=',$d->producto_id)->where('bodega','=',$this->labodega)
                ->first();
                if ($stk)
                {
                    $st=$stk->existencia;
                }
   
                $despacho=0;
                $lv=0;
                $cv='';
                $pide="SI";
                //
                $vence = Vencimiento::where('producto_id','=',$d->producto_id)->where('bodega','=',$this->labodega)
                ->where('total','>',0)->get();

                if ($vence->count()>0)
                {
                    if ($vence->count()==1 && $vence[0]->total >= $d->cantidad)
                    {
                        $despacho = $d->cantidad;
                        $lv = $vence[0]->id;
                        $pide="NO";
                        $cv=$vence[0]->mes."/".$vence[0]->anno;
                    }
                }
                else
                {
                    if (floatval($st) >= floatval($d->cantidad))
                    {   
                        $despacho = $d->cantidad;
                        $lv = 0;
                        $pide="NO";
                        $cv='';
                    }
                }
                    $this->info->push(["reg"=>$reg, "id"=>$d->idoriginal, "productoid"=>$d->producto_id, "producto"=>$d->producto,
                    "solicitado"=>$d->cantidad,"stock"=>$st,"despachado"=>$despacho, "movimiento"=>$this->tipodocumento,
                    "cvence"=>$cv,"lotevence"=>$lv,"pedir"=>$pide,"autorizado"=>$d->autorizado]);
                    //
                    $reg+=1;
                    //   
            }
            //
            if ($this->tipodocumento=="E")
            {
                $st='';
                $cv='';
                $despacho=$d->cantidad;
                $lv=$d->lotevence;
                $pide="NO";
                //
                $vence = Vencimiento::find($d->lotevence);
                if ($vence)
                {
                    $cv=$vence->mes."/".$vence->anno;
                }
                //
                $this->info->push(["reg"=>$reg, "id"=>$d->idoriginal, "productoid"=>$d->producto_id, "producto"=>$d->producto,
                "solicitado"=>$d->cantidad,"stock"=>$st,"despachado"=>$despacho, "movimiento"=>$this->tipodocumento,
                "cvence"=>$cv,"lotevence"=>$lv,"pedir"=>$pide,"autorizado"=>$d->autorizado]);
                //
                $reg+=1;
                //   
            }

            if ($pide=="NO") //crear en detalle
            {
                $this->detalle->push(["reg"=>$this->numeroreg, "id"=>$d->idoriginal, "productoid"=>$d->producto_id,
                "producto"=>$d->producto,"solicitado"=>$d->cantidad,"aprocesar"=>$d->cantidad,
                "cvence"=>$cv,"lotevence"=>$lv]);
                $this->numeroreg+=1;
            }
        }
        //
        //
        $pedido=0;
        $despacho=0;
        //
        $pedido = $this->info->sum('solicitado');
        $despacho = $this->detalle->sum('aprocesar');
        if ($pedido == $despacho)
        {
            $this->listo=1;
        }
        //
        $this->okProcesa=0;
        $this->listo=0;
        $z=0;
        $autorizados = $this->info->count();
        foreach ($this->info as $i)
        {
            if ($i['autorizado'] > $i['stock'])
            {
                $this->okProcesa=1;
            }
            //
            if ($i['autorizado'] > 0)
            {
                $z =+  1;
            }

        }
        $this->listo= $this->okProcesa;
        //
        if ($z!=$autorizados && $z>0)
        {
            $this->listo=1;
        }
        //
        if ($z==0)
        {
            $this->listo=2;
        }

        //
        $this->idtrabajo = $idm;
        $this->action =2;
        //$this->detalle = $detalle;
    }


    public function setAction($laccion)
    {
        $this->action = $laccion;
    }

    public function crearArreglos()
    {
        $this->bodegas      = Warehouse::where('estatus','=','0')->get();        
        $this->destinos     = Destino::where('estatus','=','0')->get();        
        $this->tipomovs     = Documento::where('estatus','=','0')
        ->where('tipo','=','SOL')->get();
    }
    //método para reiniciar variables
    public function resetInput()
    {
        $this->crearArreglos();

        $fecha= date('d-m-Y');
        $this->fecha=$fecha;    

        $this->bodega ='';
        $this->destino='';
        $this->concepto ='';

        $this->producto='';
        
        $this->elproducto = '';
        $this->buscarProducto = '';
   
        $this->selected_id = null;       
        $this->action = 1;
        $this->search = '';
    }

    public function confirmaDespacho()
    {

        $usuario = auth()->user()->id;

        $solicitud = Reqhead::find($this->idtrabajo);
        if (!$solicitud || $solicitud->despachado=="S")
        {
            $this->emit('msg-error', 'El pedido'.$this->idtrabajo.' ya fue despachado previamente.. . .');
            $this->resetInput();
            $this->action = 1;
            return ;
        }
        //
        //validando Existencia previa de despacho
        $verpedido=Inventoryhead::where('pedido','=',$this->idtrabajo)->count();
        if ($verpedido>0)
        {
            $this->emit('msg-error', 'El pedido'.$this->idtrabajo.' ya fue despachado previamente.. . .');    
            $this->action = 1;
            return ;
        }
        //
        $empresa = Generalsetup::first();
        $tasaiva = round($empresa->piva/100,2);
        //
        //iniciamos transacción
        DB::beginTransaction();
        try { 
            //Head Registrando Salida
            switch ($solicitud->tipodoc) {
                
                case 'PTRB':
                    //
                    //Head Registrando Salida
                    $head = InventoryHead::create([
                        'bodega'        => $solicitud->bodega,
                        'bodegadestino' => $solicitud->bodegadestino,
                        'fecha'         => Carbon::now(),
                        'tipodoc'       => 'STRA',
                        'pedido'        => $solicitud->id,
                        'user_id'       => $usuario,
                        'estatus'       => 0,
                        'concepto'      => $solicitud->concepto,
                    ]);      
                    $idmov=$head->id;
                    //Detalle Registrando Salida
                    $cont=0;
                    
                    foreach($this->info as $det)
                    {
                        $produx = Product::find($det['productoid']);

                        if ($det['lotevence']>0)
                        {
                            $miltvence=$det['lotevence'];
                        }
                        else
                        {
                            $miltvence=0;
                        }
    
                        $detabod = Movdetail::create([
                        'bodega'        => $head->bodega,
                        'tipodoc'       => $head->tipodoc,
                        'numerodoc'     => $head->id,
                        'fecha'         => $head->fecha,
                        'clase'         => $produx->clase,
                        'linea'         => $produx->linea,
                        'lineafac'      => $produx->lineafac,
                        'producto_id'   => $det['productoid'],
                        'producto'      => TRIM($produx->nombre)." ".TRIM($produx->presentacion),
                        'cantidad'      => $det['autorizado'],
                        'movimiento'    => "S",
                        'precosto'      => $produx->costo,
                        'subtotal'      => $det['autorizado']*$produx->costo,
                        'gravado'       => $det['autorizado']*$produx->costo,
                        'lotevence'     => $miltvence,
                        'estatus'       => 0
                        ]);
                        //
                        if ($miltvence>0)
                        {
                            $ltvc= Vencimiento::find($miltvence);
                            if($ltvc)
                            {
                                $ltvc->salidas=$ltvc->salidas + $det['autorizado'];
                                $ltvc->total=$ltvc->entradas-$ltvc->salidas; 
                                $ltvc->save();     
                            }
                        }
                    }
                    //Head Registrando Entrada
                    $head2 = InventoryHead::create([
                        'bodega'        => $solicitud->bodegadestino,
                        'bodegadestino' => 0,
                        'pedido'        => $solicitud->id,
                        'docucontra'    => $idmov,
                        'fecha'         => $head->fecha,
                        'tipodoc'       => 'ETRA',
                        'user_id'       => $usuario,
                        'estatus'       => 0,
                        'concepto'      => $solicitud->concepto,
                    ]);

                    $idmov2=$head2->id;
                    //Detalle Registrando Entrada
                    foreach($this->info as $det)
                    {
                        $produx = Product::find($det['productoid']);

                        if ($det['lotevence'] !=0)
                        {
                            $venceparam=Vencimiento::find($det['lotevence']);
                            $xidentralote=Vencimiento::where('bodega','=',$solicitud->bodegadestino)
                            ->where('producto_id','=',$det['productoid'])
                            ->where('mes','=',$venceparam->mes)->where('anno','=',$venceparam->anno)
                            ->select('vencimientos.*')->first();

                            if ($xidentralote!=null)
                            {
                               $identralote=$xidentralote->id;
                            }
                            else
                            {
                                $nuevolote=Vencimiento::create([
                                    'bodega'        => $solicitud->bodegadestino,
                                    'producto_id'   => $det['productoid'],
                                    'mes'           => $venceparam->mes,
                                    'anno'          => $venceparam->anno,
                                ]);
                                $identralote = $nuevolote->id;
                            }
                        }
                        else
                        {
                            $identralote=0;
                        }
                        //
                        $detabod = Movdetail::create([
                        'bodega'        => $head2->bodega,
                        'tipodoc'       => $head2->tipodoc,
                        'numerodoc'     => $head2->id,
                        'fecha'         => $head2->fecha,
                        'clase'         => $produx->clase,
                        'linea'         => $produx->linea,
                        'lineafac'      => $produx->lineafac,
                        'producto_id'   => $det['productoid'],
                        'producto'      => TRIM($produx->nombre)." ".TRIM($produx->presentacion),
                        'cantidad'      => $det['autorizado'],
                        'movimiento'    => "E",
                        'precosto'      => $produx->costo,
                        'subtotal'      => $det['autorizado']*$produx->costo,
                        'gravado'       => $det['autorizado']*$produx->costo,
                        'lotevence'     => $identralote,
                        'estatus'       => 0
                        ]);
                        //
                        if ($identralote>0)
                        {
                            $ltvc= Vencimiento::find($identralote);
                            if($ltvc)
                            {
                                $ltvc->entradas=$ltvc->entradas + $det['autorizado'];
                                $ltvc->total=$ltvc->entradas-$ltvc->salidas; 
                                $ltvc->save();     
                            }
                        }
                    }
                    $head->docucontra = $head2->id;
                    $head->save();                    
                    //
                    break;


                case 'PGAS':
                    //Head Registrando Salida
                    $head = InventoryHead::create([
                        'bodega'        => $solicitud->bodega,
                        'bodegadestino' => 0,
                        'pedido'        => $solicitud->id,
                        'destinogasto'  => $solicitud->destino_id,
                        'fecha'         => Carbon::now(),
                        'tipodoc'       => 'SGAS',
                        'estatus'       => 0,
                        'user_id'       => $usuario,
                        'concepto'      => $solicitud->concepto,
                    ]);      
                    //Detalle Registrando Salida
                    foreach($this->info as $det)
                    {
                        $produx = Product::find($det['productoid']);
                        if ($det['lotevence']>0)
                        {
                            $miltvence=$det['lotevence'];
                        }
                        else
                        {
                            $miltvence=0;
                        }

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
                        'cantidad'      => $det['autorizado'],
                        'movimiento'    => 'S',
                        'precosto'      => $produx->costo,
                        'subtotal'      => $det['autorizado']*$produx->costo,
                        'gravado'       => $det['autorizado']*$produx->costo,
                        'lotevence'     => $miltvence,
                        'estatus'       => 0
                        ]);
                        //upd vencimiento
                        if ($miltvence>0)
                        {
                            $ltvc= Vencimiento::find($miltvence);
                            if($ltvc)
                            {
                                $ltvc->salidas=$ltvc->salidas + $det['autorizado'];
                                $ltvc->total=$ltvc->entradas-$ltvc->salidas; 
                                $ltvc->save();     
                            }
                        }
                    }
            }
            //
            $solicitud->despacha_id =  $usuario;
            $solicitud->despacha_mov = $head->id;
            $solicitud->despachado = "S";
            $solicitud->despachaen = Carbon::now();
            $solicitud->save();
            $this->action=1;
            
            //confirmamos transacción
            DB::commit();
            $this->emit('msgok', 'La Solicitud ha sido despachada. . .');    

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

    //escuchar eventos y ejecutar acción solicitada
    protected  $listeners =
    [
        'deleteRow'         => 'destroy',
        'deleteRow2'        => 'destroy2',
        'deleteDoc'         => 'destroyAll',           
        'grabaItem'         => 'grabaItem',
        'confirmaDespacho'  => 'confirmaDespacho'
    ];  


    //método para eliminar un registro de la coleccion info
    public function destroy($id)
    {
        $nuevodet=collect([]);          
        foreach($this->info as $rs)
        {
            if ($rs['reg'] != $id)
            {
                $nuevodet->push(["reg"=>$rs['reg'], "id"=>$rs['id'], "productoid"=>$rs['productoid'],
                "producto"=>$rs['producto'],"solicitado"=>$rs['solicitado'],"stock"=>$rs['stock'],
                "despachado"=>$rs['despachado'], "movimiento"=>$rs['movimiento'],
                "clotevence"=>$rs['clotevence'],"lotevence"=>$rs['lotevence'],"pedir"=>$rs['pedir']]);
            }
        }
        //
        $this->info=$nuevodet;
    }

    //método para eliminar un registro de la coleccion detalle
    public function destroy2($id)
    {
        $nuevodet=collect([]);          
        foreach($this->detalle as $rs)
        {
            if ($rs['reg'] != $id)
            {
                $nuevodet->push(["reg"=>$rs['reg'],"id"=>$rs['id'],"productoid"=>$rs['productoid'],
                "producto"=>$rs['producto'], "aprocesar"=>$rs['aprocesar'],"cvence"=>$rs['cvence'],
                "lotevence"=>$rs['lotevence'],"idunico"=>$rs['idunico']]);
            }
        }
        //
        $this->detalle=$nuevodet;
    }

    //método para eliminar toto el documento de reqhead y detareq
    public function destroyAll($id)
    {
        if ($id)
        {
            //iniciamos transacción
            DB::beginTransaction();
            try
            { 
                $head = Reqhead::find($id);
                $elnumero=$head->id;
                $head->delete();
                $deta = Detareq::where('numerodoc','=',$elnumero);
                $deta->delete();
                //confirmamos transacción
                DB::commit();
                $this->emit('msgok', "La Soicitud ha sido Eliminada por Completo!!!");
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

}

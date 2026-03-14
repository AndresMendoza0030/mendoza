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
use App\Productstock;
use App\Productclassinvoice;
use App\ClienteProveedor;
use App\Inventoryhead;
use App\Generalsetup;
use App\Vencimiento;
use App\Movdetail;


class AjustesController extends Component
{
    use WithPagination;


    //properties
    public  $selected_id, $search;               //para búsquedas y fila seleccionada
    public  $section = 1;
    private $pagination = 4;                     //paginación de tabla


    public  $detalle, $destinos, $bodegas, $lotesprodsum, $productos;
    public  $lafecha, $destinogasto, $concepto, $bodega, $lproducto, $productoid, $loteid, $newlote, $tipomov;
    public  $elstock, $lacantidad, $numeroreg, $tregistros, $todobien, $annoactual, $tasaiva;
    public  $lotevence = [];



    public function mount()
    {
        $lafecha = date('Y-m-d');
        $this->lafecha = $lafecha;
        $this->lproducto = '';
        $this->numeroreg = 0;
        $this->lacantidad = '';
        $this->lotesprodsum = 0;
        $this->lotevence = [];
        $this->tipomov = '';
        $this->newlote = '';
        $this->todobien = 0;
        //
        //
        $empresa = Generalsetup::first();
        $this->tasaiva = round($empresa->piva / 100, 2);
        //
        //
        $Date = date("d-m-Y");
        $this->annoactual = date("Y");
        //
        $this->detalle = collect([]);
    }

    //método que se ejecuta al inciar el componente
    public function render()
    {
        $this->crearArreglos();

        if (strlen($this->lproducto) > 0) {
            if ($this->tipomov == "2") {
                $this->productos = Product::leftjoin('productstocks as stk', function ($join) {
                    $join->on('stk.producto_id', '=', 'products.id')
                        ->on('stk.bodega', '=', DB::raw("'" . $this->bodega . "'"));
                })
                    ->select(
                        "products.*",
                        'stk.existencia as stock',
                        DB::raw("CONCAT(TRIM(nombre)) as producto")
                    )
                    ->where('tipo', '=', 'PRODUCTO')->where('estatus', '=', '0')
                    ->where('nombre', 'like', '%' . $this->lproducto . '%')
                    ->orwhere('barra', '=', $this->lproducto)
                    ->orwhere('products.id', 'like', '%' . $this->lproducto . '%')
                    ->get();
            }

            if ($this->tipomov == "1") {
                $this->productos = Product::select(
                    "products.*",
                    DB::raw("CONCAT(TRIM(nombre)) as producto", "'' as stock")
                )
                    ->where('tipo', '=', 'PRODUCTO')->where('estatus', '=', '0')
                    ->where('nombre', 'like', '%' . $this->lproducto . '%')
                    ->orwhere('products.id', 'like', '%' . $this->lproducto . '%')
                    ->orwhere('barra', '=', $this->lproducto)
                    ->get();
            }
            //                ->orwhere('presentacion', 'like', '%'. $this->lproducto .'%')
        } else {
            $this->productos = [];
        }

        return view('livewire.ajustes.component');
    }


    public function crearArreglos()
    {
        $this->bodegas      = Warehouse::where('estatus', '=', '0')->get();
    }

    public function asgConcepto($valor)
    {
        $this->concepto = $valor;
    }

    public function asgBodega($valor)
    {
        $this->bodega = $valor;
    }


    public function seleProducto($id)
    {

        $producto = Product::find($id);
        if (!$producto) {
            $this->emit('msg-error', 'Producto inválido o formato incorrecto.');
            return;
        }
        $this->productoid = $producto->id;
        $this->lproducto  = trim($producto->nombre);

        $this->lacantidad = '';
        $this->loteid = 0;


        $this->elstock = '';
        $stock = Productstock::where('producto_id', '=', $this->productoid)
            ->where('bodega', '=', $this->bodega)->first();
        if ($stock) {
            $this->elstock = $stock->existencia;
        }
        //

        $this->lotesprodsum = 0;
        $this->lotevence = [];

        $this->lotevence = Vencimiento::where('producto_id', '=', $this->productoid)
            ->where('bodega', '=', $this->bodega)->where('total', '>', 0)
            ->orderby('anno', 'asc')->orderby('mes', 'asc')->get();
        //
        $this->lotesprodsum = $this->lotevence->count();
        if ($this->lotesprodsum == 1) {
            $this->loteid = $this->lotevence[0]->id;
        }
        //
        $this->emit('abrecaja', ["tipomov" => $this->tipomov, "muestrac" => 1, "nlotes" => $this->lotesprodsum]);
        //$this->emit('enable-grabar',$this->tregistros);
    }

    public function validaNuevoLote($valor)
    {
        $this->newlote = $valor;
        $mes = floatval(substr($valor, 0, 2));
        $anno = floatval(substr($valor, 3, 4));
        if ($mes == 0 && $anno == 0) {
            $this->todobien = 4;
            $this->emit('enable-cajas', ["tipomov" => $this->tipomov, "muestrac" => 0, "nlotes" => $this->lotesprodsum]);
            $this->emit('enable-grabar', $this->tregistros);
            return;
        }

        if ($mes < 1 || $mes > 12) {
            $this->emit('msg-error', 'El mes del lote esta fuera de rango...');

            $this->emit('enable-cajas', [
                "nlotes" => $this->lotesprodsum,
                "grabar" => "S",
                "tipomov" => $this->tipomov,
                "muestrac" => 0,
                "registros" => $this->tregistros
            ]);
            $this->emit('enable-grabar', $this->tregistros);
            $this->todobien = false;
            return false;
        }
        if ($anno < $this->annoactual) {
            $this->emit('msg-error', 'El año del lote esta fuera de rango...');
            $this->emit('enable-cajas', ["tipomov" => $this->tipomov, "muestrac" => 0, "nlotes" => $this->lotesprodsum]);
            $this->emit('enable-grabar', $this->tregistros);
            $this->todobien = false;
            return false;
        }
        $this->todobien = 4;
        $this->emit('enable-cajas', [
            "nlotes" => $this->lotesprodsum,
            "grabar" => "S",
            "tipomov" => $this->tipomov,
            "muestrac" => 0,
            "registros" => $this->tregistros
        ]);
        $this->emit('enable-grabar', $this->tregistros);
    }


    public function validaCantidad($valor)
    {
        $this->lacantidad = $valor;

        if (floatval($this->lacantidad) < 1 || $this->lacantidad == '') {
            $this->emit('msg-error', "La Cantidad debe ser mayor 1... Favor Revisar");
            $this->emit('enable-cajas', ["tipomov" => $this->tipomov, "muestrac" => 0, "nlotes" => $this->lotesprodsum]);
            return false;
        }

        if ($this->tipomov == "2") {
            if ($this->elstock <= $valor) {
                $this->emit('msg-error', "La Cantidad es mayor a la Existencia Actual... Favor Revisar");
                $this->lacantidad = '';
                //$this->emit('enable-cajas',["nlotes"=>$this->lotesprodsum, "grabar"=>"S",
                // "tipomov"=>$this->tipomov, "muestrac"=>0, "registros"=>$this->tregistros]);

                //["tipomov"=>$this->tipomov, "muestrac"=>0, "nlotes"=>$this->lotesprodsum]
                //$this->emit('enable-grabar',$this->tregistros);
                return false;
            } else {
                $this->emit('enable-grabar', [
                    "nlotes" => $this->lotesprodsum,
                    "grabar" => "S",
                    "tipomov" => $this->tipomov,
                    "muestrac" => 0,
                    "registros" => $this->tregistros
                ]);
            }
        } else {
            //$this->emit('enable-cajas',["tipomov"=>$this->tipomov, "muestrac"=>0, "nlotes"=>$this->lotesprodsum]);
            $this->emit('enable-grabar', [
                "nlotes" => $this->lotesprodsum,
                "grabar" => "S",
                "tipomov" => $this->tipomov,
                "muestrac" => 0,
                "registros" => $this->tregistros
            ]);

            //$this->emit('enable-grabar',$this->tregistros);
        }
    }

    public function asgLote($valor)
    {
        $this->loteid = $valor;
        $this->emit('enable-cajas', ["tipomov" => $this->tipomov, "muestrac" => 0, "nlotes" => $this->lotesprodsum]);
        //$this->emit('enable-grabar',$this->tregistros);
    }

    public function saveLine()
    {
        if (floatval($this->lacantidad) < 1 || $this->lacantidad == '') {
            $this->emit('msg-error', "La Cantidad debe ser mayor 1... Favor Revisar");
            $this->emit('enable-cajas', ["tipomov" => $this->tipomov, "muestrac" => 0, "nlotes" => $this->lotesprodsum]);
            return false;
        }

        $cvence = '';
        $cond = "nuevo";
        //
        if ($this->loteid == '' && $this->newlote == '') {
            $cvence = '';
            $cond = "";
        }

        if ($this->loteid == '' && $this->newlote != '') {
            $cvence = $this->newlote;
            $cond = "nuevo";
        }

        if ($this->loteid > 0 && $this->newlote == '') {
            $vence = Vencimiento::find($this->loteid);
            $cvence = $vence->mes . "/" . $vence->anno;
            $cond = "viejo";
        }

        if ($this->loteid != '' && $this->newlote != '') {
            $cvence = $this->newlote;
            $cond = "nuevo";
        }
        //
        if ($this->lotesprodsum > 0 && $this->loteid == 0 && $this->newlote == '') {
            $this->emit('msg-error', 'Debe Seleccionar un lote de vencimiento...');
            $this->emit('enable-cajas', ["tipomov" => $this->tipomov, "muestrac" => 0, "nlotes" => $this->lotesprodsum]);
            return;
        }

        //
        $this->detalle->push([
            "reg" => $this->numeroreg,
            "productoid" => $this->productoid,
            "producto" => $this->lproducto,
            "cantidad" => $this->lacantidad,
            "tipomov" => $this->tipomov,
            "unitario" => 0.00,
            "cvence" => $cvence,
            "lotevence" => $this->loteid,
            "monto" => 0.00,
            "condicionlote" => $cond,
            "lotenuevo" => $this->newlote
        ]);
        //
        $this->numeroreg += 1;
        $this->tregistros += 1;
        //
        $this->lproducto = '';
        $this->lacantidad = '';
        $this->loteid = 0;
        //
        $this->emit('enable-bodega', 1);
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
        $lafecha = date('Y-m-d');
        $this->lafecha = $lafecha;
        $this->lproducto = '';
        $this->numeroreg = 0;
        $this->bodega = '';
        $this->lacantidad = '';
        $this->lotesprodsum = 0;
        $this->concepto = '';
        $this->loteid = 0;
        $this->tregistros = 0;
        $this->lotevence = [];
        $this->detalle = collect([]);
        $this->tipomov = '';
        $this->newlote = '';
        $this->todobien = 0;
    }

    //escuchar eventos y ejecutar acción solicitada
    protected  $listeners = [
        'deleteRow'         => 'destroy'
    ];


    public function grabarBd()
    {
        //
        if ($this->tipomov == '') {
            return;
        }

        $eltipo = '';
        $elmov = '';
        //
        if ($this->tipomov == '1') {
            $eltipo = "EAJU";
            $elmov = "E";
        } else {
            $eltipo = "SAJU";
            $elmov = "S";
        }

        //iniciamos transacción
        DB::beginTransaction();
        try {
            //Head Registrando Salida
            $head = InventoryHead::create([
                'bodega'        => $this->bodega,
                'bodegadestino' => 0,
                'fecha'         => Carbon::parse($this->lafecha),
                'tipodoc'       => $eltipo,
                'estatus'       => 0,
                'user_id'       => auth()->user()->id,
                'concepto'      => $this->concepto,
            ]);

            //Detalle Registrando Salida
            foreach ($this->detalle as $det) {
                $milotevence = $det['lotevence'];
                //
                if ($det['condicionlote'] == 'nuevo') {
                    if (
                        floatval(substr($det['lotenuevo'], 0, 2)) >= 1 && floatval(substr($det['lotenuevo'], 0, 2)) <= 12
                        && floatval(substr($det['lotenuevo'], 3, 4)) >= $this->annoactual
                    ) {
                        $ltvc = Vencimiento::create([
                            'bodega'        => $head->bodega,
                            'producto_id'   => $det['productoid'],
                            'mes'           => floatval(substr($det['lotenuevo'], 0, 2)),
                            'anno'          => floatval(substr($det['lotenuevo'], 3, 4)),
                            'entradas'      => $det['cantidad'],
                            'total'         => $det['cantidad']
                        ]);
                        $milotevence = $ltvc->id;
                    }
                }
                if ($det['condicionlote'] == 'viejo' && $det['lotevence'] > 0) {
                    $ltvc = Vencimiento::find($det['lotevence']);
                    if ($ltvc) {
                        $ltvc->entradas += $det['cantidad'];
                        $ltvc->total = $ltvc->entradas - $ltvc->salidas;
                        $ltvc->save();
                        $milotevence = $ltvc->id;
                    }
                }
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
                    'preventa'      => $produx->preventa,
                    'subtotal'      => round($det['cantidad'] * $produx->costo, 4),
                    'gravado'       => round($det['cantidad'] * $produx->costo, 4),
                    'iva'           => round((round($det['cantidad'] * $produx->costo, 4)) * $this->tasaiva, 4),
                    'total'         => round(round($det['cantidad'] * $produx->costo, 4) + round((round($det['cantidad'] * $produx->costo, 4)) * $this->tasaiva, 4), 2),
                    'tasaiva'       => $this->tasaiva * 100,
                    'user_id'       => auth()->user()->id,
                    'lotevence'     => $milotevence, //$ltvc->id,
                    'estatus'       => 0
                ]);
                //
            }
            //confirmamos transacción
            DB::commit();
            $this->resetInput();
            $this->emit('msgok', 'El Ajuste ha sido generado. . .');
            $this->emit('confirmaOrden', $head->id);
            $this->emit('print-mov',  $head->id);
        } catch (\Exception $e) {
            //para no tener inconsistencia de la info, deshacemos los cambios en caso de que algo falle
            DB::rollback();
            $status = $e->getMessage();
            dd($e);
        }
    }

    //método para eliminar un registro dado
    public function destroy($id)
    {
        $nuevodet = collect([]);
        foreach ($this->detalle as $rs) {
            if ($rs['reg'] != $id) {
                $nuevodet->push([
                    "reg" => $rs['reg'],
                    "productoid" => $rs['productoid'],
                    "producto" => $rs['producto'],
                    "cantidad" => $rs['cantidad'],
                    "tipomov" => $rs['tipomov'],
                    "unitario" => $rs['unitario'],
                    "cvence" => $rs['cvence'],
                    "lotevence" => $rs['lotevence'],
                    "monto" => $rs['monto'],
                    "condicionlote" => $rs['condicionlote'],
                    "lotenuevo" => $rs['lotenuevo']
                ]);
            }
        }
        $this->detalle = $nuevodet;
        $this->tregistros = $this->detalle->count();
    }
}

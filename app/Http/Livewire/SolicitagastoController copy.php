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
use App\Destino;
use App\Productstock;
use App\Reqhead;
use App\Detareq;
use App\Generalsetup;
use App\User;

class SolicitagastoController extends Component
{
    use WithPagination;


    //properties
    public  $selected_id, $search;               //para búsquedas y fila seleccionada
    private $pagination = 4;                     //paginación de tabla

    public $lafecha, $bodega, $destino, $bodegas, $destinos, $detalle, $modous = 0;
    public $tregistros, $numeroreg, $tasaiva, $concepto;
    public $elstock, $lacantidad, $elprecio, $lproducto;

    public function mount()
    {
        $lafecha = date('Y-m-d');
        $this->lafecha = $lafecha;
        //
        $this->detalle = collect([]);
        $this->tregistros = 0;
        $this->numeroreg = 0;
        $this->bodega = '';
        $this->lproducto = '';
        $this->concepto = '';
        //
        $this->destino = auth()->user()->destino_pred;
        $this->bodega = auth()->user()->bodega_pred;
        //
        if (TRIM(auth()->user()->bodega_pred) == '')
            $this->modous = 1;

        //
        $empresa = Generalsetup::first();
        $this->tasaiva = round($empresa->piva / 100, 2);
        //
    }

    //método que se ejecuta al inciar el componente
    public function render()
    {
        if (strlen($this->lproducto) > 0) {
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
                ->orwhere('presentacion', 'like', '%' . $this->lproducto . '%')
                ->orwhere('products.id', 'like', '%' . $this->lproducto . '%')
                ->get();
        } else {
            $this->productos = [];
        }
        //
        if ($this->tregistros > 0) {
            $this->emit('enable-bodega', 2);
        }

        $this->bodegas      = Warehouse::where('estatus', '=', '0')->get();
        $this->destinos     = Destino::where('estatus', '=', '0')->get();

        return view('livewire.solicitagasto.component');
    }

    public function asgBodega($valor)
    {
        $this->bodega = $valor;
    }

    public function asgDestino($valor)
    {
        $this->destino = $valor;
    }

    public function asgConcepto($valor)
    {
        $this->concepto = $valor;
    }

    public function seleProducto($reg)
    {
        $this->lacantidad = '';
        $elprecio = 0;

        $prodJson = json_decode($reg);
        $this->lproducto = TRIM($prodJson->producto);  //." ".TRIM($prodJson->presentacion);
        $this->productoid = $prodJson->id;
        $this->elprecio = round($prodJson->costo, 4);

        $this->elstock = '';
        $stock = Productstock::where('producto_id', '=', $this->productoid)
            ->where('bodega', '=', $this->bodega)->first();
        if ($stock) {
            $this->elstock = $stock->existencia;
        }
        //
        $this->emit('enable-cajas', 1);
        //
        if ($this->tregistros > 0) {
            $this->emit('enable.bodega', 2);
        }
    }


    public function validaCantidad($valor)
    {
        /*         if ($this->elstock < $valor)
        {
            $this->emit('msg-error', "La Cantidad es mayor a la Existencia Actual... Favor Revisar");
            $this->lacantidad='';
            $this->emit('enable-cajas',1);
        }
        else
        {
 */
        $this->emit('enable-cajas', 0);
        $this->emit('enable-grabalinea');

        //}
    }

    public function saveLine()
    {

        $preciov = round($this->elprecio * $this->lacantidad, 4);
        $iv = round($preciov * $this->tasaiva, 4);
        $monto = round($preciov + $iv, 2);
        //
        $this->detalle->push([
            "reg" => $this->numeroreg,
            "productoid" => $this->productoid,
            "producto" => $this->lproducto,
            "presentacion"  => $this->lpresentacion,
            "manejopor"     => $this->lmanejado,
            "cantidad" => $this->lacantidad,
            "unitario" => round($monto / $this->lacantidad, 4),
            "monto" => $monto
        ]);
        //
        $this->numeroreg += 1;
        $this->tregistros += 1;
        //
        $this->lproducto = '';
        $this->lpresentacion = '';
        $this->lmanejado = '';
        $this->lacantidad = '';
        $this->elprecio = 0;
        //
        $this->emit('enable-bodega', 2);
    }

    public function descartarOperacion()
    {
        $this->resetInput();
    }

    public function resetInput()
    {
        $this->detalle = collect([]);
        $this->tregistros = 0;
        $this->numeroreg = 0;
        $this->pacienteid = 0;
        $this->bodega = '';
        $this->destino = '';
        $this->concepto = '';
        //
        $this->elreferente = '';
        $this->elcuarto = '';
        $this->lafechayhora = '';
        //
        $this->emit('enable-bodega', 1);
    }


    //escuchar eventos y ejecutar acción solicitada
    protected  $listeners = [
        'deleteRow'         => 'destroy'
    ];


    public function grabarBd()
    {
        if ($this->bodega < 1 || $this->destino < 1) {
            return;
        }

        //iniciamos transacción
        DB::beginTransaction();
        try {
            //Head Registrando Salida
            $head = Reqhead::create([
                'bodega'        => $this->bodega,
                'destino_id'    => $this->destino,
                'fecha'         => Carbon::parse($this->lafecha),
                'tipodoc'       => 'PGAS',
                'estatus'       => 0,
                'user_id'       => auth()->user()->id,
                'concepto'      => TRIM($this->concepto),
            ]);

            //Detalle Registrando Salida
            foreach ($this->detalle as $det) {

                $produx = Product::find($det['productoid']);

                $detabod = Detareq::create([
                    'bodega'        => $head->bodega,
                    'tipodoc'       => $head->tipodoc,
                    'numerodoc'     => $head->id,
                    'fecha'         => $head->fecha, //Carbon::parse($this->fecha),
                    'producto_id'   => $det['productoid'],
                    'producto'      => $det['producto'],
                    'cantidad'      => $det['cantidad'],
                    'user_id'       => $head->user_id,
                    'precosto'      => $produx->costo,
                    'preventa'      => $produx->precioventa,
                    'subtotal'      => round($det['cantidad'] * $produx->costo, 4),
                    'estatus'       => 0
                ]);
                //

            }
            DB::commit();
            $this->resetInput();
            $this->emit('msgok', 'Solicitud ha sido guardada. . .');
            $this->emit('confirmaOrden', $head->id);
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
                    "unitario" => $rs['unitario'],
                    "monto" => $rs['monto']
                ]);
            }
        }
        //
        $this->detalle = $nuevodet;
        $this->tregistros = $this->detalle->count();
    }
}

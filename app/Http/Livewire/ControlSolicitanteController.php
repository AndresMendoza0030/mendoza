<?php

namespace App\Http\Livewire;

use App\Exports\ExportCatalogoProd;
use App\Exports\ExportCompraSum;
use App\Exports\ExportCompraDet;
use App\Exports\ExportConsumoSum;
use App\Exports\ExportConsumoDet;


use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Livewire\WithPagination;
use Livewire\Component;

use App\Inventoryhead;
use App\Movdetail;
use App\Product;
use App\Productclass;
use App\Productline;
use App\Documento;
use App\ClienteProveedor;
use App\Warehouse;

use App\User;
use Spatie\Permission\Models\Role;

class ControlSolicitanteController extends Component
{

    use WithPagination;

    protected $paginationTheme = 'bootstrap';


	//properties
    public  $mianno, $mimes, $lafecha, $desde, $hasta, $datos, $bodega, $bodegas=[];   			//para búsquedas y fila seleccionada
    public $midestino='', $bodpred=0;
    private $pagination = 5;         			//paginación de tabla

    

    public function mount()
    {
        $this->mimes = date('m');
        $this->mianno= date('Y');
        $this->lafecha = date("Y-m-d");
        $this->desde = date("Y-m-d");
        $this->hasta = date("Y-m-d");
        //
        $this->midestino=auth()->user()->destino_pred;
        $this->bodega=auth()->user()->bodega_pred;
        //
    }

    //método que se ejecuta al inciar el componente
    public function render()
    {
        //cuentas contables
        $this->bodegas =Warehouse::select('warehouses.*')->get();

        $this->datos=[];
        return view('livewire.controlsolicitante.component', ['datos' => $this->datos,]);
    }


    public function rpConsumoSumPer($desde,$hasta,$bod)
    {
        if ($this->midestino ='')
        {
            $this->emit('msg-error','El solicitante no tiene definido un Destino..');
            return;
        }

        $this->eltitulo= "";
        $labod=Warehouse::find($bod)->nombre;
        //
        if ($bod!='0')
        {
            $detalle = Movdetail::leftjoin('inventoryhead as ih','ih.id','=','movdetails.numerodoc')
            ->leftjoin('productclasses as cl','cl.id','movdetails.clase')
            ->leftjoin('destinos as ds','ds.id','ih.destinogasto')
            ->select('movdetails.*','ds.nombre as destino', 'cl.nombre as cclase',
            DB::raw("SUM(movdetails.gravado) as tgravado"))
            ->where('movdetails.tipodoc','SGAS')->where('ih.reversado','NO')->where('movdetails.bodega',$bod)
            ->where('ih.destinogasto',$this->midestino)
            ->whereDate('movdetails.fecha','>=',$desde)->whereDate('movdetails.fecha','<=',$hasta)
            ->groupby('ih.destinogasto')->groupby('movdetails.clase')
            ->orderby('ih.destinogasto')->orderby('movdetails.clase')->get();
        }
        else
        {
            $detalle = Movdetail::leftjoin('inventoryhead as ih','ih.id','=','movdetails.numerodoc')
            ->leftjoin('productclasses as cl','cl.id','movdetails.clase')
            ->leftjoin('destinos as ds','ds.id','ih.destinogasto')
            ->select('movdetails.*','ds.nombre as destino', 'cl.nombre as cclase',
            DB::raw("SUM(movdetails.gravado) as tgravado"))
            ->where('movdetails.tipodoc','SGAS')->where('ih.reversado','NO')
            ->where('ih.destinogasto',$this->midestino)
            ->whereDate('movdetails.fecha','>=',$desde)->whereDate('movdetails.fecha','<=',$hasta)
            ->groupby('ih.bodega')->groupby('ih.destinogasto')->groupby('movdetails.clase')
            ->orderby('ih.bodega')->orderby('ih.destinogasto')->orderby('movdetails.clase')->get();
        }

        //

        return Excel::download(new ExportConsumoSum($detalle, $bod, $labod, $desde, $hasta ), 'consumoxmes-sum.xlsx');
    }


    public function rpConsumoDetPer($desde,$hasta,$bod)
    {
        $this->eltitulo= "";
        $labod=Warehouse::find($bod)->nombre;
        //
        $detalle = Movdetail::leftjoin('inventoryhead as ih','ih.id','=','movdetails.numerodoc')
        ->leftjoin('productclasses as cl','cl.id','movdetails.clase')
        ->leftjoin('destinos as ds','ds.id','ih.destinogasto')
        ->select('movdetails.*','ds.nombre as destino', 'cl.nombre as cclase', 'ih.destinogasto as destinogasto',
        DB::raw("SUM(movdetails.cantidad) as tcantidad, SUM(movdetails.gravado) as tgravado "))
        ->where('movdetails.tipodoc','SGAS')->where('ih.reversado','NO')->where('movdetails.bodega',$bod)
        ->whereDate('movdetails.fecha','>=',$desde)->whereDate('movdetails.fecha','<=',$hasta)
        ->groupby('movdetails.producto_id')
        ->orderby('ih.destinogasto')->orderby('movdetails.clase')->orderby('movdetails.producto_id')->get();

        //

        return Excel::download(new ExportConsumoDet($detalle, $bod, $labod, $desde, $hasta ), 'consumoxmes-det.xlsx');
    }

    public function nombremes($m)
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

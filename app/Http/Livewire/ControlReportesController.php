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

class ControlReportesController extends Component
{

    use WithPagination;

    protected $paginationTheme = 'bootstrap';


	//properties
    public  $mianno, $mimes, $lafecha, $desde, $hasta, $datos, $bodega, $bodegas=[];   			//para búsquedas y fila seleccionada
    private $pagination = 5;         			//paginación de tabla

    

    public function mount()
    {
        $this->mimes = date('m');
        $this->mianno= date('Y');
        $this->lafecha = date("Y-m-d");
        $this->desde = date("Y-m-d");
        $this->hasta = date("Y-m-d");
        //
    }

    //método que se ejecuta al inciar el componente
    public function render()
    {
        //cuentas contables
        $this->bodegas =Warehouse::select('warehouses.*')->get();

        $this->datos=[];
        return view('livewire.controlreportes.component', ['datos' => $this->datos,]);
    }



    public function rpCatalogoProducto()
    {
        $this->eltitulo= "";
        //
        $detalle = Product::leftjoin('productclasses as cl','products.clase','=','cl.id')
        ->leftjoin('productlines as ln','products.linea','=','ln.id')
        ->select('products.*','cl.nombre as cclase','ln.nombre as llinea')
        ->orderby('clase')->orderby('nombre')->get();
        //
        return Excel::download(new ExportCatalogoProd($detalle), 'listaproducto.xlsx');
    }


    public function rpCompraSumPer($desde,$hasta,$bod)
    {
        $this->eltitulo= "";
        $labod=Warehouse::find($bod)->nombre;
        //
        $detalle = Inventoryhead::leftjoin('cliente_proveedores as pr','pr.id','=','inventoryhead.sujeto_id')
        ->leftjoin('documentos as dc','dc.id','inventoryhead.tpdocufiscal')
        ->select('inventoryhead.*','pr.razon as razon', 'dc.documento as documento')->where('inventoryhead.tipodoc','CPRA')
        ->where('inventoryhead.reversado','NO')->where('inventoryhead.bodega',$bod)
        ->whereDate('inventoryhead.fecha','>=',$desde)->whereDate('inventoryhead.fecha','<=',$hasta)
        ->orderby('fecha')->get();
        //

        return Excel::download(new ExportCompraSum($detalle, $bod, $labod, $desde, $hasta ), 'comprasxmes-sum.xlsx');
    }


    public function rpCompraDetPer($desde,$hasta,$bod)
    {
        $this->eltitulo= "";
        $labod=Warehouse::find($bod)->nombre;
        //
        $detalle = Movdetail::leftjoin('inventoryhead as ih','ih.id','=','movdetails.numerodoc')
        ->leftjoin('documentos as dc','dc.id','ih.tpdocufiscal')
        ->select('movdetails.*','ih.sujeto as razon', 'dc.documento as documento','ih.gravado as hgravado', 'ih.iva as hiva',
        'ih.total as htotal', 'ih.numdocufiscal as numdocufiscal')->where('movdetails.tipodoc','CPRA')->where('movdetails.bodega',$bod)
        ->whereDate('movdetails.fecha','>=',$desde)->whereDate('movdetails.fecha','<=',$hasta)
        ->orderby('fecha')->orderby('razon')->get();
        //

        return Excel::download(new ExportCompraDet($detalle, $bod, $labod, $desde, $hasta ), 'comprasxmes-det.xlsx');
    }

    public function rpConsumoSumPer($desde,$hasta,$bod)
    {
        $this->eltitulo= "";
        $labod=Warehouse::find($bod)->nombre;
        //
        $detalle = Movdetail::leftjoin('inventoryhead as ih','ih.id','=','movdetails.numerodoc')
        ->leftjoin('productclasses as cl','cl.id','movdetails.clase')
        ->leftjoin('destinos as ds','ds.id','ih.destinogasto')
        ->select('movdetails.*','ds.nombre as destino', 'cl.nombre as cclase',
        DB::raw("SUM(movdetails.gravado) as tgravado"))
        ->where('movdetails.tipodoc','SGAS')->where('ih.reversado','NO')->where('movdetails.bodega',$bod)
        ->whereDate('movdetails.fecha','>=',$desde)->whereDate('movdetails.fecha','<=',$hasta)
        ->groupby('ih.destinogasto')->groupby('movdetails.clase')
        ->orderby('ih.destinogasto')->orderby('movdetails.clase')->get();

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

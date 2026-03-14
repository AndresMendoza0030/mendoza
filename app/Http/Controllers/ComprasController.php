<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Carbon\Carbon;

use Illuminate\Support\Collection;

use App\Manufacturer;
use App\Warehouse;
use App\Productclass;
use App\Productline;
use App\Product;
use App\Productsstocks;
use App\Productclassinvoice;
use App\ClienteProveedor;
use App\Inventoryhead;
use App\Movdetail;
use App\Invoice;
use App\Vencimiento;
use App\rxInvoice;

class ComprasController extends Controller
{

    //informacion del producto
    public function infoproducto($idp)
	{
        $this->llaveid=$idp;

        $encontrado=0;

        $encontrado= DB::table('products')->where('estatus','=','0')
        ->where('barra', '=', $idp)->get()->count();
        if ($encontrado>0)
        {
            $infoproducto=DB::table('products')
            ->select(DB::raw("CONCAT(TRIM(products.nombre),' ',TRIM(products.presentacion)) as nombreproducto"),
            'id','manejopor','ultimocosto','nombre')
            ->where('estatus','=','0')
            ->where('barra', '=', $idp)->get();
            return ($infoproducto);
        }
        else
        {
            return (0);
        }
        // json_encode($this->infoproducto);
	}	

    //informacion del stock de producto por bodega
    public function productStock($idp,$idbd)
	{
        $this->llaveid=$idp;

        $existe=0.00;

        $stock= DB::table('productstocks')
        ->where('producto_id', '=', $idp)->where('bodega', '=', $idbd)
        ->get()->count();

        if ($stock>0)
        {
            $infoproducto=DB::table('productstocks')
            ->select('producto_id','bodega','existencia')
            ->where('producto_id', '=', $idp)->where('bodega', '=', $idbd)
            ->first();   

            $existe=$infoproducto->existencia;

            return ($existe);
        }
        else
        {
            return (0.00);
        }
        // json_encode($this->infoproducto);
	}	

    public function lotesBodega($idp,$idbd)
	{
        $lotes= Vencimiento::where('producto_id','=',$idp)
        ->where('bodega','=',$idbd)->where('total','>',0)
        ->select('producto_id', 'bodega','total','mes','anno','id',
        DB::raw("CONCAT(TRIM(mes),'/',TRIM(anno)) as clote"))
        ->orderby('anno')->orderby('mes')->get();


        if ($lotes)
        {
            return ($lotes);
        }
        else
        {
            return ('');
        }
	}	

    //confirma existencia de crrelativo factura
    public function existeDocVenta($tipdoc,$seriedoc,$corrdoc)
	{
        $existe='';

        $resultado= Invoice::where('tipofac','=',$tipdoc)
        ->where('serie','=',$seriedoc)->where('numerofac','=',$corrdoc)
        ->get()->count();

        if ($resultado>0)
        {
            return ("SI");
        }
        else
        {
            return ("NO");
        }
        // json_encode($this->infoproducto);
	}	

    public function existeDocVentaRx($tipdoc,$seriedoc,$corrdoc)
	{
        $existe='';

        $resultado= rxInvoice::where('tipofac','=',$tipdoc)
        ->where('serie','=',$seriedoc)->where('numerofac','=',$corrdoc)
        ->get()->count();

        if ($resultado>0)
        {
            return ("SI");
        }
        else
        {
            return ("NO");
        }
        // json_encode($this->infoproducto);
	}	

}

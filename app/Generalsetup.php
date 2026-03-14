<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Generalsetup extends Model
{
    protected $table = 'generalsetup';
    
    protected $fillable = ['empresa','nit','nrc','giro',
'direccion','piva','prenta','pretiene','ppercibe',
'corrfactura','seriefactura','resolucionfactura',
'corrcredito','seriecredito','resolucioncredito',
'corrncredito','seriencredito','resolucionncredito',
'correxcluido','serieexcluido','resolucionexcluido','ciudad'];

}



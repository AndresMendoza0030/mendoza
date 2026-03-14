<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClienteProveedor extends Model
{
    protected $table = 'cliente_proveedores';
    
    protected $fillable = ['razon','tipo', 'documento_id','nit','nrc','giro','user_id',
    'dui','fecha','hora','email','direccion','telefono','contacto','categoria','estatus','plazo','credito','cuentacontable'];
}

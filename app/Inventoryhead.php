<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventoryhead extends Model
{
    protected $table = 'inventoryhead';
    
    protected $fillable = ['tipodoc','fecha', 'bodega','bodegadestino','docucontra',
    'fecha','paciente_id','sujeto_id','sujeto','tpdocufiscal','numdocufiscal','serie','subtotal',
    'concepto','estatus','flete','gravado','iva','retencion','percepcion','total','fiscal','fecha_doc',
    'user_id','destinogasto','pedido','no_provision','cajanumero','seguromedico','gestion_seguro'];

    public function bodega()
    {
    	return $this->HasOne(Warehouse::class, 'id', 'bodega');
    }

}

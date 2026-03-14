<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Procedurebypac extends Model
{
    protected $table = 'proceduresbypacs';
    
    protected $fillable = ['id','paciente_id','procedure','fecha','cirujano','anestesista',
    'anestesia_tp','horaentrada','horasalida','estatus','user_id'];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reqhead extends Model
{
    protected $table = 'reqhead';

    protected $fillable = [
        'tipodoc',
        'bodega',
        'bodegadestino',
        'docucontra',
        'fecha',
        'paciente_id',
        'autorizado',
        'autorizadoen',
        'destino_id',
        'user_id',
        'autoriza_id',
        'despacha_mov',
        'despacha_id',
        'despachaen',
        'estatus',
        'despachado',
        'idoriginal'
    ];

    // Relación con la tabla 'warehouses' (bodega)
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'bodega', 'id');
    }

    // Relación con la tabla 'destinos'
    public function destinoRel()
    {
        return $this->belongsTo(Destino::class, 'destino_id', 'id');
    }

    // Relación con detalles (Detareq)
    // asumiendo que Detareq usa 'numerodoc' = 'reqhead.id'
    public function detalles()
    {
        return $this->hasMany(Detareq::class, 'numerodoc', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

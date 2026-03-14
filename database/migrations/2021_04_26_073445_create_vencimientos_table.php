<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVencimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vencimientos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->smallInteger('mes');            
            $table->smallInteger('anno');            
            $table->smallInteger('bodega');     
            $table->smallInteger('producto_id');
            $table->unsignedBigInteger('entradas');
            $table->unsignedBigInteger('salidas');
            $table->unsignedBigInteger('existencia');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vencimientos');
    }
}

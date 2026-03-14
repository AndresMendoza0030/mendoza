<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePacientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('apellidos',50)->nullable();
            $table->string('nombres',50)->nullable();

            $table->unsignedBigInteger('idunico');            
            //*$table->foreign('idunico')->references('id')->on('unicopacientes');            

            $table->unsignedBigInteger('room');            
            //*$table->foreign('room')->references('id')->on('rooms');            

            $table->unsignedBigInteger('referente');            
            $table->foreign('referente')->references('id')->on('referentes');            

            $table->date('fecha');            
            $table->time('hora', 2);            

            $table->date('fechanace');
            $table->string('edad',20)->nullable();
            $table->enum('tipopaciente',['CAMA','AMBU'])->default('CAMA');
            $table->string('docu_id',20)->nullable();
            $table->string('movil',12)->nullable();
            $table->string('direccion',80)->nullable();
            $table->unsignedBigInteger('ciudad');            
            $table->foreign('ciudad')->references('id')->on('ciudades');            

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');            


            $table->string('responsable',80)->nullable();
            $table->string('responsable_docu_id',20)->nullable();
            $table->string('responsable_movil',12)->nullable();
            $table->string('reponsable_direccion',80)->nullable();

            $table->enum('genero',['MASCULINO', 'FEMENINO'])->default('MASCULINO');
            $table->enum('abierto',['SI', 'NO'])->default('SI');
            $table->enum('facturado',['SI', 'NO'])->default('NO');

            $table->date('fechacierre')->nullable();            
            $table->string('horacierre',15)->nullable();            
            $table->string('comentacierre',50)->nullable();
            $table->enum('tipocierre',['EGRESO', 'DEFUNCION'])->default('EGRESO');

            $table->unsignedBigInteger('cierrepor')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pacientes');
    }
}

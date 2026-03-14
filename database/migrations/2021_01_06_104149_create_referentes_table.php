<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referentes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('apellidos',50);
            $table->string('nombres',50);
            $table->string('jvpm',30);
            $table->string('nit',14);
            $table->string('nrc',10);
            $table->string('dui',10);

            $table->unsignedBigInteger('especialidad');        
            //*$table->foreign('especialidad')->references('id')->on('especialidades');            

            $table->unsignedBigInteger('ciudad');        
            //**$table->foreign('ciudad')->references('id')->on('ciudades');            

            $table->string('email',80)->nullable();
            $table->string('direccion',100)->nullable();
            $table->string('telefono',12)->nullable();
            $table->string('contacto',80)->nullable();

            $table->enum('tipo',['CONTRIBUYENTE','NO REGISTRADO'])->default('NO REGISTRADO');
            $table->enum('genero',['MASCULINO','FEMENINO'])->default('MASCULINO');

            $table->unsignedSmallInteger('estatus');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referentes');
    }
}

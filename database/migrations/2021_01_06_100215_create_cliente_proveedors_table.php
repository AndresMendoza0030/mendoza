<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClienteProveedorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cliente_proveedores', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('razon',100)->nullable();
            $table->enum('tipo',['CLIENTE', 'PROVEEDOR'])->default('PROVEEDOR');
            $table->string('documento_id',50)->nullable();
            $table->string('nit',14)->nullable();
            $table->string('nrc',10)->nullable();
            $table->string('dui',10)->nullable();

            $table->string('email',80)->nullable();
            $table->string('direccion',100)->nullable();
            $table->string('telefono',12)->nullable();
            $table->string('contacto',80)->nullable();

            $table->enum('categoria',['MEDIANO','GRANDE'])->default('GRANDE');

            $table->unsignedSmallInteger('estatus')->default('0');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cliente_proveedores');
    }
}

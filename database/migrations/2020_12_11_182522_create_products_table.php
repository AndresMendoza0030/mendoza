<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('nombre',100)->nullable();
            $table->string('barra',100)->nullable();            
            $table->string('presentacion',80)->nullable();
            $table->enum('tipo',['PRODUCTO','SERVICIO'])->default('PRODUCTO');
            $table->enum('cambioprecio',['NO','SI'])->default('NO');
            $table->unsignedSmallInteger('estatus')->default(0);

            $table->unsignedBigInteger('clase')->default(0);;
            //$table->foreign('clase')->references('id')->on('productclasses');            

            $table->unsignedBigInteger('linea')->default(0);;        
            //$table->foreign('linea')->references('id')->on('productlines');            

            $table->unsignedBigInteger('lineafac')->default(0);;
            //$table->foreign('lineafac')->references('id')->on('productclassinvoices');            

            $table->unsignedBigInteger('fabricante')->default(0);;
            //$table->foreign('fabricante')->references('id')->on('manufacturers');            

            $table->enum('manejopor',['UNIDAD', 'PRESENTACION'])->default('PRESENTACION');

            $table->string('registro',50)->nullable();            
            $table->unsignedSmallInteger('principio')->default(0);

            $table->decimal('costo',15,4)->defult(0);
            $table->decimal('ultimocosto',15,4)->defult(0);
            $table->decimal('costoanterior',15,4)->defult(0);
            $table->decimal('precioventa',15,4)->defult(0);

            $table->date('ultimacompra')->nullable();
            $table->date('ultimaventa')->nullable();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}

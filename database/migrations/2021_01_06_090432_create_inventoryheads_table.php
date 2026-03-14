<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryheadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('inventoryhead', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('tipodoc',4)->nullable();
            $table->unsignedBigInteger('bodega');            
            $table->foreign('bodega')->references('id')->on('warehouses');            

            $table->unsignedBigInteger('destinogasto')->default(0);            
            $table->unsignedBigInteger('pedido')->default(0);            

            $table->unsignedBigInteger('bodegadestino');            
            $table->foreign('bodegadestino')->references('id')->on('warehouses');            

            $table->unsignedBigInteger('docucontra');            
            $table->date('fecha');            

            $table->unsignedBigInteger('paciente_id');
            //*$table->foreign('paciente_id')->references('id')->on('pacientes');            

            $table->unsignedBigInteger('sujeto_id');
            //*$table->foreign('sujeto_id')->references('id')->on('clientesproveedores');            
            $table->string('sujeto',100)->nullable();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');            

            $table->string('tpdocufiscal',4)->nullable();
            $table->unsignedBigInteger('numdocufiscal');            
            $table->string('serie',40)->nullable();

            $table->string('concepto',100)->nullable();

            $table->unsignedSmallInteger('estatus');

            $table->decimal('flete',10,2)->defult(0);
            $table->decimal('gravado',15,4)->defult(0);
            $table->decimal('iva',15,4)->defult(0);
            $table->decimal('retencion',15,4)->defult(0);
            $table->decimal('percepcion',15,4)->defult(0);
            $table->decimal('total',10,2)->defult(0);

            $table->enum('fiscal',['SI', 'NO'])->default('NO');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventoryhead');
    }
}

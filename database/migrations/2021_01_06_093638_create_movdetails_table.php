<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovdetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movdetails', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('tipodoc',4)->nullable();
            $table->unsignedBigInteger('numerodoc');            
            $table->foreign('numerodoc')->references('id')->on('inventoryhead');            

            $table->date('fecha');            

            $table->unsignedBigInteger('paciente_id');
            //*$table->foreign('paciente_id')->references('id')->on('pacientes');            

            $table->unsignedBigInteger('bodega');            
            //*$table->foreign('bodega')->references('id')->on('warehouses');            

            $table->unsignedBigInteger('clase');            
            $table->foreign('clase')->references('id')->on('productclasses');            

            $table->unsignedBigInteger('linea');            
            //*$table->foreign('linea')->references('id')->on('productlines');            

            $table->unsignedBigInteger('lineafac');            
            //*$table->foreign('lineafac')->references('id')->on('productclassinvoices');            

            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('products');            
            $table->string('producto',100)->nullable();

            $table->decimal('anterior',10,2)->defult(0);
            $table->decimal('cantidad',10,2)->defult(0);
            $table->decimal('posterior',10,2)->defult(0);
            $table->decimal('devuelto',10,2)->defult(0);
            
            $table->string('movimiento',1)->nullable();

            $table->decimal('precosto',15,4)->defult(0);
            $table->decimal('preventa',15,4)->defult(0);

            $table->decimal('subtotal',15,4)->defult(0);            
            $table->decimal('pdescuento',10,2)->defult(0);
            $table->decimal('descuento',15,4)->defult(0);
            $table->decimal('gravado',15,4)->defult(0);
            $table->decimal('iva',15,4)->defult(0);
            $table->decimal('total',10,2)->defult(0);
            $table->decimal('tasaiva',10,2)->defult(0);

            $table->unsignedSmallInteger('lotevence')->defult(0);

            $table->decimal('admonval',15,4)->defult(0);
            $table->unsignedSmallInteger('admongp')->defult(0);

            $table->unsignedSmallInteger('revisado');

            $table->unsignedSmallInteger('estatus');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');            

            $table->unsignedBigInteger('revisauser_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movdetails');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicedetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoicedetails', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('tipofac',4)->nullable();
            $table->unsignedBigInteger('numerofac');            
            $table->string('serie',30)->nullable();
            $table->string('resolucion',30)->nullable();

            $table->unsignedBigInteger('lineafac');            
            $table->foreign('lineafac')->references('id')->on('productclassinvoices');            

            $table->unsignedBigInteger('paciente_id');

            $table->decimal('subtotal',15,4)->defult(0);            
            $table->decimal('pdescuento',10,2)->defult(0);
            $table->decimal('descuento',15,4)->defult(0);
            $table->decimal('gravado',15,4)->defult(0);
            $table->decimal('iva',15,4)->defult(0);
            $table->decimal('total',10,2)->defult(0);
            $table->decimal('tasaiva',10,2)->defult(0);

            $table->enum('control',['SI', 'NO'])->default('NO');

            $table->unsignedBigInteger('admon_class');
            $table->decimal('comision',10,2)->defult(0);
            $table->unsignedSmallInteger('admongp')->defult(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoicedetails');
    }
}

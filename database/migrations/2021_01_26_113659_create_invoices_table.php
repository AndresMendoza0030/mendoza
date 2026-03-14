<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('tipofac',4)->nullable();
            $table->unsignedBigInteger('numerofac')->defult(0);            
            $table->string('serie',30)->nullable();
            $table->unsignedSmallInteger('resolucion')->defult(0);            

            $table->unsignedBigInteger('numerodoc')->defult(0);
            $table->unsignedSmallInteger('referente')->defult(0);            
            $table->unsignedSmallInteger('paciente_id')->defult(0);            
            $table->date('fecha');            
                        
            $table->unsignedSmallInteger('sujeto_id')->defult(0);            
            $table->string('sujeto',80)->nullable();
            $table->string('nit',20)->nullable();
            $table->string('dui',15)->nullable();
            $table->string('nrc',15)->nullable();
            $table->string('giro',50)->nullable();
            $table->string('direccion',80)->nullable();

            $table->decimal('subtotal',15,4)->defult(0);            
            $table->decimal('pdescuento',10,2)->defult(0);
            $table->decimal('descuento',15,4)->defult(0);
            $table->decimal('gravado',15,4)->defult(0);
            $table->decimal('iva',15,4)->defult(0);
            $table->decimal('total',10,2)->defult(0);
            $table->decimal('abonos',10,2)->defult(0);
            $table->decimal('apagar',10,2)->defult(0);
            $table->decimal('saldo',10,2)->defult(0);
            $table->decimal('tasaiva',10,2)->defult(0);

            $table->unsignedSmallInteger('tipopago')->defult(0);            
            $table->unsignedSmallInteger('estatus')->defult(0);            
            $table->unsignedSmallInteger('borradopor')->defult(0);            
            $table->date('borradoen');            

            $table->unsignedSmallInteger('user_id')->defult(0);            
            $table->unsignedBigInteger('operacioncaja')->defult(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}

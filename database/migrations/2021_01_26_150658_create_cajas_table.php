<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCajasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caja', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->enum('movimiento',['E','S'])->default('E');
            $table->string('tipofac',4)->nullable();
            $table->unsignedBigInteger('numerofac')->defult(0);            
            $table->string('serie',30)->nullable();
            $table->unsignedSmallInteger('resolucion')->defult(0);            

            $table->unsignedBigInteger('numerodoc')->defult(0);
            $table->date('fecha');            
                        
            $table->decimal('acobrar',10,2)->defult(0);            
            $table->decimal('recibido',10,2)->defult(0);            
            $table->decimal('vuelto',10,2)->defult(0);
            $table->decimal('efectivo',10,2)->defult(0);
            $table->decimal('tarjeta',10,2)->defult(0);
            $table->decimal('cheque',10,2)->defult(0);
            $table->decimal('saldo',10,2)->defult(0);
            $table->string('referencia',80)->nullable();

            $table->unsignedSmallInteger('estatus')->defult(0);            
            $table->unsignedSmallInteger('borradopor')->defult(0);            
            $table->date('borradoen');            

            $table->unsignedSmallInteger('user_id')->defult(0);            

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('caja');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbonosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abonos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('paciente_id')->default(0);           
            $table->date('fecha');

            $table->decimal('efectivo',10,2)->defult(0);                                              
            $table->decimal('tarjeta',10,2)->defult(0);                                              
            $table->decimal('cheque',10,2)->defult(0);                                  
            $table->decimal('contable',10,2)->defult(0);                                  
            $table->decimal('valor',10,2)->defult(0);                                  
            $table->string('refetarjeta',10)->nullable();

            $table->string('concepto',80)->nullable();

            $table->unsignedSmallInteger('user_id')->nullable();
            $table->unsignedSmallInteger('caja_id')->default(0);            

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('abonos');
    }
}

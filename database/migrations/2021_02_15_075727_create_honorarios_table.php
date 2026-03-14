<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHonorariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('honorarios', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        
            $table->unsignedBigInteger('paciente_id')->defult(0);           
            $table->unsignedBigInteger('referente_id')->defult(0);
            $table->date('fecha');
            $table->decimal('valor',10,2)->defult(0);                                  
            $table->string('concepto',100)->nullable();
            $table->unsignedSmallInteger('estatus')->defult(0);            

            $table->enum('cobrado',['S','N'])->default('N');            
            $table->unsignedSmallInteger('user_id')->nullable();
            $table->unsignedSmallInteger('cobro_id')->default(0);            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('honorarios');
    }
}

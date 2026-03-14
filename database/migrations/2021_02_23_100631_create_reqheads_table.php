<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReqheadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reqhead', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('tipodoc',4)->nullable();
            $table->unsignedBigInteger('bodega');            

            $table->unsignedBigInteger('bodegadestino')->default(0);;
            $table->unsignedBigInteger('docucontra')->default(0);
            $table->date('fecha');
            $table->unsignedBigInteger('paciente_id')->default(0);;
            $table->unsignedBigInteger('destino_id')->default(0);;
            $table->unsignedBigInteger('user_id')->default(0);;

            $table->unsignedBigInteger('despacha_mov');            
            $table->unsignedBigInteger('despacha_id');
            $table->timestamp('despachaen', 0);

            $table->unsignedSmallInteger('estatus')->default(0);

            $table->enum('despachado',['S', 'N'])->default('N');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reqhead');
    }
}

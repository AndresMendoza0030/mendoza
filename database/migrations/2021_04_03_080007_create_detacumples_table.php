<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetacumpleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detacumple', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('tipodoc',4)->nullable();
            $table->unsignedBigInteger('numerodoc');            
            $table->date('fecha');            

            $table->unsignedBigInteger('paciente_id');

            $table->unsignedBigInteger('bodega');            

            $table->unsignedBigInteger('producto_id');
            $table->string('producto',100)->nullable();

            $table->decimal('cantidad',10,2)->defult(0);

            $table->decimal('precosto',15,4)->defult(0);
            $table->decimal('preventa',15,4)->defult(0);

            $table->decimal('subtotal',15,4)->defult(0);            

            $table->unsignedSmallInteger('cobrado');
            $table->unsignedSmallInteger('estatus');

            $table->unsignedBigInteger('user_id');

        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detacumple');
    }
}

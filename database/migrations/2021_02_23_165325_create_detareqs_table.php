<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetareqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detareqs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('tipodoc',4)->nullable();
            $table->unsignedBigInteger('numerodoc');            
            $table->date('fecha');            

            $table->unsignedBigInteger('bodega');     
            $table->unsignedBigInteger('idoriginal');                        
            $table->unsignedBigInteger('producto_id');
            $table->string('producto',100)->nullable();

            $table->decimal('cantidad',10,2)->defult(0);


            $table->decimal('precosto',15,4)->defult(0);
            $table->decimal('preventa',15,4)->defult(0);
            $table->decimal('subtotal',15,4)->defult(0);            
            $table->unsignedBigInteger('user_id');
            
            $table->unsignedSmallInteger('estatus');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detareqs');
    }
}

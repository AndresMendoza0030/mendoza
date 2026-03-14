<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralsetupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('generalsetup', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('empresa',100)->nullable();
            $table->string('nit',20)->nullable();
            $table->string('nrc',20)->nullable();
            $table->string('giro',100)->nullable();
            $table->string('direccion',100)->nullable();

            $table->decimal('piva',10,2)->defult(0);
            $table->decimal('prenta',10,2)->defult(0);                        
            $table->decimal('pretiene',10,2)->defult(0);
            $table->decimal('ppercibe',10,2)->defult(0);

            $table->unsignedBigInteger('corrfactura');            
            $table->string('seriefactura',30)->nullable();
            $table->string('resolucionfactura',30)->nullable();

            $table->unsignedBigInteger('corrcredito');            
            $table->string('seriecredito',30)->nullable();
            $table->string('resolucioncredito',30)->nullable();

            $table->unsignedBigInteger('corrtiquete');            
            $table->string('serietiquete',30)->nullable();
            $table->string('resoluciontiquete',30)->nullable();

            $table->unsignedBigInteger('corrncredito');            
            $table->string('seriecredito',30)->nullable();
            $table->string('resolucionncredito',30)->nullable();

            $table->unsignedBigInteger('correxcluido');            
            $table->string('serienexcluido',30)->nullable();
            $table->string('resolucionexcluido',30)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('generalsetup');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductclassinvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productclassinvoices', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('nombre',100)->nullable();
            $table->string('cuenta',100)->nullable();
            $table->enum('admon',['SI','NO'])->default('SI');

            $table->unsignedBigInteger('admongp')->defult(0);
            //$table->foreign('admongp')->references('id')->on('admongps');            

            $table->decimal('comision',10,2)->defult(0);
            $table->unsignedSmallInteger('estatus')->defult(0);
            $table->unsignedSmallInteger('control')->defult(0);

            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productclassinvoices');
    }
}

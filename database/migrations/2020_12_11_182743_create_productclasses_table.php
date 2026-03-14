<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductclassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productclasses', function (Blueprint $table)
        {
            $table->id();
            $table->timestamps();
            $table->string('nombre',100)->nullable();
            $table->string('cuenta',60)->nullable();
            $table->unsignedSmallInteger('estatus')->default(0);
    
        });
    }

   
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productclasses');
    }
}

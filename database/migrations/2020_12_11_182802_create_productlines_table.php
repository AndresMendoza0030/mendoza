<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductlinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productlines', function (Blueprint $table)
        {
            $table->id();
            $table->timestamps();
            $table->string('nombre',100)->nullable();
            $table->string('cuenta',60)->nullable();
            $table->unsignedSmallInteger('estatus')->default(0);

            $table->unsignedBigInteger('clase_id')->nullable();
            $table->foreign('clase_id')->references('id')->on('productclasses');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productlines');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagomedheadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagomedhead', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('referente_id')->defult(0);
            $table->date('fecha');
            $table->decimal('valor',10,2)->defult(0);            
            $table->unsignedSmallInteger('user_id')->nullable();
            $table->unsignedSmallInteger('estatus')->defult(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pagomedheads');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIngmedicosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingmedicos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('paciente_id')->defult(0);           
            $table->unsignedBigInteger('referente_id')->defult(0);
            $table->date('fecha_recibido');
            $table->decimal('valor',10,2)->defult(0);            
            $table->decimal('efectivo',10,2)->defult(0);
            $table->decimal('tarjeta',10,2)->defult(0);
            $table->decimal('cheque',10,2)->defult(0);
            $table->decimal('contable',10,2)->defult(0);                                  
            $table->unsignedSmallInteger('user_id_recibe')->nullable();

            $table->string('concepto',100)->nullable();
            $table->string('revisado',1)->nullable();

            $table->enum('entregado',['S','N'])->default('N');            
            $table->date('fecha_entregado');
            $table->unsignedSmallInteger('user_id_entrega')->nullable();
            $table->unsignedSmallInteger('reporte_id')->default(0);            
            $table->unsignedBigInteger('lotepago')->defult(0);

            $table->unsignedSmallInteger('estatusrecibe')->default(0);
            $table->unsignedSmallInteger('estatusentrega')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ingmedicos');
    }
}

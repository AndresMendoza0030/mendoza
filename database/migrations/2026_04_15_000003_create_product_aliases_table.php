<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductAliasesTable extends Migration
{
    public function up()
    {
        Schema::create('product_aliases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('supplier_name')->nullable();
            $table->string('alias_text');
            $table->string('normalized_alias');
            $table->timestamps();

            $table->index('product_id');
            $table->index('supplier_name');
            $table->index('normalized_alias');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_aliases');
    }
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseImportItemsTable extends Migration
{
    public function up()
    {
        Schema::create('purchase_import_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_import_id');
            $table->integer('line_no')->nullable();
            $table->string('extracted_code')->nullable();
            $table->text('extracted_description')->nullable();
            $table->string('extracted_unit')->nullable();
            $table->decimal('extracted_qty', 12, 4)->default(0);
            $table->decimal('extracted_unit_price', 12, 4)->default(0);
            $table->decimal('extracted_subtotal', 12, 4)->nullable();
            $table->unsignedBigInteger('matched_product_id')->nullable();
            $table->integer('match_score')->default(0);
            $table->boolean('needs_review')->default(true);
            $table->timestamps();

            $table->index('purchase_import_id');
            $table->index('matched_product_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_import_items');
    }
}
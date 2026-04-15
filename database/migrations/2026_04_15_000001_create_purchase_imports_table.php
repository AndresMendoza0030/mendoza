<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseImportsTable extends Migration
{
    public function up()
    {
        Schema::create('purchase_imports', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name')->nullable();
            $table->string('supplier_nit')->nullable();
            $table->string('supplier_nrc')->nullable();
            $table->string('document_number')->nullable();
            $table->string('control_number')->nullable();
            $table->string('generation_code')->nullable();
            $table->date('issue_date')->nullable();
            $table->decimal('subtotal', 12, 2)->nullable();
            $table->decimal('iva', 12, 2)->nullable();
            $table->decimal('total', 12, 2)->nullable();
            $table->string('payment_terms')->nullable();
            $table->longText('raw_text')->nullable();
            $table->string('image_path')->nullable();
            $table->enum('status', ['draft', 'reviewed', 'confirmed', 'failed'])->default('draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('confirmed_by')->nullable();
            $table->timestamps();

            $table->index('document_number');
            $table->index('generation_code');
            $table->index('issue_date');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_imports');
    }
}
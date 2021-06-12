<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductInventories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_inventories', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->bigInteger('barcode')->nullable();
            $table->integer('qty');
            $table->decimal('retail_price','10','2');
            $table->decimal('supply_price','10','2');
            $table->date('expiry')->nullable();
            $table->integer('po_id');
            $table->string('type','10')->nullable();
            $table->string('batch_no',30)->nullable();
            $table->date('expiry')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_inventories');
    }
}

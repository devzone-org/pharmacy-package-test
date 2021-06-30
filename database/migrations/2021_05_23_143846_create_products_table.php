<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('salt',100)->nullable();
            $table->string('barcode',50)->nullable();
            $table->integer('packing')->nullable();
            $table->decimal('cost_of_price')->default(0);
            $table->decimal('retail_price')->default(0);
            $table->integer('rack_id')->nullable();
            $table->integer('manufacture_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->integer('reorder_level')->nullable();
            $table->integer('reorder_qty')->nullable();
            $table->char('narcotics',1)->default('f');
            $table->char('type',1)->default('f');
            $table->char('control_medicine',1)->default('f');
            $table->char('narcotics',1)->default('f');
            $table->char('status',1)->default('f');
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
        Schema::dropIfExists('products');
    }
}

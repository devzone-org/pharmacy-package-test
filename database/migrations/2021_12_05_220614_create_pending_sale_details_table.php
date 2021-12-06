<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePendingSaleDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_sale_details', function (Blueprint $table) {
            $table->id();
            $table->integer('sale_id');
            $table->integer('product_id');

            $table->integer('qty');
            $table->decimal('supply_price');
            $table->decimal('retail_price');
            $table->decimal('total');
            $table->decimal('disc');
            $table->decimal('total_after_disc');
            $table->decimal('retail_price_after_disc');
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
        Schema::dropIfExists('pending_sale_details');
    }
}

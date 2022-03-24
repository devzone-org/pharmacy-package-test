<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpenReturnDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('open_return_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('open_return_id');
            $table->unsignedBigInteger('product_id');
            $table->date('expiry');
            $table->unsignedInteger('qty');
            $table->unsignedDecimal('retail_price');
            $table->unsignedDecimal('total');
            $table->unsignedInteger('deduction');
            $table->unsignedDecimal('total_after_deduction');
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
        Schema::dropIfExists('open_return_details');
    }
}

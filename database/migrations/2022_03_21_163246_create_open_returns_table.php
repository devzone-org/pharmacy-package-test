<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpenReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('open_returns', function (Blueprint $table) {
            $table->id();
            $table->text('remarks');
            $table->unsignedDecimal('total');
            $table->unsignedDecimal('total_after_deduction');
            $table->unsignedInteger('added_by');
            $table->unsignedInteger('voucher');
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
        Schema::dropIfExists('open_returns');
    }
}

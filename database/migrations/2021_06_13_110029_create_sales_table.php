<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->integer('patient_id')->nullable();
            $table->integer('referred_by')->nullable();
            $table->integer('sale_by');
            $table->dateTime('sale_at');
            $table->text('remarks')->nullable();
            $table->decimal('receive_amount');
            $table->decimal('payable_amount');
            $table->decimal('sub_total');
            $table->decimal('gross_total');
            $table->char('is_refund',1)->default('f');
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
        Schema::dropIfExists('sales');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->mediumText('description')->nullable();
            $table->integer('receiving_in');
            $table->date('receiving_date')->nullable();
            $table->integer('added_by');
            $table->integer('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->string('receipt_no')->nullable();
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
        Schema::dropIfExists('customer_payments');
    }
}

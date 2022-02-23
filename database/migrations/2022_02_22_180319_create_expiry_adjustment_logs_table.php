<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpiryAdjustmentLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expiry_adjustment_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_inventory_id');
            $table->date('old_expiry');
            $table->date('new_expiry');
            $table->unsignedBigInteger('created_by');
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('expiry_adjustment_logs');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePendingSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_sales', function (Blueprint $table) {
            $table->id();
            $table->string('type',20)->nullable();
            $table->integer('patient_id')->nullable();
            $table->integer('referred_by')->nullable();
            $table->integer('sale_by');
            $table->dateTime('sale_at');
            $table->integer('complete_by')->nullable();
            $table->decimal('sub_total');
            $table->decimal('gross_total');
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
        Schema::dropIfExists('pending_sales');
    }
}

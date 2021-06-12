<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierRefunds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_refunds', function (Blueprint $table) {
            $table->id();
            $table->integer('supplier_id');
            $table->mediumText('description')->nullable();
            $table->decimal('total_amount')->nullable();
            $table->integer('created_by');
            $table->integer('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->char('is_receive', '1')->default('f');
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
        Schema::dropIfExists('supplier_refunds');
    }
}

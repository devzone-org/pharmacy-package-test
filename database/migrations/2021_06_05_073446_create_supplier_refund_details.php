<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierRefundDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_refund_details', function (Blueprint $table) {
            $table->id();
            $table->integer('supplier_refund_id');
            $table->integer('product_id');
            $table->integer('product_inventory_id');
            $table->integer('po_id');
            $table->integer('qty');
            $table->decimal('supply_price')->nullable();
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
        Schema::dropIfExists('supplier_refund_details');
    }
}

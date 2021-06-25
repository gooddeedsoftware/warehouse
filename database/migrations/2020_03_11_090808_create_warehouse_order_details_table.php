<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('warehouse_order_details')) {
            Schema::create('warehouse_order_details', function (Blueprint $table) {
                $table->string('whs_order_id', 32)->nullable();
                $table->string('whs_product_id', 32)->nullable();
                $table->string('product_id', 32)->nullale();
                $table->string('source_whs_id', 32)->nullable();
                $table->string('destination_whs_id', 32)->nullable();
                $table->decimal("ordered_qty", 15, 2)->nullable();
                $table->decimal("picked_qty", 15, 2)->nullable();
                $table->decimal("received_qty", 15, 2)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('warehouse_order_details');
    }
}

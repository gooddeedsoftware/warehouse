<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Creating the Product Table
        if (!Schema::hasTable('product')) {
            Schema::create('product', function (Blueprint $table) {
                $table->string('id', 32)->unique()->index();
                $table->string('product_number')->nullable();
                $table->string('warehouse', 32)->nullable();
                $table->integer("store_product_id")->nullable();
                $table->string("modell", 100)->nullable();
                $table->string("supplier_id", 32)->nullable();
                $table->string("acc_plan_id", 32)->nullable();
                $table->string("unit", 50)->nullable();
                $table->decimal("order_qty", 15, 2)->nullable();
                $table->string("ean", 20)->nullable();
                $table->string("nobb")->nullable();
                $table->string("nrf", 20)->nullable();
                $table->decimal("sale_price", 15, 4)->nullable();
                $table->decimal("list_price", 15, 4)->nullable();
                $table->string("curr_iso_name", 5)->nullable();
                $table->decimal("vendor_price", 15, 4)->nullable();
                $table->decimal("tax", 5, 2)->nullable();
                $table->decimal("discount", 5, 2)->nullable();
                $table->string('description', 80)->nullable();
                $table->tinyInteger('approved_product')->default(0);
                $table->tinyInteger('is_package')->default(0);
                $table->decimal('costs', 15, 2)->nullable();
                $table->decimal('cost_factor', 15, 4)->nullable();
                $table->decimal('profit_percentage', 15, 4)->nullable();
                $table->decimal('profit', 15, 4)->nullable();
                $table->decimal('sale_price_with_vat', 15, 4)->nullable();
                $table->decimal('dg', 15, 4)->nullable();
                $table->decimal('vendor_price_nok', 15, 4)->nullable();
                $table->tinyInteger('stockable')->nullable();
                $table->decimal('cost_price', 15, 4)->nullable();
                $table->integer('uni_id')->nullable();
                $table->integer('product_group')->nullable();
                $table->timestamps();
                $table->softDeletes();
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
        Schema::drop('product');
    }
}

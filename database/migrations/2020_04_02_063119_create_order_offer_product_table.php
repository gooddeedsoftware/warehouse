<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderOfferProductTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            if (!Schema::hasTable('order_offer_product')) {
                Schema::create('order_offer_product', function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('order_id', 32)->nullable();
                    $table->string('order_material_id', 32)->nullable();
                    $table->string('product_id', 32)->index();
                    $table->string('product_text', 255)->nullable();
                    $table->decimal("qty", 15, 2)->nullable();
                    $table->string("unit", 255)->nullable();
                    $table->decimal("price", 15, 4)->nullable();
                    $table->decimal("discount", 5, 2)->nullable();
                    $table->decimal("sum_ex_vat", 15, 2)->nullable();
                    $table->decimal("vat", 5, 2)->nullable();
                    $table->string('created_by', 32)->nullable();
                    $table->string('updated_by', 32)->nullable();
                    $table->tinyInteger("is_approved")->default(0)->nullable();
                    $table->date('delivery_date')->nullable();
                    $table->tinyInteger('is_text')->nullable();
                    $table->text('text')->nullable();
                    $table->timestamps();
                    $table->softDeletes();
                });
            }
        } catch (Exception $e) {

        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('order_offer_product');
    }
}

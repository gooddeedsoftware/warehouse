<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhsTransferOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            if (!Schema::hasTable("whs_transfer_order")) {
                Schema::create("whs_transfer_order", function (Blueprint $table) {
                    $table->string("id", 32)->unique()->index();
                    $table->string("order_number", 10)->nullable();
                    $table->tinyInteger("order_type")->nullable();
                    $table->string("supplier_id", 32)->nullable();
                    $table->string("source_warehouse", 32)->nullable();
                    $table->string("destination_warehouse", 32)->nullable();
                    $table->string("warehouse", 32)->nullable();
                    $table->tinyInteger("priority")->nullable();
                    $table->date("order_date")->nullable();
                    $table->text("order_comment")->nullable();
                    $table->tinyInteger("order_status")->nullable();
                    $table->tinyInteger('is_notified')->default(0);
                    $table->text("product_details")->nullable();
                    $table->string('customer_order_id', 32)->nullable();
                    $table->string('customer_order_number', 40)->nullable();
                    $table->string('added_by', 32)->nullable();
                    $table->string('updated_by', 32)->nullable();
                    $table->string('company')->nullable();
                    $table->string('post_address')->nullable();
                    $table->string('zip')->nullable();
                    $table->string('city')->nullable();
                    $table->string('country')->nullable();
                    $table->string('delivery_method')->nullable();
                    $table->string('supplier_ref')->nullable();
                    $table->string('our_reference', 40)->nullable();
                    $table->softDeletes();
                    $table->timestamps();
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
        Schema::drop('whs_transfer_order');
    }
}

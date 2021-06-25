<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderMaterialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            if (!Schema::hasTable('order_material')) {
                Schema::create('order_material', function (Blueprint $table) {
                    $table->string('id', 32)->unique()->index();
                    $table->string('product_number', 50)->nullable();
                    $table->string('warehouse', 32)->nullable();
                    $table->string("location", "32")->nullable();
                    $table->decimal("order_quantity", 15, 2)->nullable();
                    $table->decimal("quantity", 15, 2)->nullable();
                    $table->decimal("invoice_quantity", 15, 2)->nullable();
                    $table->decimal("package_quantity", 15, 2)->nullable();
                    $table->integer('sort_number')->nullable();
                    $table->date('date')->nullable();
                    $table->string('description', 80)->nullable();
                    $table->string('userid', 32)->nullable();
                    $table->string('order_id', 32)->nullable();
                    $table->tinyInteger('is_package')->default(0);
                    $table->string('reference_id', 32)->nullable();
                    $table->tinyInteger('approved_product')->default(0);
                    $table->tinyInteger("invoiced")->default(0);
                    $table->tinyInteger('mail_notification_status')->nullable()->default(0);
                    $table->decimal("discount", 5, 2)->nullable();
                    $table->decimal("offer_sale_price", 15, 4)->nullable();
                    $table->tinyInteger("is_offerorder")->default(0)->nullable();
                    $table->decimal("return_quantity", 15, 2)->default(0);
                    $table->integer("order_offer_product_id")->nullable();
                    $table->date('delivery_date')->nullable();
                    $table->bigInteger('shippment_id')->nullable();
                    $table->text('product_text')->nullable();
                    $table->tinyInteger('is_text')->default(0);
                    $table->string('text_ref_id')->nullable();
                    $table->integer('uni_status')->nullable();
                    $table->integer('sent_id')->default(0);
                    $table->tinyInteger('is_logistra')->nullable();
                    $table->string('track_number')->nullable();
                    $table->decimal("vat", 15, 4)->nullable();
                    $table->decimal("sum_ex_vat", 15, 4)->nullable();
                    $table->integer("unit")->nullable();
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
        Schema::drop('order_material');
    }
}

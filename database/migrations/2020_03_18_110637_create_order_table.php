<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order', function (Blueprint $table) {
            $table->string('id', 32)->index()->nullable();
            $table->string('customer_id', 32)->index()->nullable();
            $table->string('ordered_by', 32)->index()->nullable();
            $table->string('order_number', 40)->nullable();
            $table->date('order_date')->nullable();
            $table->string('project_number', 40)->nullable();
            $table->text('visiting_address')->nullable();
            $table->tinyInteger('controll_type')->nullable();
            $table->longText('comments')->nullable();
            $table->string('invoice_customer', 32)->nullable();
            $table->text('order_invoice_comments')->nullable();
            $table->text('email_invoice', 255)->nullable();
            $table->integer('status')->default(1);
            $table->tinyInteger('all_approved')->default(0);
            $table->longText('customer_sign')->nullable();
            $table->string('deliveraddress', 32)->nullable();
            $table->integer('pmt_term')->nullable();
            $table->string('contact', 32)->nullable();
            $table->string('phone', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->date('date_completed')->nullable();
            $table->text('invoice_type', 255)->nullable();
            $table->text('priority', 255)->nullable();
            $table->longText('assigned')->nullable();
            $table->longText('error_description')->nullable();
            $table->text('order_category', 255)->nullable();
            $table->string('equipment_id', 32)->nullable();
            $table->tinyInteger('order_invoice_status')->default(1);
            $table->tinyInteger('is_delete')->default(0);
            $table->decimal("sum", 20, 4)->nullable();
            $table->decimal("mva", 20, 4)->nullable();
            $table->decimal("round_down", 20, 4)->nullable();
            $table->decimal("total", 20, 4)->nullable();
            $table->string('deliveraddress1')->nullable();
            $table->string('deliveraddress2')->nullable();
            $table->string('deliveraddress_zip')->nullable();
            $table->string('deliveraddress_city')->nullable();
            $table->tinyInteger('is_category_enable')->default(0);
            $table->string('order_user', 32)->nullable();
            $table->string('added_by', 32)->nullable();
            $table->string('updated_by', 32)->nullable();
            $table->integer('offer_id')->nullable();
            $table->string('visitingAddress')->nullable();
            $table->string('visitingAddress1')->nullable();
            $table->string('visitingAddress2')->nullable();
            $table->string('visitingAddressZip')->nullable();
            $table->string('visitingAddressCity')->nullable();
            $table->date('offer_due_date')->nullable();
            $table->integer('uni_status')->nullable();
            $table->tinyInteger('is_res_order')->nullable();
            $table->string('res_order_id')->nullable();
            $table->tinyInteger("is_offer")->defalut(0);
            $table->string("offer_order_id", 32)->nullable();
            $table->string("offer_number", 40)->nullable();
            $table->string("offer_order_number", 40)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order');
    }
}

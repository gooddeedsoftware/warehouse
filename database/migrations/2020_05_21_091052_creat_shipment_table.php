<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatShipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('shipment')) {
            Schema::create('shipment', function (Blueprint $table) {
                $table->increments('id')->index();
                $table->string('order_id')->nullable();
                $table->string('product_name')->nullable();
                $table->string('product_identifier')->nullable();
                $table->string('carrier_name')->nullable();
                $table->string('carrier_identifier')->nullable();
                $table->decimal("customerprice", 8, 2)->nullable();
                $table->decimal("grossprice", 8, 2)->nullable();
                $table->decimal("netprice", 8, 2)->nullable();
                $table->decimal("estimatedcost", 8, 2)->nullable();
                $table->integer('sender_id')->nullable();
                $table->integer('shipment_status')->nullable();
                $table->bigInteger('consignment_id')->nullable();
                $table->string('track_number')->nullable();
                $table->softDeletes();
                $table->timestamps();
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
        Schema::drop('shipment');
    }
}

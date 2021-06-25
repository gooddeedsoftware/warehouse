<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatShipmentMeasurementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('shipping_measurement')) {
            Schema::create('shipping_measurement', function (Blueprint $table) {
                $table->increments('id')->index();
                $table->string('order_id')->nullable();
                $table->integer("height")->nullable();
                $table->integer("length")->nullable();
                $table->integer("width")->nullable();
                $table->decimal("volume", 8, 2)->nullable();
                $table->decimal("weight", 8, 2)->nullable();
                $table->integer('shipping_id')->nullable();
                $table->bigInteger('shipment_status')->nullable();
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
        Schema::drop('shipping_measurement');
    }
}

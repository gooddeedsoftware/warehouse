<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhsHistoryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('whs_history_details')) {
            Schema::create('whs_history_details', function (Blueprint $table) {
                $table->increments('id')->index();
                $table->integer('whs_history_id')->nullable();
                $table->integer('order_type')->nullable();
                $table->string('order_id')->nullable();
                $table->string('user')->nullable();
                $table->string('from_warehouse')->nullable();
                $table->string('customer')->nullable();
                $table->string('from_location')->nullable();
                $table->string('destination_warehouse')->nullable();
                $table->string('destination_location')->nullable();
                $table->decimal("received_qty", 15, 2)->nullable();
                $table->date("action_date")->nullable();
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
        Schema::drop('whs_history_details');
    }
}

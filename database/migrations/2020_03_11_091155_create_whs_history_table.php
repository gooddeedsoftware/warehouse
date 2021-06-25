<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhsHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('whs_history')) {
            Schema::create('whs_history', function (Blueprint $table) {
                $table->increments('id')->index();
                $table->string('product_id')->nullable();
                $table->string('whs_inventory_id')->nullable();
                $table->tinyInteger('is_deleted')->default(0);
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
        Schema::drop('whs_history');
    }
}

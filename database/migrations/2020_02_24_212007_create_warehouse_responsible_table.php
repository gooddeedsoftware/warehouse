<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseResponsibleTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('warehouse_responsible')) {
            Schema::create('warehouse_responsible', function (Blueprint $table) {
                $table->string('warehouse_id', 32)->nullable();
                $table->string('user_id', 32)->nullable();
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
        Schema::drop('warehouse_responsible');
    }
}

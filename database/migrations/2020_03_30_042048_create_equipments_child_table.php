<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipmentsChildTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('equipments_child')) {
            Schema::create('equipments_child', function (Blueprint $table) {
                $table->string('equipment_id', 32)->nullable();
                $table->string('child_equipment_id', 32)->nullable();
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
        Schema::drop('equipments_child');
    }
}

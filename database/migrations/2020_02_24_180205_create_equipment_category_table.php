<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipmentCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            if (!Schema::hasTable('equipment_category')) {
                Schema::create("equipment_category", function (Blueprint $table) {
                    $table->string("id", 32)->unique()->index();
                    $table->string("type", 100)->nullable();
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
        Schema::drop('equipment_category');
    }
}

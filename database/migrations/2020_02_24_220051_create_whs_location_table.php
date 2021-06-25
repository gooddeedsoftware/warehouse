<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhsLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            if (!Schema::hasTable("whs_location")) {
                Schema::create("whs_location", function (Blueprint $table) {
                    $table->string("id", 32)->unique()->index();
                    $table->string("warehouse_id", 32)->nullable();
                    $table->foreign("warehouse_id")->nullable();
                    $table->string("name", 32)->nullable();
                    $table->tinyInteger("scrap_location")->nullable();
                    $table->tinyInteger("return_location")->nullable();
                    $table->string('added_by', 32)->nullable();
                    $table->string('updated_by', 32)->nullable();
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
        Schema::drop('whs_location');
    }
}

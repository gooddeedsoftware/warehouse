<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            if (!Schema::hasTable("warehouse")) {
                Schema::create("warehouse", function (Blueprint $table) {
                    $table->string("id", 32)->unique()->index();
                    $table->string("shortname", 32)->nullable();
                    $table->integer("main")->nullable();
                    $table->string("notification_email", 255)->nullable();
                    $table->string("description", 255)->nullable();
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
        Schema::drop('warehouse');
    }
}

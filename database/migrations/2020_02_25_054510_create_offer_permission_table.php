<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfferPermissionTable extends Migration
{
    public function up()
    {
        try {
            if (!Schema::hasTable('offer_permission')) {
                Schema::create('offer_permission', function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('group')->nullable();
                    $table->foreign('group')->nullable();
                    $table->tinyInteger('create')->default(0);
                    $table->tinyInteger('edit')->default(0);
                    $table->tinyInteger('view')->default(0);
                    $table->tinyInteger('delete')->default(0);
                    $table->integer('create_order')->default(0);
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
        if (Schema::hasTable('offer_permission')) {
            Schema::dropIfExists('offer_permission');
        }
    }
}

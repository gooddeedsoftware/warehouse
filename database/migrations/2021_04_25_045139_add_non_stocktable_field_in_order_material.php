<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNonStocktableFieldInOrderMaterial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('order_material')) {
            Schema::table('order_material', function (Blueprint $table) {
                $table->tinyInteger("stockable")->nullable();
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
        Schema::table('order_material', function (Blueprint $table) {
            $table->dropColumn('stockable');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsToMaterialTable extends Migration
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
                $table->string("product_description")->nullable();
                $table->decimal("cost_price", 15, 4)->nullable();
                $table->decimal("dg", 8, 4)->nullable();
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
            $table->dropColumn('product_description');
            $table->dropColumn('cost_price');
            $table->dropColumn('dg');
        });
    }
}

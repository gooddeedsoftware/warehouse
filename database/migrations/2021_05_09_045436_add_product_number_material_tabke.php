<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductNumberMaterialTabke extends Migration
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
                $table->string("prod_nbr")->nullable();
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
            $table->dropColumn('prod_nbr');
        });
    }
}

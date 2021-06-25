<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAccountsAddUniId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('acc_plan')) {
            Schema::table('acc_plan', function (Blueprint $table) {
                $table->integer("uni_id")->after('DefAccount')->nullable();
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
        Schema::table('acc_plan', function (Blueprint $table) {
            $table->dropColumn('DefAccount');
        });
    }
}

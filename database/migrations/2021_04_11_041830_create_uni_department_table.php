<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUniDepartmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uni_department', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('uni_id')->nullable();
            $table->string('name')->nullable();
        });

        if (Schema::hasTable('department')) {
            Schema::table('department', function (Blueprint $table) {
                $table->integer("uni_department")->after('status')->nullable();
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
        Schema::dropIfExists('uni_department');

        Schema::table('department', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}

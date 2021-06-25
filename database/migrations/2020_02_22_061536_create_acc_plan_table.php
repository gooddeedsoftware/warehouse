<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('acc_plan')) {
            Schema::create('acc_plan', function (Blueprint $table) {
                $table->string('id', 32)->unique()->index();
                $table->string('AccountNo', 30)->unique();
                $table->string('Name', 80)->nullable();
                $table->string('AccountGroup', 80)->nullable();
                $table->tinyInteger('ResAccount')->default(1);
                $table->string('TaxCode', 30)->nullable();
                $table->tinyInteger('DefAccount')->default(0);
                $table->string('added_by', 32)->nullable();
                $table->string('updated_by', 32)->nullable();
                $table->softDeletes();
                $table->timestamps();
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
        Schema::drop('acc_plan');
    }
}

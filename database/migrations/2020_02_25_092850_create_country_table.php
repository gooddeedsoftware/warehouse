<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (!Schema::hasTable('country')) {
            Schema::create('country', function (Blueprint $table) {
                $table->string('id', 32)->unique()->index();
                $table->string('name', 50)->nullable();
                $table->string('code', 50)->nullable();
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
        Schema::drop('country');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            if (!Schema::hasTable('group')) {
                Schema::create('group', function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('group', 255)->nullable();
                    $table->string('module')->nullable();
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
        if (Schema::hasTable('group')) {
            Schema::dropIfExists('group');
        }
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionGroupUsersTable extends Migration
{
    public function up()
    {
        try {
            if (!Schema::hasTable('permission_group_users')) {
                Schema::create('permission_group_users', function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('module')->nullable();
                    $table->integer('group_id')->nullable();
                    $table->string('user_id', 32)->nullable();
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
        if (Schema::hasTable('permission_group_users')) {
            Schema::dropIfExists('permission_group_users');
        }
    }
}

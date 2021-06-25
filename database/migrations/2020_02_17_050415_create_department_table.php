<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('department')) {
            Schema::create('department', function (Blueprint $table) {
                $table->string('id', 32)->unique()->index();
                $table->string('Nbr', 5);
                $table->string('Name', 45);
                $table->smallInteger('status');
                $table->string('added_by', 32)->nullable();
                $table->string('updated_by', 32)->nullable();
                $table->SoftDeletes();
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
        Schema::dropIfExists('department');
    }
}

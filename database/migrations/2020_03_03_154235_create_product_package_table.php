<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPackageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            Schema::create('product_package', function (Blueprint $table) {
                $table->increments('id');
                $table->string('package_id', 32)->nullable();
                $table->string('content', 32)->nullable();
                $table->integer('qty')->nullable();
                $table->integer('sort_number')->nullable();
                $table->timestamps();
            });
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
        if (Schema::hasTable('product_package')) {
            Schema::dropIfExists('product_package');
        }
    }
}

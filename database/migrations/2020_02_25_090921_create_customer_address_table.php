<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            if (!Schema::hasTable('customer_address')) {
                Schema::create('customer_address', function (Blueprint $table) {
                    $table->string('id', 32)->primary();
                    $table->string('customer_id', 32)->nullable();
                    $table->tinyInteger('type')->nullable();
                    $table->tinyInteger('main')->default(0);
                    $table->text('address1')->nullable();
                    $table->text('address2')->nullable();
                    $table->string('zip', 10)->nullable();
                    $table->string('city', 100)->nullable();
                    $table->string('country', 32)->nullable();
                    $table->string('added_by', 32)->nullable();
                    $table->string('updated_by', 32)->nullable();
                    $table->timestamps();
                    $table->softDeletes();
                });
            }
        } catch (Exception $e) {}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('customer');
    }
}

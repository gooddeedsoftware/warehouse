<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUniCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uni_customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('uni_id')->nullable();
            $table->string('name')->nullable();
            $table->string('org_number')->nullable();
            $table->string('customer_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uni_customers');
    }
}

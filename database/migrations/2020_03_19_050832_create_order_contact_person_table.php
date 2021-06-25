<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderContactPersonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        //Creating the Order Contact person table
        if(!Schema::hasTable('order_contact_person')){
            Schema::create('order_contact_person',function(Blueprint $table){
                $table->string('order_id', 32)->index()->nullable();
                $table->string('contact_person_id', 32)->index()->nullable();
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
        Schema::drop('order_contact_person');
    }
}
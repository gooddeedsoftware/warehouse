<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('equipment')) {
            Schema::create('equipment', function (Blueprint $table) {
                $table->string('id', 32)->index();
                $table->string('customer_id', 32)->index()->nullable();
                $table->string('sn', 255)->nullable();
                $table->string('internalnbr', 255)->nullable();
                $table->string('reginnid', 255)->nullable();
                $table->string('description', 255)->nullable();
                $table->string('equipment_category', 32)->nullable();
                $table->date('install_date')->nullable();
                $table->date('last_repair_date')->nullable();
                $table->string('added_by', 32)->nullable();
                $table->string('updated_by', 32)->nullable();
                $table->string('order_id', 32)->nullable();
                $table->string('order_number', 40)->nullable();
                $table->text("note")->nullable();
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
        Schema::drop('equipment');
    }
}

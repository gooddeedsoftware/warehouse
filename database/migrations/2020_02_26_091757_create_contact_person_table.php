<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactPersonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('contact_person')) {
            Schema::create('contact_person', function (Blueprint $table) {
                $table->string('id', 32)->unique()->index();
                $table->string('customer_id', 32)->nullable();
                $table->string('name', 255);
                $table->string('email', 255)->nullable();
                $table->string('mobile', 15)->nullable();
                $table->string('phone', 15)->nullable();
                $table->string('added_by', 32)->nullable();
                $table->string('updated_by', 32)->nullable();
                $table->string('title', 50)->nullable();
                $table->timestamps();
                $table->softDeletes();
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
        Schema::drop('contact_person');
    }
}

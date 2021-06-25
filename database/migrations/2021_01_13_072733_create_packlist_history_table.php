<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePacklistHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packlist_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_id', 32)->nullable();
            $table->string('user_id', 32)->nullable();
            $table->json('pdf_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packlist_history');
    }
}

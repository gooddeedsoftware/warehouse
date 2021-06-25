<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogistraDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logistra_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->index()->nullable();
            $table->string('cargonizer_key')->index()->nullable();
            $table->string('cargonizer_sender')->index()->nullable();
            $table->tinyInteger('status')->default(0);
            $table->softDeletes();
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
        Schema::dropIfExists('logistra_details');
    }
}

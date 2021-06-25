<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrossMarginTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gross_margin', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('supplier')->nullable();
            $table->integer('product_group')->nullable();
            $table->decimal("gross_margin", 8, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gross_margin');
    }
}

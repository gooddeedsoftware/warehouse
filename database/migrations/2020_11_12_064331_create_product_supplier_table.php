<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSupplierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_supplier', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('supplier')->nullable();
            $table->string('product_id')->nullable();
            $table->string('articlenumber')->nullable();
            $table->string('articlename')->nullable();
            $table->string('curr_iso_name')->nullable();
            $table->decimal("supplier_price", 15, 4)->nullable();
            $table->decimal("supplier_discount", 15, 4)->nullable();
            $table->decimal("discount", 15, 4)->nullable();
            $table->decimal("other", 15, 4)->nullable();
            $table->decimal("addon", 15, 4)->nullable();
            $table->decimal("realcost", 15, 4)->nullable();
            $table->decimal("realcost_nok", 15, 4)->nullable();
            $table->tinyInteger("is_main")->nullable();
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
        Schema::dropIfExists('product_supplier');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCcsheetScannedProductsTable extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('ccsheet_scanned_products')){
            Schema::create('ccsheet_scanned_products', function(Blueprint $table) {
                $table->increments('id')->index();
                $table->string('ccsheet_id')->nullable();
                $table->string('product')->nullable();
                $table->string('warehouse')->nullable();
                $table->string('location')->nullable();
                $table->decimal("qty", 15, 2)->nullable();
                $table->tinyInteger('counted')->default(0);
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
        Schema::drop('ccsheet_scanned_products');
    }
}

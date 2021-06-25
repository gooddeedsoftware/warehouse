<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCcsheetDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('ccsheet_details')) {
            Schema::create('ccsheet_details', function (Blueprint $table) {
                $table->bigincrements('id');
                $table->unsignedBigInteger('ccsheet_id')->nullable();
                $table->string('inv_id', 32)->nullable();
                $table->string('location_id', 32)->nullable();
                $table->string('product_id', 32)->nullable();
                $table->string('product_number', 50)->nullable();
                $table->string('nobb', 20)->nullable();
                $table->string('description', 80)->nullable();
                $table->string('unit', 50)->nullable();
                $table->string('curr_iso', 5)->nullable();
                $table->decimal('vendor_price', 15, 4)->nullable();
                $table->decimal('on_stock_qty', 15, 2)->nullable();
                $table->decimal('counted_qty', 15, 2)->nullable();
                $table->tinyInteger('mismatched')->default(0);
                $table->timestamp('counted_at')->nullable();
                $table->string('counted_by', 32)->nullable();
                $table->timestamp('recounted_at')->nullable();
                $table->string('recounted_by', 32)->nullable();
                $table->string('added_by', 32)->nullable();
                $table->text('comments')->nullable();
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
        Schema::drop('ccsheet_details');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCcsheetCurrencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            if (!Schema::hasTable('ccsheet_currency')) {
                Schema::create('ccsheet_currency', function (Blueprint $table) {
                    $table->increments('id');
                    $table->unsignedBigInteger('ccsheet_id')->nullable();
                    $table->string('curr_iso', 32)->nullable();
                    $table->decimal('exch_rate', 15, 2);
                    $table->timestamps();
                    $table->softDeletes();
                });
            }
        } catch (Exception $e) {}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ccsheet_currency');
    }
}

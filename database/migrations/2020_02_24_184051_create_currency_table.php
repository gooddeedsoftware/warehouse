<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            if (!Schema::hasTable('currency')) {
                Schema::create('currency', function (Blueprint $table) {
                    $table->string('id', 32)->index();
                    $table->string('curr_iso_name', 5)->nullable();
                    $table->decimal('exch_rate', 15, 2)->nullable();
                    $table->date('valid_from')->nullable();
                    $table->string('added_by', 32)->nullable();
                    $table->string('updated_by', 32)->nullable();
                    $table->softDeletes();
                    $table->timestamps();
                });
            }
        } catch (Exception $e) {
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('currency');
    }
}

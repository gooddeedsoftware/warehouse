<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfferOrderMailHistoryTable extends Migration
{
/**
 * Run the migrations.
 *
 * @return void
 */
    public function up()
    {
        try {
            if (!Schema::hasTable('offer_order_mail_history')) {
                Schema::create('offer_order_mail_history', function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('order_id', 32)->nullable();
                    $table->string('email', 255)->nullable();
                    $table->string('user_id', 32)->nullable();
                    $table->string('user_name', 255)->nullable();
                    $table->date('send_date', 255)->nullable();
                    $table->tinyInteger('order_status')->nullable();
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
        if (Schema::hasTable('offer_order_mail_history')) {
            Schema::dropIfExists('offer_order_mail_history');
        }
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfferSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            if (!Schema::hasTable('offer_settings')) {
                Schema::create('offer_settings', function (Blueprint $table) {
                    $table->increments('id');
                    $table->tinyInteger('type')->nullable(); // 1 => Standard Text, 2 => order, 3 => supplier order
                    $table->text('data')->nullable();
                    $table->text('comments')->nullable();
                    $table->string('added_by', 32)->nullable();
                    $table->string('updated_by', 32)->nullable();
                    $table->timestamps();
                    $table->softDeletes();
                });
                $this->seedOfferSettings();
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
        try {
            if (Schema::hasTable('offer_settings')) {
                Schema::dropIfExists('offer_settings');
            }
        } catch (Exception $e) {

        }
    }

    /**
     * seed Offer Settings
     * @return void
     */
    public function seedOfferSettings()
    {
        try {
            DB::table('offer_settings')->insert([
                [
                    'type'     => 1,
                    'data'     => '',
                    'comments' => 'Offer Standard Text',
                ],
            ]);
        } catch (Exception $e) {

        }
    }
}

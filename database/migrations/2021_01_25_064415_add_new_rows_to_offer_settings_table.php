<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewRowsToOfferSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offer_settings', function (Blueprint $table) {
            DB::table('offer_settings')->insert([
                [
                    'type'     => 2,
                    'data'     => '',
                    'comments' => 'Order Standard Text',
                ],
                [
                    'type'     => 3,
                    'data'     => '',
                    'comments' => 'Supplier Order Text',
                ],
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}

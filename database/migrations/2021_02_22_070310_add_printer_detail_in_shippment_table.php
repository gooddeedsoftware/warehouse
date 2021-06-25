<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrinterDetailInShippmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('shipment')) {
            Schema::table('shipment', function (Blueprint $table) {
                $table->integer("printer")->after('sender_id')->nullable();
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
        Schema::table('shipment', function (Blueprint $table) {
            $table->dropColumn('integer');
        });
    }
}

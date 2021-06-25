<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhsInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            if (!Schema::hasTable("whs_inventory")) {
                Schema::create("whs_inventory", function (Blueprint $table) {
                    $table->string("id", 32)->unique()->index();
                    $table->string("warehouse_id", 32)->nullable();
                    $table->string("location_id", 32)->nullable();
                    $table->string("product_id", 32)->nullable();
                    $table->decimal("qty", 15, 2)->nullable();
                    $table->decimal("ordered", 15, 2)->nullable();
                    $table->decimal('delivered', 16, 4)->default(0);
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
        Schema::drop('whs_inventory');
    }
}

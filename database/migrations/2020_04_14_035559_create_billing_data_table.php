<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('billing_data')) {
            Schema::create('billing_data', function (Blueprint $table) {
                $table->increments('id')->index();
                $table->string('order_id')->nullable();
                $table->string('ordermaterial_id')->nullable();
                $table->string('product_number')->nullable();
                $table->string('description')->nullable();
                $table->decimal('invoice_quantity', 15, 2)->nullable();
                $table->string('unit')->nullable();
                $table->decimal('sale_price', 15, 4)->default(0);
                $table->decimal("discount", 8, 2)->nullable();
                $table->decimal('vat', 8, 2)->default(0);
                $table->string('updated_by')->nullable(0);
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
        Schema::drop('billing_data');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('customer')) {
            Schema::create('customer', function (Blueprint $table) {
                $table->string('id', 32)->unique()->index();
                $table->integer('act_no')->nullable();
                $table->integer('customer')->nullable();
                $table->integer('supplier')->nullable();
                $table->tinyInteger('is_supplier')->default(0);
                $table->string('name', 255)->nullable();
                $table->string('shortname', 10)->nullable();
                $table->string('VAT', 24)->nullable();
                $table->text('address1')->nullable();
                $table->text('address2')->nullable();
                $table->string('city', 45)->nullable();
                $table->string('zip', 15)->nullable();
                $table->string('phone', 15)->nullable();
                $table->string('fax', 15)->nullable();
                $table->tinyInteger('invoicing')->nullable();
                $table->string('email', 255)->nullable();
                $table->string('country_id', 32)->nullable();
                $table->integer('cdeliverymtd')->nullable();
                $table->integer('sdeliverymtd')->nullable();
                $table->integer('cdeltrm')->nullable();
                $table->integer('sdeltrm')->nullable();
                $table->string('bankaccount', 35)->nullable();
                $table->string('currency', 10)->nullable();
                $table->decimal('percentage', 28, 6)->nullable();
                $table->decimal('percentage_other', 28, 6)->nullable();
                $table->integer('cpaymentcond')->nullable();
                $table->integer('spaymentcond')->nullable();
                $table->integer('cpaymentmtd')->nullable();
                $table->integer('spaymentmtd')->nullable();
                $table->text('web')->nullable();
                $table->decimal('creditlimit', 28, 6)->nullable();
                $table->integer('cmainbook')->nullable();
                $table->integer('smainbook')->nullable();
                $table->tinyInteger('status')->default(0);
                $table->tinyInteger('financing_company')->default(0);
                $table->integer('pmt_terms')->nullable();
                $table->string('added_by', 32)->nullable();
                $table->string('updated_by', 32)->nullable();
                $table->text("customer_note")->nullable();
                $table->string('uni_id')->nullable();
                $table->tinyInteger('language')->default(2);
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
        Schema::drop('customer');
    }
}

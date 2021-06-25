<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('activities')) {
            Schema::create('activities', function (Blueprint $table) {
                $table->string('id', 32)->unique()->index();
                $table->string('department_id', 32)->nullable();
                $table->string('ltkode', 10)->nullable();
                $table->string('unit', 10)->nullable();
                $table->TinyInteger('billable')->default(1);
                $table->tinyInteger("category")->nullable();
                $table->tinyInteger('travel_expense')->default(0);
                $table->tinyInteger('show_to_all')->default(0);
                $table->integer('wgsrt_wagetype')->nullable();
                $table->string('description', 255)->nullable();
                $table->string('invoice_text', 255)->nullable();
                $table->double('price', 15, 8)->nullable();
                $table->double('VAT', 15, 8)->nullable();
                $table->text('comments')->nullable();
                $table->string('fk_AccountNo', 32)->nullable();
                $table->string('added_by', 32)->nullable();
                $table->string('updated_by', 32)->nullable();
                $table->SoftDeletes();
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
        Schema::drop('activities');
    }
}

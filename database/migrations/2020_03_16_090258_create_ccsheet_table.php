<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCcsheetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            if (!Schema::hasTable('ccsheet')) {
                Schema::create('ccsheet', function (Blueprint $table) {
                    $table->bigIncrements('id');
                    $table->string('whs_id', '32')->nullable();
                    $table->tinyInteger('status');
                    $table->timestamp('completed_at')->nullable();
                    $table->string('completed_by', 32)->nullable();
                    $table->string('created_by', 32)->nullable();
                    $table->string('updated_by', 32)->nullable();
                    $table->integer('recount_of')->nullable();
                    $table->string('whs_order_id', 32)->nullable();
                    $table->text('comments')->nullable();
                    $table->tinyInteger('blind_count')->default(0);
                    $table->timestamps();
                    $table->softDeletes();
                });
                DB::update("ALTER TABLE ccsheet AUTO_INCREMENT = 1000;");
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
        Schema::dropIfExists('ccsheet');
    }
}

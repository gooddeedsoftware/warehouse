<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('company')) {
            Schema::create('company', function (Blueprint $table) {
                $table->string('id', 32)->unique()->index();
                $table->string('name', 255)->nullable();
                $table->string('IBAN', 50)->nullable();
                $table->string('BIC', 50)->nullable();
                $table->string('account_number', 50)->nullable();
                $table->text('company_information')->nullable();
                $table->string('company_email', 50)->nullable();
                $table->string('company_VAT', 50)->nullable();
                $table->longText('post_address')->nullable();
                $table->string('phone', 15)->nullable();
                $table->string('fax', 15)->nullable();
                $table->string('visma_clientnumber')->nullable();
                $table->string('city', 50)->nullable();
                $table->string('zip', 15)->nullable();
                $table->string('web_page')->nullable();
                $table->string('country')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
        $this->seed();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('company');
    }
    public function seed()
    {
        DB::table('company')->insert(array(
            array(
                'id'                  => '5dfbd06740f94131a23y673730f98fcf',
                'name'                => 'Gantic AS',
                'IBAN'                => '',
                'BIC'                 => '',
                'account_number'      => '',
                'company_information' => '',
                'company_email'       => '',
                'company_VAT'         => '',
                'post_address'        => '',
                'phone'               => '',
                'fax'                 => '',
                'city'                => '',
                'zip'                 => '',
            ),
        ));
    }
}

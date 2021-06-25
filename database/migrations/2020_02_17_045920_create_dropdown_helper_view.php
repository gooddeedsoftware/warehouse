<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateDropdownHelperView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('DROP VIEW dropdown_helper_view');
        if (Schema::hasTable('dropdown_helper')) {
            DB::statement("CREATE VIEW dropdown_helper_view AS
                            select group_code AS groupcode,group_name AS groupname,
                            key_code AS keycode,value_no AS label, ('no') as language
                            from dropdown_helper
                            union all
                            select group_code AS groupcode,group_name AS groupname,
                            key_code AS keycode,value_en AS label, 'en' as language
                            from dropdown_helper");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW dropdown_helper_view');
    }
}

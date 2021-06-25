<?php
use App\Helpers\GanticHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsertypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('user_type')) {
            Schema::create('user_type', function (Blueprint $table) {
                $table->string('id', 32)->unique()->index();
                $table->string('type', 50)->nullable();
                $table->tinyInteger('status')->default(0);
                $table->text('permissions')->nullable();
                $table->softDeletes();
                $table->timestamps();
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
        Schema::drop('user_type');
    }
    /*
     *   Default seeding for Usertypes
     */
    public function seed()
    {
        $save                = array();
        $save['id']          = GanticHelper::gen_uuid();
        $save['type']        = 'Administrative';
        $save['status']      = 1;
        $save['permissions'] = '{"superuser":1}';
        DB::table('user_type')->insert($save);

        $save           = array();
        $save['id']     = GanticHelper::gen_uuid();
        $save['type']   = 'Admin';
        $save['status'] = 1;
        DB::table('user_type')->insert($save);

        $save           = array();
        $save['id']     = GanticHelper::gen_uuid();
        $save['type']   = 'User';
        $save['status'] = 1;
        DB::table('user_type')->insert($save);

        $save           = array();
        $save['id']     = GanticHelper::gen_uuid();
        $save['type']   = 'Department Chief';
        $save['status'] = 1;
        DB::table('user_type')->insert($save);
    }
}

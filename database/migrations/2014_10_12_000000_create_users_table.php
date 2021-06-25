<?php

use App\Helpers\GanticHelper;
use App\Models\UserType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->string('id', 32)->unique()->index();
            $table->string('customer_id', 32)->nullable()->nullable();
            $table->string('usertype_id', 32)->index()->nullable();
            $table->string('department_id', 32)->index()->nullable();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 30)->nullable();
            $table->string('initials')->nullable();
            $table->string('phone', 15)->nullable();
            $table->TinyInteger('dept_chief')->nullable();
            $table->text('signature')->nullable();
            $table->text('signature_image')->nullable();
            $table->longText('user_image')->nullable();
            $table->string('notify_medium', 10)->nullable();
            $table->string('notify_frequency', 15)->nullable();
            $table->text('permissions')->nullable();
            $table->tinyInteger('activated')->default(0);
            $table->string('activation_code', 255)->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->rememberToken();
            $table->integer('pagination_size')->default(10);
            $table->double('hourly_rate', 15, 2)->nullable();
            $table->string('added_by', 32)->nullable();
            $table->string('updated_by', 32)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            $table->SoftDeletes();
        });
        $this->seed();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user');
    }

    public function seed()
    {
        $save['id']          = GanticHelper::gen_uuid();
        $usertype            = UserType::select('id')->where('type', '=', 'Administrative')->first();
        $save['email']       = 'vitali@avalia.no';
        $save['password']    = bcrypt('Avalia123!');
        $save['usertype_id'] = $usertype->id;
        $save['permissions'] = '{"superuser":1}';
        $save['first_name']  = 'Vitali';
        $save['last_name']   = 'Berby';
        $save['initials']    = 'VB';
        DB::table('user')->insert($save);
    }
}

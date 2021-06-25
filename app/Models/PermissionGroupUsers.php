<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionGroupUsers extends Model
{
    protected $table   = 'permission_group_users';
    public $timestamps = true;

    protected $fillable = array(
        'group_id',
        'user_id',
        'module',
    );

}

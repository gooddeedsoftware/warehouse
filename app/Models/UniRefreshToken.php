<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniRefreshToken extends Model
{
    protected $table    = 'uni_refresh_token';
    protected $fillable = ['refresh_token'];
}

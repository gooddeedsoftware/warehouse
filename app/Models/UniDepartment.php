<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniDepartment extends Model
{
    protected $table    = 'uni_department';
    public $timestamps  = false;
    protected $fillable = array('id', 'uni_id', 'name');

}

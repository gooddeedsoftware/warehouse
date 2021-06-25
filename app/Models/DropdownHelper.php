<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DropdownHelper extends Model
{
    protected $table = 'dropdown_helper_view';
    public $timestamps = true;
    protected $fillable = array('groupcode', 'groupname', 'keycode', 'label', 'language');
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniSellers extends Model
{
    protected $table    = 'uni_sellers';
    public $timestamps  = false;
    protected $fillable = array('id', 'uni_id', 'name');

}

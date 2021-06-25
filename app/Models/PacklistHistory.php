<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PacklistHistory extends Model
{

    protected $table    = 'packlist_history';
    public $timestamps  = true;
    protected $fillable = array('order_id', 'user_id', 'pdf_data');

}

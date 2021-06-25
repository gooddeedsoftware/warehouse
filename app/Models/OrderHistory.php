<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderHistory extends Model
{

    protected $table     = 'order_history';
    public $timestamps   = true;
    public $incrementing = false;
    use SoftDeletes;
    protected $fillable = array('id', 'order_id', 'order_data', 'modified_date', 'modified_by');
}

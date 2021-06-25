<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UNICustomers extends Model
{
    protected $table    = 'uni_customers';
    public $timestamps  = false;
    protected $fillable = array('id', 'uni_id', 'name', 'org_number', 'customer_number');

}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniAccounts extends Model
{
    protected $table   = 'uni_accounts';
    public $timestamps = false;
	protected $fillable = array('id', 'uni_id','account_no','account_name');

}

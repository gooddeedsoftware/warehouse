<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Session;

class UserType extends Model
{

    protected $table     = 'user_type';
    public $timestamps   = true;
    public $incrementing = false;
    use SoftDeletes;

    protected $dates    = ['deleted_at'];
    protected $fillable = array('id', 'type', 'status', 'permissions');

    public function user()
    {
        return $this->hasMany('App\Models\Usertype');
    }

    public static function getUsertypes($conditions = false, $orderby = 'type', $order = 'asc')
    {
        $Usertype = UserType::orderBy($orderby, $order);
        if (isset($conditions['search']) && $conditions['search'] != '') {
            $search = $conditions['search'];
            $Usertype->where(function ($query) use ($search) {
                $query->orwhere('type', 'LIKE', '%' . $search . '%');

            });

        }
        $paginate_size = Session::get('paginate_size') ? Session::get('paginate_size') : 10;
        return $Usertype->paginate($paginate_size);
    }

}

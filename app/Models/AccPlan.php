<?php
namespace App\Models;

use DB;
use Session;
use Redirect;
use App\Models\Department;
use Kyslik\ColumnSortable\Sortable;
use App\Helpers\MaskinstyringHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccPlan extends Model {

	protected $table = 'acc_plan';
	public $timestamps = true;
	public $incrementing = false;
    
	use SoftDeletes;
	use Sortable;

	protected $fillable = array('id', 'AccountNo','Name','AccountGroup','ResAccount', 'TaxCode', 'DefAccount','deleted_at','added_by', 'updated_by', 'uni_id');
	protected $sortable = array('AccountNo', 'Name', 'AccountGroup', 'ResAccount', 'TaxCode', 'DefAccount', 'uni_id');

	public function product() {
		return $this->hasMany('App\Models\Product', 'acc_plan_id', 'id');
	}

	/*
	*Getting the acc plan details 
	*/
	public function scopeGetAccPlans($query, $conditions = false)
    {
		$activities = AccPlan::whereNull('deleted_at')->with('product');
        if (isset($conditions['search']) && $conditions['search'] != '') {
            $search = $conditions['search'];
            $activities->where(function ($query) use ($search) {
                $query->orwhere('AccountNo', 'LIKE', '%' . $search . '%');
				$query->orwhere('Name', 'LIKE', '%' . $search . '%');
				$query->orwhere('AccountGroup', 'LIKE', '%' . $search . '%');
				$query->orwhere('ResAccount', 'LIKE', '%' . $search . '%');
				$query->orwhere('TaxCode', 'LIKE', '%' . $search . '%');
				$query->orwhere('DefAccount', 'LIKE', '%' . $search . '%');
            });
        }
        $paginate_size = Session::get('paginate_size') ? Session::get('paginate_size') : 10; 
		return $activities->sortable(['AccountNo'])->paginate($paginate_size);
    }
    
}
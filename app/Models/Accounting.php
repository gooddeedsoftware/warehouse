<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\MaskinstyringHelper;
use App\Models\Department;
use Redirect;
use DB;
use Session;

class Accounting extends Model {

	protected $table = 'accouting';
	public $timestamps = true;
	public $incrementing=false;
    
	use SoftDeletes;

	protected $fillable = array('id', 'department_id','ltkode','unit','billable', 'wgsrt_wagetype', 'description', 'price','VAT', 'comments','deleted_at','invoice_text');

	public function department(){
		return $this->belongsTo('App\Models\Department','department_id','id');
	}
	public static function getAccouting($conditions = false,$orderby='ltkode',$order='asc')
    {
		$activities = activities::orderBy($orderby,$order);
		$activities->leftjoin('department','department.id', '=', 'activities.department_id');
		$activities->addselect('activities.*','department.Name as department_name');
        if (isset($conditions['search']) && $conditions['search'] != '') {
            $search = $conditions['search'];
            $activities->where(function ($query) use ($search) {
                $query->orwhere('ltkode', 'LIKE', '%' . $search . '%');
				$query->orwhere('unit', 'LIKE', '%' . $search . '%');
				$query->orwhere('price', 'LIKE', '%' . $search . '%');
				$query->orwhere('vat', 'LIKE', '%' . $search . '%');
				$query->orwhere('comments', 'LIKE', '%' . $search . '%');
				$query->orwhereHas('department', function ($query) use ($search) {
                    $query->where('Name', 'LIKE', '%' . $search . '%');                    
                });
            });
        }
        $paginate_size = Session::get('paginate_size') ? Session::get('paginate_size') : 10; 
		return $activities->paginate($paginate_size);
    }
    
}
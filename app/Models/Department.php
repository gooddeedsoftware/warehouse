<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Session;

class Department extends Model
{
    use Sortable, SoftDeletes;
    protected $table     = 'department';
    public $incrementing = false;
    public $timestamps   = true;

    protected $fillable = array('id', 'Nbr', 'Name', 'status', 'deleted_at', 'added_by', 'updated_by', 'uni_department');

    public function user()
    {
        return $this->hasMany('App\Models\User', 'department_id', 'id');
    }


    /**
     * get DepartmentDetails
     * @param  String $conditions
     * @return object
     */
    public static function getDepartmentDetails($conditions = false)
    {
        $department = Department::whereNull('deleted_at')->with('user');
        if (isset($conditions['search']) && $conditions['search'] != '') {
            $search = $conditions['search'];
            $department->where(function ($query) use ($search) {
                $query->orwhere('Nbr', 'LIKE', '%' . $search . '%');
                $query->orwhere('Name', 'LIKE', '%' . $search . '%');
            });
        }
        $paginate_size = Session::get('paginate_size') ? Session::get('paginate_size') : 10;
        return $department->sortable(['Name'])->paginate($paginate_size);
    }

    // get single department details
    public static function getDepartmentDetail($id)
    {
        $department_details = Department::where('id', "=", $id)->get();
        return $department_details;
    }
}

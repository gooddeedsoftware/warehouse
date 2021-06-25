<?php
namespace App\Models;

use App\Helpers\GanticHelper;
use App\Models\Department;
use Auth;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Session;

class Activities extends Model
{
    protected $table     = 'activities';
    public $timestamps   = true;
    public $incrementing = false;
    use SoftDeletes;
    use Sortable;

    protected $fillable = array('id', 'department_id', 'ltkode', 'unit', 'billable', 'wgsrt_wagetype', 'description', 'price', 'VAT', 'comments', 'deleted_at', 'invoice_text', 'fk_AccountNo', 'travel_expense', 'show_to_all', 'category', 'added_by', 'updated_by');

    protected $sortable = array('ltkode', 'unit', 'wgsrt_wagetype', 'description', 'price', 'VAT');

    /**
     * [department description]
     * @return [type] [description]
     */
    public function department()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

    public function acc_plan()
    {
        return $this->belongsTo('App\Models\AccPlan', 'fk_AccountNo', 'id');
    }

    /**
     * getActivities
     * @param  string $conditions
     * @return object
     */
    public static function getActivities($conditions = false)
    {
        $activities = Activities::with('department', 'acc_plan');
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
                $query->orwhereHas('acc_plan', function ($query) use ($search) {
                    $query->where('acc_plan.AccountNo', 'LIKE', '%' . $search . '%');
                });
            });
        }
        $paginate_size = Session::get('paginate_size') ? Session::get('paginate_size') : 10;
        return $activities->sortable(['ltkode'])->paginate($paginate_size);
    }

    /**
     * department Sortable
     * @param  string $query
     * @param  string $direction
     * @return void
     */
    public function departmentSortable($query, $direction)
    {
        $query->orderby('department.Name', $direction);
    }

    /**
     * Account Sortable
     * @param  string $query
     * @param  string $direction
     * @return void
     */
    public function accountSortable($query, $direction)
    {
        $query->orderby('acc_plan.AccountNo', $direction);
    }

    /**
     * Creates an or update.
     *
     * @param      <type>   $data   The data
     * @param      boolean  $id     The identifier
     */
    public static function createOrUpdate($input, $id = false, $activityRef = false)
    {
        $input['id']             = GanticHelper::gen_uuid();
        $input['billable']       = @$input['billable'] ? '1' : '0';
        $input['travel_expense'] = @$input['travel_expense'] ? '1' : '0';
        $input['show_to_all']    = @$input['show_to_all'] ? '1' : '0';
        $input['added_by']       = Session::get('currentUserID');
        if ($id) {
            $activity = $activityRef->fill($input);
            $activity = $activityRef->save();
        } else {
            $activity = Activities::create($input);
        }
        return $activity;
    }

    // get activity single details
    public function scopeGetActivityDetails($query, $field, $value)
    {
        $activity_detail = $query->where($field, '=', $value)->with('acc_plan', 'department')->first();
        return $activity_detail;
    }

    // get activitie by department
    public function scopeGetActivitiesByDepartment($query, $department)
    {
        $usertype = Session::get('usertype');
        try {
            if ($usertype == "User") {
                $activities = Activities::select(DB::Raw("concat(ltkode, ' - ', IFNULL(description,'')) AS wagetype,id"))->where('department_id', '=', Auth::getUser()->department_id)->where('billable', '=', '1')->where('travel_expense', '=', 0)->where(function ($query) {
                    $query->where('category', '!=', '5')->orwhereNull('category');
                });
                $activities->orwhere('show_to_all', '=', '1')->where('billable', '=', '1')->where('travel_expense', '=', '0')->where(function ($query) {
                    $query->where('category', '!=', '5')->orwhereNull('category');
                });
                $activities = $activities->orderby('ltkode', 'asc')->lists('wagetype', 'id');
                return $activities;
            } else {
                if ($usertype == "Department Chief") {
                    $activities = Activities::select(DB::Raw("concat(ltkode, ' - ', IFNULL(description,'')) AS wagetype,id"))->whereIN('department_id', $department)->orderby('ltkode', 'asc')->where('billable', '=', '1')->where('travel_expense', '=', 0)->where(function ($query) {
                        $query->where('category', '!=', '5')->orwhereNull('category');
                    });
                    $activities->orwhere('show_to_all', '=', '1')->where('billable', '=', '1')->where('travel_expense', '=', '0')->where(function ($query) {
                        $query->where('category', '!=', '5')->orwhereNull('category');
                    });
                    $activities = $activities->lists('wagetype', 'id');
                } else {
                    $activities = Activities::select(DB::Raw("concat(ltkode, ' - ', IFNULL(description,'')) AS wagetype,id"))->whereIN('department_id', $department)->orderby('ltkode', 'asc')->where('billable', '=', '1')->where('travel_expense', '=', 0)->orwhere('show_to_all', '=', '1')->where('billable', '=', '1')->where('travel_expense', '=', '0')->lists('wagetype', 'id');
                }
                return $activities;
            }
        } catch (Exception $e) {
            return false;
        }
    }

}

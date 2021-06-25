<?php
namespace App\Models;

use App\Helpers\GanticHelper;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Image;
use Kyslik\ColumnSortable\Sortable;
use Session;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{

    protected $table     = 'user';
    public $timestamps   = true;
    public $incrementing = false;

    use SoftDeletes;
    use Sortable;
    use Notifiable;
    protected $fillable = array('id', 'customer_id', 'usertype_id', 'department_id', 'email', 'password', 'first_name', 'last_name', 'initials', 'phone', 'dept_chief', 'signature', 'signature_image', 'user_image', 'notify_medium', 'notify_frequency', 'permissions', 'activated', 'activation_code', 'activated_at', 'last_login', 'remember_token', 'deleted_at', 'pagination_size', 'hourly_rate', 'added_by', 'updated_by', 'uni_seller');
    protected $sortable = array('email', 'first_name', 'last_name', 'phone');

    /**
     * usertype
     * @return object
     */
    public function usertype()
    {
        return $this->belongsTo('App\Models\UserType', 'usertype_id', 'id');
    }

    /**
     * customer
     * @return object
     */
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id', 'id');
    }

    /**
     * [department description]
     * @return [type] [description]
     */
    public function department()
    {
        return $this->belongsTo('App\Models\Department', 'department_id', 'id');
    }

    /**
     * order
     * @return object
     */
    public function order()
    {
        return $this->hasMany('App\Models\Order', 'project_owner', 'id');
    }

    /**
     * getUsers
     * @param  boolean $conditions
     * @param  string  $orderby
     * @param  string  $order
     * @param  boolean $active_users
     * @return object
     */
    public static function getUsers($conditions = false, $active_users = false)
    {
        $user = User::leftjoin('user_type', 'user_type.id', '=', 'user.usertype_id');
        $user->leftjoin('department', 'department.id', '=', 'user.department_id');
        $user->addSelect('user.*', 'user_type.type as user_type_name', 'department.Name as department_name');
        if (isset($conditions['search']) && $conditions['search'] != '') {
            $search = $conditions['search'];
            $user->where(function ($query) use ($search) {
                $query->orwhere('user.first_name', 'LIKE', '%' . $search . '%');
                $query->orwhere('user.last_name', 'LIKE', '%' . $search . '%');
                $query->orwhere('department.name', 'LIKE', '%' . $search . '%');
                $query->orwhere('user.email', 'LIKE', '%' . $search . '%');
                $query->orwhere('user.phone', 'LIKE', '%' . $search . '%');
                $query->orwhereHas('usertype', function ($query) use ($search) {
                    $query->where('type', 'LIKE', '%' . $search . '%');
                });
            });
        }
        $usertype = Session::get('usertype') == "Admin" || Session::get('usertype') == "Administrative" ? true : false;
        if (!$usertype) {
            $user->where('user_type.type', '!=', "Admin");
            $user->where('user.activated', '=', "0");
        }
        // filter by active
        if ($active_users != '') {
            $user->where('user.activated', '=', $active_users);
        } else {
            $user->where('user.activated', '=', "0");
        }
        $paginate_size = Session::get('paginate_size') ? Session::get('paginate_size') : 10;
        return $user->sortable(['first_name'])->paginate($paginate_size);
    }

    /**
     * departmentSortable
     * @param  string $query
     * @param  string $direction
     * @return void
     */
    public function departmentSortable($query, $direction)
    {
        $query->orderby('department.Name', $direction);
    }

    /**
     * usertypeSortable
     * @param  string $query
     * @param  string $direction
     * @return void
     */
    public function usertypeSortable($query, $direction)
    {
        $query->orderby('user_type.type', $direction);
    }

    /**
     * getUser
     * @param  string $userid
     * @return object
     */
    public static function getUser($userid)
    {
        $user = User::with('usertype')->where('id', '=', $userid)->first();
        return $user;
    }

    /**
     * Save a new model and return the instance.
     *
     * @param  array  $attributes
     * @return static
     */
    public static function upload_file($request, $file, $files = false)
    {

        if ($request->hasFile($file) && $request->file($file)->getSize() > 0) {
            $destinationPath = storage_path() . "/uploads/user/";
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $filename                   = $request->file($file)->getClientOriginalName();
            $extension                  = $request->file($file)->getClientOriginalExtension();
            $fileName                   = GanticHelper::gen_uuid() . '.' . $extension;
            $return['fileName']         = $fileName;
            $return['filePath']         = '/uploads/user/' . $fileName;
            $return['fileSize']         = $request->file($file)->getSize();
            $return['fileType']         = $request->file($file)->getMimeType();
            $return['fileExtension']    = $extension;
            $return['fileOriginalName'] = $filename;
            $tmp_file                   = GanticHelper::resizeImage($request->file($file)->getRealPath(), 200, 200, 0, $extension);
            Image::make($tmp_file)->save($destinationPath . $fileName);
            $files = json_encode($return);
        }
        return $files;
    }

    /**
     * [insert_reset_password_token description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public static function insert_reset_password_token($data)
    {
        DB::table('password_resets')->where('email', $data['email'])->delete();
        DB::table('password_resets')->insert($data);
    }

    // get users by department id
    /**
     * [getusersByDepartmentID description]
     * @param  [type] $department_id [description]
     * @return [type]                [description]
     */
    public static function getusersByDepartmentID($department_id)
    {
        $users = array();
        try {
            $results = User::where('department_id', '=', $department_id)->where('activated', '=', 0)->get()->toArray();
            foreach ($results as $key => $value) {
                $users[] = $value['id'];
            }
        } catch (Exception $e) {}
        return $users;
    }

    /**
     * [getUsersDropDown description]
     * @return [type] [description]
     */
    public static function getUsersDropDown($type = false, $offer_permission_id = false)
    {
        try {
            if ($type == 1) {
                $users = OfferPermissionUsers::select('user_id')->get()->toArray();
                return User::select(DB::Raw("concat(first_name ,' ', last_name) AS name, id"))->where('activated', '=', 0)->whereNotIn('id', $users)->orderBy('first_name', 'asc')->pluck('name', 'id');
            } elseif ($type == 2) {
                $users = OfferPermissionUsers::select('user_id')->where('offer_permission_id', '!=', $offer_permission_id)->get()->toArray();
                return User::select(DB::Raw("concat(first_name ,' ', last_name) AS name, id"))->where('activated', '=', 0)->whereNotIn('id', $users)->orderBy('first_name', 'asc')->pluck('name', 'id');
            } else {
                return User::select(DB::Raw("concat(first_name ,' ', last_name) AS name, id"))->where('activated', '=', 0)->orderBy('first_name', 'asc')->pluck('name', 'id');
            }
        } catch (Exception $e) {
            echo $e;die;
        }

    }

    /**
     * [getUsersDropDown description]
     * @return [type] [description]
     */
    public static function getUsersDropDownForGroup($type = false, $group_id = false, $module)
    {
        try {
            if ($type == 1) {
                $users = PermissionGroupUsers::select('user_id')->where('module', '=', $module)->get()->toArray();
                return User::select(DB::Raw("concat(first_name ,' ', last_name) AS name, id"))->where('activated', '=', 0)->whereNotIn('id', $users)->orderBy('first_name', 'asc')->pluck('name', 'id');
            } elseif ($type == 2) {
                $users = PermissionGroupUsers::select('user_id')->where('module', '=', $module)->where('group_id', '!=', $group_id)->get()->toArray();
                return User::select(DB::Raw("concat(first_name ,' ', last_name) AS name, id"))->where('activated', '=', 0)->whereNotIn('id', $users)->orderBy('first_name', 'asc')->pluck('name', 'id');
            } else {
                return User::select(DB::Raw("concat(first_name ,' ', last_name) AS name, id"))->where('activated', '=', 0)->orderBy('first_name', 'asc')->pluck('name', 'id');
            }

        } catch (Exception $e) {
            echo $e;die;
        }

    }
}

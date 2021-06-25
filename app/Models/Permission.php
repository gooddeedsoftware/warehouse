<?php
namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Route;
use App\Models\UserType;
use Session;

//use Illuminate\Database\Eloquent\SoftDeletes;
class Permission extends Model
{
    protected $table = 'permission';
    public $timestamps = true;
    //use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = array('name', 'slug', 'permissions');
    /**
     * Mutator for taking permissions.
     *
    
     *
     * @param $permissions
     *
     * @return void
     */

    public static function search_permission($conditions = false,$orderby='name',$order='asc',$id = false)
    {

        $permission = Permission::select('id', 'name');
        if (isset($conditions['search']) && $conditions['search'] != '') {
            $search = $conditions['search'];
            $permission->where(function ($query) use ($search) {
                $query->orwhere('name', 'LIKE', '%' . $search . '%');

            });
        }
        $paginate_size = Session::get('paginate_size') ? Session::get('paginate_size') : 10; 
		return $permission->orderBy('permission.'.$orderby,$order)->paginate($paginate_size);
       
    }
    /**
     * Construct the permission with module and methods.
     *
     
     *
     * @param $permissions
     *
     * @return void
     */
    public static function getper($permissions)
    {
        $module = lcfirst(@$permissions['slug']);
        $roles = array();
        if (@$permissions['permissions']) {
            //prefix the permission with the module name ex: user.create
            foreach ($permissions['permissions'] as $key => $value) {
                $roles[] = $module . '.' . $value;
            }
        }
        $permission = (!empty($roles)) ? json_encode($roles) : '';
        return $permission;
    }
    /**
     * format the json input to array
     *
     
     *
     * @param $input json string
     *
     * @return void
     */
    public static function formatpermission($input)
    {
        $permissions = json_decode($input);
        if ($permissions) {
            return Permission::toArrayn($permissions);
        } else {
            return array();
        }
    }
    /**
     * Convert the object array to associative array.
     *
     
     *
     * @param $object
     *
     * @return array
     */
    public static function toArrayn($obj)
    {
        if (is_object($obj)) {
            $obj = (array) $obj;
        }

        if (is_array($obj)) {
            $new = array();
            foreach ($obj as $key => $val) {
                $new[$key] = static::toArrayn($val);
            }
        } else {
            $new = $obj;
        }
        return $new;
    }
    /**
     * Get the permission of logged in user and merged with user group permissions.
     *
     
     *
     * @param $permissions
     *
     * @return array
     */
    public static function getMergedPermissions()
    {
        $permissions = array();
        if (Auth::user()) {
            $group = UserType::where('status', 1)->find(Auth::user()->usertype_id);
            if ($group) {
                $grouppermission = Permission::toArrayn(json_decode($group->permissions));
                if ($group->type == 'Customer' || $group->type == 'Kunde'  ) {
                    $grouppermission = array_merge($grouppermission, Permission::getCustomerPermissions());
                }
                $userpermission = Permission::toArrayn(json_decode(Auth::user()->permissions));
                if ($grouppermission && is_array($grouppermission)) {
                    $permissions = array_merge($permissions, $grouppermission);
                }

                if ($userpermission && is_array($userpermission)) {
                    $permissions = array_merge($permissions, $userpermission);
                }
            }
        }
        return $permissions;
    }

    public static function getCustomerPermissions()
    {
        return array('kunde.view' => 1, 'kunde.index' => 1, 'maskinstyring.home' => 1, 'checklistreport.equipment' => 1);
    }

    /**
     * See if a user has access to the passed permission(s).
     * Permissions are merged from all groups the user belongs to
     * and then are checked against the passed permission(s).
     *
     * If multiple permissions are passed, the user must
     * have access to all permissions passed through, unless the
     * "all" flag is set to false.
     *
     * Super users have access no matter what.
     *
     * @param  string|array  $permissions
     * @param  bool  $all
     * @return bool
     */
    public static function hasAccess($permissions, $all = true)
    {

        if (static::isSuperUser()) {
            return true;
        }

        return static::hasPermission($permissions, $all);
    }
    /**
     * Checks if the user is a super user - has
     * access to everything regardless of permissions.
     *
     * @return bool
     */
    public static function isSuperUser()
    {
        return static::hasPermission('superuser');
    }
    /**
     * Checks if the user is a super user - has
     * access to everything regardless of permissions.
     *
     * @return bool
     */
    public static function isCustomerUser()
    {
        return static::hasPermission('customeruser');
    }

    /**
     * See if a user has access to the passed permission(s).
     * Permissions are merged from all groups the user belongs to
     * and then are checked against the passed permission(s).
     *
     * If multiple permissions are passed, the user must
     * have access to all permissions passed through, unless the
     * "all" flag is set to false.
     *
     * Super users DON'T have access no matter what.
     *
     * @param  string|array  $permissions
     * @param  bool  $all
     * @return bool
     */
    public static function hasPermission($permissions, $all = true)
    {
        $mergedPermissions = Permission::getMergedPermissions();

        if (!is_array($permissions)) {
            $permissions = (array) $permissions;
        }

        foreach ($permissions as $permission) {
            // We will set a flag now for whether this permission was
            // matched at all.
            $matched = true;

            // Now, let's check if the permission ends in a wildcard "*" symbol.
            // If it does, we'll check through all the merged permissions to see
            // if a permission exists which matches the wildcard.
            if ((strlen($permission) > 1) and ends_with($permission, '*')) {
                $matched = false;

                foreach ($mergedPermissions as $mergedPermission => $value) {
                    // Strip the '*' off the end of the permission.
                    $checkPermission = substr($permission, 0, -1);

                    // We will make sure that the merged permission does not
                    // exactly match our permission, but starts with it.
                    if ($checkPermission != $mergedPermission and starts_with($mergedPermission, $checkPermission) and $value == 1) {
                        $matched = true;
                        break;
                    }
                }
            } elseif ((strlen($permission) > 1) and starts_with($permission, '*')) {
                $matched = false;

                foreach ($mergedPermissions as $mergedPermission => $value) {
                    // Strip the '*' off the beginning of the permission.
                    $checkPermission = substr($permission, 1);

                    // We will make sure that the merged permission does not
                    // exactly match our permission, but ends with it.
                    if ($checkPermission != $mergedPermission and ends_with($mergedPermission, $checkPermission) and $value == 1) {
                        $matched = true;
                        break;
                    }
                }
            } else {
                $matched = false;

                foreach ($mergedPermissions as $mergedPermission => $value) {
                    // This time check if the mergedPermission ends in wildcard "*" symbol.
                    if ((strlen($mergedPermission) > 1) and ends_with($mergedPermission, '*')) {
                        $matched = false;

                        // Strip the '*' off the end of the permission.
                        $checkMergedPermission = substr($mergedPermission, 0, -1);

                        // We will make sure that the merged permission does not
                        // exactly match our permission, but starts with it.
                        if ($checkMergedPermission != $permission and starts_with($permission, $checkMergedPermission) and $value == 1) {
                            $matched = true;
                            break;
                        }
                    }

                    // Otherwise, we'll fallback to standard permissions checking where
                    // we match that permissions explicitly exist.
                    elseif ($permission == $mergedPermission and $mergedPermissions[$permission] == 1) {
                        $matched = true;
                        break;
                    }
                }
            }

            // Now, we will check if we have to match all
            // permissions or any permission and return
            // accordingly.
            if ($all === true and $matched === false) {
                return false;
            } elseif ($all === false and $matched === true) {
                return true;
            }
        }

        if ($all === false) {
            return false;
        }

        return true;
    }
    /*
     * Get the rules for checking permision of each method with moduels
     *
     * return @string

     */
    public static function getRule($userRule = false)
    {
        $explode = explode('.', Route::currentRouteName());
        if (count($explode) < 3) {
            return Route::currentRouteName();
        }
        list($prefix, $module, $rule) = $explode;
        return $module . '.' . $rule;
    }

}

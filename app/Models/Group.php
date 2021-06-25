<?php
/*
------------------------------------------------------------------------------------------------
Created By   : S David Antony
Email:       : david@processdrive.com
Created Date : 5.4.2018
Purpose      : Group model
------------------------------------------------------------------------------------------------
*/
namespace App\Models;
use Session;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
	use sortable;
    protected $table = 'group';
	public $timestamps = true;

	protected $fillable = array(
                    'group',
                    'module',
                  );
	 protected $sortable = array('group', 'module');
	/**
	 * [getGroups description]
	 * @return [type] [description]
	 */
	public static function getGroups($conditions = false) {
		try {
			$group_details = Group::select('*');
	        if (isset($conditions['search']) && $conditions['search'] != '') {
	            $search = $conditions['search'];
	            $group_details->where(function ($query) use ($search) {
	                $query->orwhere('group', 'LIKE', '%' . $search . '%');
	            });
	        }
	        $paginate_size = Session::get('paginate_size') ? Session::get('paginate_size') : 10;
			$group_details = $group_details->sortable(['group' => 'desc'])->paginate($paginate_size);
			return $group_details;
		} catch (\Exception $e) {
			echo $e;
			exit;
		}
	}


	/**
     * [getDataForEdit description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public static function getDataForEditOrCreate($id = false) {
    	try {
    		if (@$id) {
    			$data['group'] = Group::findOrFail($id);
    			$data['users'] = User::getUsersDropDownForGroup(2, $id, $data['group']->module);
    			$data['selected_users'] = array();
    			$selected_users = PermissionGroupUsers::where('group_id', '=', $id)->get()->toArray();
    			if (@$selected_users) {
    				foreach ($selected_users as $key => $value) {
    					$data['selected_users'][] = $value['user_id'];
    				}
    			}
    		} else {
                $data['users'] = [];
            }
            $data['modules'] = ['Offer' => trans('main.offer.title')];
    		return $data;
    	} catch (\Exception $e) {
    		echo $e;
    		exit;
    	}
    }


       /**
     * [saveOfferGroupDetails description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public static function saveGroupDetails($data) {
    	try {
    		if(@$data) {
    			$input['group'] = $data['group'];
                $input['module'] = $data['module'];
    			$offer_group = Group::create($input);
    			Group::storeGroupUserDetails($data, $offer_group->id, $data['module']);
                OfferPermission::setOfferModulePermssion();
                Agreement::setAgreementModulePermssion();
    		}
    	} catch (\Exception $e) {
    		echo $e;
    		exit;
    	}
    }


    /**
	   * [updateOfferGroupDetails description]
	   * @param  [type] $data [description]
	   * @param  [type] $id   [description]
	   * @return [type]       [description]
	   */
    public static function updateOfferGroupDetails($data, $id) {
    	try {
    		if($id) {
    			$offer_group = Group::findOrFail($id);
                $input['group'] = $data['group'];
                $input['module'] = $data['module'];
                $offer_group->fill($input);
				$offer_group->save();
				PermissionGroupUsers::where('group_id', '=', $id)->delete();
                Group::storeGroupUserDetails($data, $id, $data['module']);
                OfferPermission::setOfferModulePermssion();
                Agreement::setAgreementModulePermssion();
			}
    	} catch (\Exception $e) {
    		echo $e;
    		exit;
    	}
    }


    /**
     * [storeOfferPermssionUserDetails description]
     * @param  [type] $data [description]
     * @param  [type] $id   [description]
     * @return [type]       [description]
     */
    public static function storeGroupUserDetails($data, $id, $module) {
        try {
            if (@$data['users']) {
                foreach ($data['users'] as $key => $value) {
                    $record['group_id'] = $id;
                    $record['user_id'] = $value;
                    $record['module'] = $module;
                    PermissionGroupUsers::create($record);
                }
            }
        } catch (\Exception $e) {
            echo $e;
            exit;
        }
    }

}

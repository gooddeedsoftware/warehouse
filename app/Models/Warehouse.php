<?php

namespace App\Models;

use App\Helpers\GanticHelper;
use App\Models\Location;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Session;

class Warehouse extends Model
{

    protected $table     = 'warehouse';
    public $timestamps   = true;
    public $incrementing = false;
    use SoftDeletes;
    use Sortable;

    protected $fillable = array('id', 'shortname', 'main', 'description', 'deleted_at', 'notification_email', 'added_by', 'updated_by');
    protected $sortable = array('shortname', 'main', 'description', 'notification_email', 'added_by', 'updated_by');

    public function warehouseResponsible()
    {
        return $this->hasMany('App\Models\WarehouseResponsible', 'warehouse_id', 'id');
    }
    public function whsInventory()
    {
        return $this->hasMany('App\Models\WarehouseInventory', 'warehouse_id', 'id');
    }

    public function location()
    {
        return $this->hasMany('App\Models\Location');
    }

    /**
     * get WarehouseDetails
     * @param  string $conditions
     * @return object
     */
    public static function getWarehouseDetails($conditions = false)
    {
        $warehouse_details = Warehouse::whereNull('deleted_at')->with('whsInventory', 'location');
        if (Session::get('usertype') == "User") {
            $warehouse_ids = WarehouseResponsible::where('user_id', \Auth::user()->id)->pluck('warehouse_id')->toArray();
            $warehouse_details->whereIn('id', $warehouse_ids);
        }
        $warehouse_details->with('warehouseResponsible');
        if (isset($conditions['search']) && $conditions['search'] != '') {
            $search = $conditions['search'];
            $warehouse_details->where(function ($query) use ($search) {
                $query->orwhere('shortname', 'LIKE', '%' . $search . '%');
                $query->orwhere('description', 'LIKE', '%' . $search . '%');
            });
        }
        $paginate_size     = Session::get('paginate_size') ? Session::get('paginate_size') : 10;
        $warehouse_details = $warehouse_details->sortable(['shortname'])->paginate($paginate_size);

        for ($i = 0; $i < count($warehouse_details); $i++) {
            $warehouse_details[$i]->main = (($warehouse_details[$i]->main == 1) ? "01" : (($warehouse_details[$i]->main == 2) ? "02" : ""));
        }
        return $warehouse_details;
    }

    // get warehouse details for transfer orders
    public static function getWarehouseforTransferOrders($user_id = false)
    {
        $whs_transfer_array = array();
        try {
            if ($user_id) {
                $warehouse_details = Warehouse::leftjoin('warehouse_responsible', 'warehouse_responsible.warehouse_id', '=', 'warehouse.id')->where('warehouse_responsible.user_id', '=', $user_id)->orWhere('warehouse.main', '=', 1)->orderby('shortname', 'asc')->with('whsInventory')->get();
            } else {
                $warehouse_details = Warehouse::orderby('shortname', 'asc')->with('whsInventory')->get();
            }
            if (count($warehouse_details) > 0) {
                foreach ($warehouse_details as $key => $value) {

                    // if (count($value->whsInventory) > 0) {
                    $whs_transfer_array[$value->id] = $value->shortname;
                    // }
                }
            }
            return $whs_transfer_array;
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * [createDefaultLocation description]
     * @param  [type] $warehouse_id [description]
     * @return [type]               [description]
     */
    public static function createDefaultLocation($warehouse_id)
    {
        $location_data                    = [];
        $location_data['name']            = "Undefined";
        $location_data['id']              = GanticHelper::gen_uuid();
        $location_data['warehouse_id']    = $warehouse_id;
        $location_data['scrap_location']  = 1;
        $location_data['return_location'] = 1;
        Location::create($location_data);

    }
}

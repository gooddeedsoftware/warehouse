<?php
namespace App\Models;

use App\Models\Warehouse;
use App\Models\WarehouseInventory;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Session;

class Location extends Model
{

    protected $table     = 'whs_location';
    public $timestamps   = true;
    public $incrementing = false;
    use SoftDeletes, Sortable;

    protected $fillable = array('id', 'name', 'warehouse_id', 'scrap_location', 'return_location', 'deleted_at', 'added_by', 'updated_by');

    protected $sortable = array('name', 'warehouse_id', 'scrap_location', 'return_location');

    // warehouse relationship
    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse', 'warehouse_id', 'id');
    }

    // warehouse inventory relationship
    public function whsInventory()
    {
        return $this->hasMany('App\Models\WarehouseInventory', 'location_id', 'id');
    }

    // get Location
    public static function getLocations($conditions = false)
    {

        $location_details = Location::leftjoin('warehouse', 'warehouse.id', '=', 'whs_location.warehouse_id')->with('whsInventory');
        $location_details->addselect('whs_location.*', 'warehouse.shortname as shortname');
        $location_details->where('name', '!=', 'Undefined');
        if (Session::get('usertype') == "User") {
            $warehouse_ids = WarehouseResponsible::where('user_id', \Auth::user()->id)->pluck('warehouse_id')->toArray();
            $location_details->whereIn('warehouse_id', $warehouse_ids);
        }
        if (isset($conditions['search']) && $conditions['search'] != '') {
            $search = $conditions['search'];
            $location_details->where(function ($query) use ($search) {
                $query->orwhere('name', 'LIKE', '%' . $search . '%');
                $query->orwhereHas('warehouse', function ($query) use ($search) {
                    $query->where('shortname', 'LIKE', '%' . $search . '%');
                });
            });
        }
        $paginate_size    = Session::get('paginate_size') ? Session::get('paginate_size') : 10;
        $location_details = $location_details->sortable(['name'])->paginate($paginate_size);
        for ($i = 0; $i < count($location_details); $i++) {
            $location_details[$i]->scrap_location  = (($location_details[$i]->scrap_location == 1) ? "01" : (($location_details[$i]->scrap_location == 2) ? "02" : ""));
            $location_details[$i]->return_location = (($location_details[$i]->return_location == 1) ? "01" : (($location_details[$i]->return_location == 2) ? "02" : ""));
        }
        return $location_details;
    }

    /**
     * warehoue Sortable
     * @param  string $query
     * @param  string $direction
     * @return void
     */
    public function warehouseSortable($query, $direction)
    {
        $query->orderby('warehouse.shortName', $direction);
    }

    // get location details for warehouse order
    public static function getLocationsForWarehourOrders($order_type = false, $warehouse_id = false)
    {
        try {
            if ($order_type == "1") {
                //Transfer
                return Location::locationAsList($warehouse_id, $order_type);
            } else if ($order_type == "2") {
                // Adjustment
                return Location::locationAsList($warehouse_id, false, true);
            } else {
                return Location::locationAsList($warehouse_id);
            }
        } catch (Exception $e) {
            return false;
        }
    }

    // Location details in id, name as Lists
    public static function locationAsList($warehouse_id = false, $order_type = false, $adjustment_order = false)
    {
        try {
            if ($warehouse_id) {
                if ($order_type) {
                    $location_details = WarehouseInventory::select(DB::Raw("CONCAT(whs_location.name , '(', replace(FORMAT(SUM(whs_inventory.qty),2), '.', ','), ')') AS NAME, whs_inventory.location_id AS ID"))->leftjoin('whs_location', 'location_id', '=', 'whs_location.id')->where('whs_inventory.warehouse_id', '=', $warehouse_id)->whereNull('whs_inventory.deleted_at')->groupBy('location_id')->pluck("NAME", "ID");
                } else if ($adjustment_order) {
                    $location_details = Location::select(DB::Raw("CONCAT(whs_location.name , '(', FORMAT(SUM(whs_inventory.qty),0), ')') AS NAME, whs_location.id AS ID"))->orderby('name', 'asc')->leftjoin('whs_inventory', 'whs_inventory.location_id', '=', 'whs_location.id')->where('whs_location.warehouse_id', '=', $warehouse_id)->whereNull('whs_inventory.deleted_at')->groupBy('whs_inventory.location_id')->pluck('NAME', 'ID');
                } else {
                    $location_details = Location::orderby('name', 'asc')->where('warehouse_id', '=', $warehouse_id)->pluck('name', 'id');
                }
                return $location_details;
            }
        } catch (Exception $e) {
            return false;
        }

    }

    public static function locationAsListForAdjustment($warehouse_id)
    {
        $location_details = Location::orderby('name', 'asc')->where('warehouse_id', '=', $warehouse_id)->pluck('name', 'id');
        return $location_details;
    }
}

<?php
namespace App\Models;

use App\Models\Customer;
use App\Models\EquipmentCategory;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Session;

class Equipment extends Model
{

    protected $table     = 'equipment';
    public $timestamps   = true;
    public $incrementing = false;

    use SoftDeletes;
    use Sortable;

    protected $fillable = array('id', 'customer_id', 'sn', 'description', 'install_date', 'internalnbr', 'deleted_at', 'last_repair_date', 'equipment_category', 'reginnid', 'added_by', 'updated_by', 'order_id', 'order_number', 'note');
    protected $sortable = array('sn', 'description', 'install_date', 'internalnbr', 'last_repair_date', 'equipment_category', 'reginnid', 'order_number');

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id', 'id');
    }

    public function equipmentCategory()
    {
        return $this->belongsTo('App\Models\EquipmentCategory', 'equipment_category', 'id');
    }

    public function order()
    {
        return $this->hasMany('App\Models\Order', 'equipment_id', 'id');
    }

    public function equipmentChild()
    {
        return $this->hasMany('App\Models\EquipmentChild', 'equipment_id', 'id');
    }

    public static function getEquipment($conditions = false, $customer_id = false)
    {
        $equipment = equipment::with('order', 'customer', 'equipmentChild');
        $equipment->leftjoin('customer', 'customer.id', '=', 'equipment.customer_id');
        $equipment->leftjoin('equipment_category', 'equipment_category.id', '=', 'equipment.equipment_category');
        $equipment->addselect('equipment.*', 'customer.name as name');
        if (isset($conditions['search']) && $conditions['search'] != '') {
            if (@$conditions['search']) {
                $search = @$conditions['search'];
                $equipment->where(function ($query) use ($search) {
                    $query->orwhere('sn', 'LIKE', '%' . $search . '%');
                    $query->orwhere('description', 'LIKE', '%' . $search . '%');
                    $query->orwhere('internalnbr', 'LIKE', '%' . $search . '%');
                    $query->orwhere('install_date', 'LIKE', '%' . formatSearchDate($search) . '%');
                    $query->orwhereHas('customer', function ($query) use ($search) {
                        $query->where('name', 'LIKE', '%' . $search . '%');
                    });
                    $query->orwhereHas('equipmentCategory', function ($query) use ($search) {
                        $query->where('type', 'LIKE', '%' . $search . '%');
                    });
                });
            }
        }
        if (@$customer_id) {
            $search = @$conditions['customer_id'];
            $equipment->where(function ($query) use ($search) {
                $query->orwhere('customer_id', 'LIKE', '%' . $search . '%');
            });
        }
        $paginate_size = Session::get('paginate_size') ? Session::get('paginate_size') : 10;
        return $equipment->sortable(['sn'])->paginate($paginate_size);
    }

    /**
     * customer Sortable
     * @param  string $query
     * @param  string $direction
     * @return void
     */
    public function customerSortable($query, $direction)
    {
        $query->orderby('customer.Name', $direction);
    }

    /**
     * category Sortable
     * @param  string $query
     * @param  string $direction
     * @return void
     */
    public function categorySortable($query, $direction)
    {
        $query->orderby('equipment_category.type', $direction);
    }

    // update last_repair_date (When order is creadted and used equipment)
    public static function updateLastRepairDate($equipment_id, $date)
    {
        if ($equipment_id && $date) {
            try {
                $date = date("Y-m-d", strtotime($date));
                DB::update('update equipment SET last_repair_date="' . $date . '" WHERE id="' . $equipment_id . '"');
            } catch (Exception $e) {}
        }
    }

    /**
     * [getParentEquipments description]
     * @param  boolean $child_equipment_id [description]
     * @return [type]                      [description]
     */
    public static function getParentEquipments($parentequipemnts_array, $parent_equipment_id)
    {

        $parentequipemnts = EquipmentChild::select('child_equipment_id')->where('equipment_id', '=', $parent_equipment_id)->get();
        if (@$parentequipemnts) {
            foreach ($parentequipemnts as $key => $value) {
                $parentequipemnts_array[] = $value->child_equipment_id;
                Equipment::getParentEquipments($parentequipemnts_array, $value->child_equipment_id);
            }
        }
        return $parentequipemnts_array;
    }

    /**
     * [updateCustomerByParent description]
     * @param  [type] $parentequipemnts_array [description]
     * @param  [type] $parent_equipment_id    [description]
     * @return [type]                         [description]
     */
    public static function updateCustomerByParent($child_equipment_id, $customer_id)
    {

        Equipment::where('id', $child_equipment_id)->update(['customer_id' => $customer_id]);

        $child_equipment_ids = EquipmentChild::select('child_equipment_id')->where('equipment_id', '=', $child_equipment_id)->get();

        if (@$child_equipment_ids) {
            foreach ($child_equipment_ids as $key => $value) {
                Equipment::updateCustomerByParent($value->child_equipment_id, $customer_id);
            }
        }
    }

    /**
     * [getEquipmentDropDown description]
     * @return [type] [description]
     */
    public static function getEquipmentDropDown($customer)
    {
        try {
            if (@$customer) {
                $equipment_result = Equipment::select(DB::Raw("concat(COALESCE(internalnbr,''), IF(LENGTH(internalnbr), ' - ', ''),  COALESCE(sn,''), IF(LENGTH(sn), ' - ', ''), COALESCE(description,'') ) AS name,id"))->where('customer_id', '=', $customer)->orderby('sn', 'asc')->lists('name', 'id');
                return $equipment_result;
            }
        } catch (Exception $e) {
            echo $e;die;
        }
    }
}

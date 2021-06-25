<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Session;

class EquipmentCategory extends Model
{

    protected $table     = 'equipment_category';
    public $timestamps   = true;
    public $incrementing = false;
    use SoftDeletes;
    use Sortable;

    protected $dates    = ['deleted_at'];
    protected $fillable = array('id', 'type', 'deleted_at');
    protected $sortable = array('type');


    public function equipment()
    {
        return $this->hasMany('App\Models\Equipment', 'equipment_category', 'id');
    }

    /**
     * getEquipment Categories
     * @param  string $conditions
     * @return object
     */
    public static function getEquipmentCategories($conditions = false)
    {

        $equipmentCategory = EquipmentCategory::whereNull('deleted_at')->with('equipment');
        if (isset($conditions['search']) && $conditions['search'] != '') {
            $search = $conditions['search'];
            $equipmentCategory->where(function ($query) use ($search) {
                $query->orwhere('type', 'LIKE', '%' . $search . '%');
            });
        }
        $paginate_size = Session::get('paginate_size') ? Session::get('paginate_size') : 10;
        return $equipmentCategory->sortable(['type'])->paginate($paginate_size);
    }

    // get all equipment category (including deleted)
    public static function getAllEquipmentCategories()
    {
        $equipment_category = DB::table('equipment_category')->whereNull('deleted_at')->orWhere(function ($query) {$query->whereNotNull('deleted_at');})->orderBy('type', 'asc')->pluck('type', 'id');
        return $equipment_category;
    }
}

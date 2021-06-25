<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Session;

class ProductGroup extends Model
{
    use SoftDeletes, Sortable;
    public $incrementing = true;
    protected $table     = 'product_group';
    public $timestamps   = true;
    protected $fillable  = array('number', 'name', 'status');
    protected $sortable  = array('number', 'name');
    /**
     * [getProductGroups description]
     * @param  boolean $conditions [description]
     * @return [type]              [description]
     */
    public function getProductGroups($conditions = false)
    {
        $productGroups = ProductGroup::whereNull('deleted_at');
        if (isset($conditions['search']) && $conditions['search'] != '') {
            $search = $conditions['search'];
            $productGroups->where(function ($query) use ($search) {
                $query->orwhere('number', 'LIKE', '%' . $search . '%');
                $query->orwhere('name', 'LIKE', '%' . $search . '%');
            });
        }
        return $productGroups->sortable(['number'])->paginate(Session::get('paginate_size') ? Session::get('paginate_size') : 10);
    }

    public function createRec($input)
    {
        try {
            return ProductGroup::create($input);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateRec($input, $id)
    {
        try {
            $productGroup = ProductGroup::find($id);
            if ($productGroup) {
                $productGroup->fill($input);
                $productGroup->save();
                return true;
            }
        } catch (\Exception $e) {

        }
        return false;
    }

    public function deleteRec($id)
    {
        $productGroup = ProductGroup::find($id);
        if (!$productGroup) {
            return false;
        }
        $productGroup->delete();
        return true;
    }
}

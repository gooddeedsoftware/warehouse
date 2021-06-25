<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Session;

class GrossMargin extends Model
{
    use SoftDeletes, Sortable;
    public $incrementing = true;
    protected $table     = 'gross_margin';
    public $timestamps   = true;
    protected $fillable  = array('supplier', 'product_group', 'gross_margin');
    protected $sortable  = array('supplier', 'product_group', 'gross_margin');
    /**
     * [getGrossMargins description]
     * @param  boolean $conditions [description]
     * @return [type]              [description]
     */
    public function getGrossMargins($conditions = false)
    {
        $grossMargins = GrossMargin::select('gross_margin.*', 'customer.name as supplier_name', 'product_group.name as group_name')->whereNull('gross_margin.deleted_at');
        $grossMargins->leftjoin('customer', 'gross_margin.supplier', '=', 'customer.id');
        $grossMargins->leftjoin('product_group', 'gross_margin.product_group', '=', 'product_group.id');
        if (isset($conditions['search']) && $conditions['search'] != '') {
            $search = $conditions['search'];
            $grossMargins->where(function ($query) use ($search) {
                $query->orwhere('product_group.name', 'LIKE', '%' . $search . '%');
                $query->orwhere('customer.name', 'LIKE', '%' . $search . '%');
                $query->orwhere('gross_margin', 'LIKE', '%' . str_replace(",", ".", $search) . '%');
            });
        }
        return $grossMargins->sortable(['created_at'])->paginate(Session::get('paginate_size') ? Session::get('paginate_size') : 10);
    }

    public function supplierSortable($query, $direction)
    {
        $query->orderby('supplier_name', $direction);
    }
    /**
     * [customerSortable description]
     * @param  [type] $query     [description]
     * @param  [type] $direction [description]
     * @return [type]            [description]
     */
    public function product_groupSortable($query, $direction)
    {
        $query->orderby('group_name', $direction);
    }

    public function createRec($input)
    {
        try {
            $input['gross_margin'] = isset($input['gross_margin']) ? str_replace(",", ".", $input['gross_margin']) : "";
            return GrossMargin::create($input);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateRec($input, $id)
    {
        try {
            $grossMargin = GrossMargin::find($id);
            if ($grossMargin) {
                $grossMargin->fill($input);
                $grossMargin->save();
                return true;
            }
        } catch (\Exception $e) {

        }
        return false;
    }

    public function deleteRec($id)
    {
        $grossMargin = GrossMargin::find($id);
        if (!$grossMargin) {
            return false;
        }
        $grossMargin->delete();
        return true;
    }
}

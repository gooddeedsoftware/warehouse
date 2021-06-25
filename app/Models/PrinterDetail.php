<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Session;

class PrinterDetail extends Model
{

    protected $table   = 'printer_detail';
    public $timestamps = true;
    use SoftDeletes;
    use Sortable;
    protected $fillable = array('id', 'name', 'number');
    
    /**
     * [getRecs description]
     * @param  boolean $conditions [description]
     * @return [type]              [description]
     */
    public static function getRecs($conditions = false)
    {
        $country = PrinterDetail::whereNotNull('name');
        if (isset($conditions['search']) && $conditions['search'] != '') {
            $search = $conditions['search'];
            $country->where(function ($query) use ($search) {
                $query->orwhere('name', 'LIKE', '%' . $search . '%');
                $query->orwhere('number', 'LIKE', '%' . $search . '%');
            });
        }
        $paginate_size = Session::get('paginate_size') ? Session::get('paginate_size') : 10;
        return $country->sortable(['type'])->paginate($paginate_size);
    }
}

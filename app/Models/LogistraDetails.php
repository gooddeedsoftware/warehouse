<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Session;

class LogistraDetails extends Model
{

    protected $table   = 'logistra_details';
    public $timestamps = true;
    use SoftDeletes;
    use Sortable;
    protected $fillable = array('id', 'name', 'cargonizer_key', 'cargonizer_sender', 'status');
    public static function getRecs($conditions = false)
    {
        $country = LogistraDetails::whereNotNull('name');
        if (isset($conditions['search']) && $conditions['search'] != '') {
            $search = $conditions['search'];
            $country->where(function ($query) use ($search) {
                $query->orwhere('name', 'LIKE', '%' . $search . '%');
                $query->orwhere('cargonizer_key', 'LIKE', '%' . $search . '%');
                $query->orwhere('cargonizer_sender', 'LIKE', '%' . $search . '%');
            });
        }
        $paginate_size = Session::get('paginate_size') ? Session::get('paginate_size') : 10;
        return $country->sortable(['type'])->paginate($paginate_size);
    }
}

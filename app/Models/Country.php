<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Session;

class Country extends Model
{

    protected $table     = 'country';
    public $timestamps   = false;
    public $incrementing = false;
    use Sortable;

    protected $fillable = array('id', 'name', 'code');

    /**
     * getEquipment Categories
     * @param  string $conditions
     * @return object
     */
    public static function getCountries($conditions = false)
    {
        $country = Country::whereNotNull('name');
        if (isset($conditions['search']) && $conditions['search'] != '') {
            $search = $conditions['search'];
            $country->where(function ($query) use ($search) {
                $query->orwhere('name', 'LIKE', '%' . $search . '%');
            });
        }
        $paginate_size = Session::get('paginate_size') ? Session::get('paginate_size') : 10;
        return $country->sortable(['type'])->paginate($paginate_size);
    }

}

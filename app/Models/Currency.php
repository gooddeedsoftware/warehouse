<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Session;

class Currency extends Model
{

    protected $table     = 'currency';
    public $timestamps   = true;
    public $incrementing = false;
    use SoftDeletes, Sortable;
    protected $dates    = ['deleted_at'];
    protected $fillable = array('id', 'curr_iso_name', 'exch_rate', 'valid_from', 'deleted_at', 'added_by', 'updated_by');

    // list currencies
    public static function getCurrencies($conditions = false, $orderby = 'valid_from', $order = 'desc')
    {
        $currency = Currency::whereNull('deleted_at');
        $currency->leftjoin('dropdown_helper', 'currency.curr_iso_name', '=', 'dropdown_helper.key_code')->where('dropdown_helper.group_code', '=', '015');
        if (isset($conditions['search']) && $conditions['search'] != '') {
            $search = $conditions['search'];
            $currency->Where(function ($query) use ($search) {
                $query->orwhere('currency.exch_rate', 'LIKE', '%' . str_replace(",", ".", $search) . '%');
                $query->orwhere('currency.valid_from', 'LIKE', '%' . formatSearchDate($search) . '%');
                $query->orwhere('dropdown_helper.value_en', 'LIKE', '%' . $search . '%');
                $query->orwhere('dropdown_helper.value_no', 'LIKE', '%' . $search . '%');
            });
        }
        $paginate_size = Session::get('paginate_size') ? Session::get('paginate_size') : 10;
        return $currency->sortable(['curr_iso_name'])->paginate($paginate_size);
    }

    // get currency details from fields
    public static function getCurrencyDetails($data = false)
    {
        try {
            if ($data) {
                $currencyDetails = Currency::orderBy('curr_iso_name', 'asc');
                $data            = json_decode($data);
                $currency_name   = "NOK";
                foreach ($data as $key => $value) {
                    $currencyDetails->where($value->name, '=', $value->value);
                    if ($value->name == "curr_iso_name") {
                        $currency_name = $value->value;
                    }
                }
                $currencyDetails->whereRaw('valid_from = (SELECT max(valid_from) FROM currency WHERE curr_iso_name="' . $currency_name . '") ');
                $currencyDetails = $currencyDetails->first();
                return $currencyDetails;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    // get latest crrency details
    public static function getRecentCurrencyDetails()
    {
        try {
            $currencyDetails = Currency::whereRaw('valid_from IN (SELECT max(valid_from) FROM currency GROUP BY curr_iso_name)')->groupBy('curr_iso_name')->get();
            return $currencyDetails;
        } catch (Exception $e) {

        }
    }
}

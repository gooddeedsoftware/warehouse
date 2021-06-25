<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;

class Company extends Model
{
    protected $table     = 'company';
    public $incrementing = false;
    public $timestamps   = true;
    protected $fillable  = array('id', 'name', 'IBAN', 'BIC', 'account_number', 'company_information', 'company_email', 'company_VAT', 'post_address', 'phone', 'fax', 'deleted_at', 'zip', 'city', 'visma_clientnumber', 'web_page', 'country');

    public static function getCompanyDetails($conditions = false, $orderby = 'name', $order = 'asc')
    {
        $company = Company::orderBy($orderby, $order);
        if (isset($conditions['search']) && $conditions['search'] != '') {
            $search = $conditions['search'];
            $company->where(function ($query) use ($search) {
                $query->orwhere('name', 'LIKE', '%' . $search . '%');
                $query->orwhere('IBAN', 'LIKE', '%' . $search . '%');
                $query->orwhere('phone', 'LIKE', '%' . $search . '%');
                $query->orwhere('BIC', 'LIKE', '%' . $search . '%');
            });
        }
        $paginate_size = Session::get('paginate_size') ? Session::get('paginate_size') : 10;
        return $company->paginate($paginate_size);
    }

    // get only one department details
    public static function getCompanyDetail($id)
    {
        $company_details = Company::where('id', "=", $id)->get();
        return $company_details;
    }

}

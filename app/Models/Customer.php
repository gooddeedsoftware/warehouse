<?php
namespace App\Models;

use App\Helpers\GanticHelper;
use App\Models\Country;
use App\Models\Equipment;
use App\Models\UNICustomers;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Session;

class Customer extends Model
{
    use Sortable;
    protected $table     = 'customer';
    public $incrementing = false;
    public $timestamps   = true;
    use SoftDeletes;
    protected $fillable = array('id', 'customer', 'supplier', 'name', 'shortname', 'VAT', 'address1', 'address2', 'city', 'zip', 'phone', 'fax', 'email', 'country_id', 'cdeliverymtd', 'sdeliverymtd', 'cdeltrm', 'sdeltrm', 'bankaccount', 'currency', 'percentage', 'cpaymentcond', 'spaymentcond', 'cpaymentmtd', 'spaymentmtd', 'web', 'creditlimit', 'cmainbook', 'smainbook', 'status', 'deleted_at', 'percentage_other', 'is_supplier', 'pmt_terms', 'invoicing', 'added_by', 'updated_by', 'financing_company', 'customer_note', 'uni_id', 'language');

    public $sortable = ['customer', 'name', 'email', 'address1', 'zip', 'city', 'phone', 'pmt_terms', 'customer_address'];

    public function contact()
    {
        return $this->hasMany('App\Models\Contact', 'customer_id', 'id');
    }

    public function address()
    {
        return $this->hasMany('App\Models\CustomerAddress', 'customer_id', 'id');
    }

    public function user()
    {
        return $this->hasMany('App\Models\User');
    }

    public function order()
    {
        return $this->hasMany('App\Models\Order', 'customer_id', 'id');
    }

    public function invoice()
    {
        return $this->hasMany('App\Models\Invoice');
    }

    public function departmentWithMainCustomerAddress()
    {
        return $this->hasMany('App\Models\CustomerAddress')->where('main', '=', 1)->where('type', '=', 1)->orderBy('address1', 'asc');
    }

    public function departmentCustomerAddress()
    {
        return $this->hasMany('App\Models\CustomerAddress')->where('type', '=', 1)->orderBy('address1', 'asc');
    }

    public function invoiceCustomerAddress()
    {
        return $this->hasMany('App\Models\CustomerAddress')->where('type', '=', 2);
    }

    public function shippingCustomerAddress()
    {
        return $this->hasMany('App\Models\CustomerAddress')->where('type', '=', 3);
    }

    public function mainCustomerAddress()
    {
        return $this->hasMany('App\Models\CustomerAddress')->where('main', '=', 1);
    }

    /**
     * getCustomersDetails
     * @param  boolean $conditions
     * @param  string  $orderby
     * @param  string  $order
     * @return object
     */
    public static function getCustomersDetails($conditions = false, $orderby = 'name', $order = 'asc', $is_supplier = false)
    {

        $customer = Customer::select('customer.*')->selectRaw('customer_address.address1 as department_address, customer_address.zip as department_zip, customer_address.city as department_city');
        $customer->leftjoin('customer_address', function ($query) {
            $query->on('customer_address.customer_id', '=', 'customer.id');
            $query->where('main', '=', 1);
            $query->where('customer_address.address1', '!=', '');
        });

        if (isset($conditions['search']) && $conditions['search'] != '') {
            $search = $conditions['search'];
            $customer->where(function ($query) use ($search) {
                $query->orwhere('act_no', 'LIKE', '%' . $search . '%');
                $query->orwhere('customer', 'LIKE', '%' . $search . '%');
                $query->orwhere('name', 'LIKE', '%' . $search . '%');
                $query->orwhere('customer.address1', 'LIKE', '%' . $search . '%');
                $query->orwhere('customer.city', 'LIKE', '%' . $search . '%');
                $query->orwhere('customer.zip', 'LIKE', '%' . $search . '%');
                $query->orwhere('phone', 'LIKE', '%' . $search . '%');
                $query->orwhere('email', 'LIKE', '%' . $search . '%');
            });
        }

        if (isset($conditions['status']) && $conditions['status'] != '') {
            $search = $conditions['status'];
            $customer->where(function ($query) use ($search) {
                $query->orwhere('customer.status', '=', $search);
            });
        }
        if ($is_supplier == 1) {
            $customer->where('is_supplier', 1);
        }
        $customer->groupBy('customer.id');
        $paginate_size = Session::get('paginate_size') ? Session::get('paginate_size') : 10;
        return $customer->sortable(['name'])->paginate($paginate_size);
    }

    /**
     * zipSortable
     * @param  string $query
     * @param  string $direction
     * @return void
     */
    public function zipSortable($query, $direction)
    {
        $query->orderby('customer_address.zip', $direction);
    }

    /**
     * citySortable
     * @param  string $query
     * @param  string $direction
     * @return void
     */
    public function citySortable($query, $direction)
    {
        $query->orderby('customer_address.city', $direction);
    }

    /**
     * zip Sortable
     * @param  string $query
     * @param  string $direction
     * @return void
     */
    public function departmentAddressSortable($query, $direction)
    {
        $query->orderby('customer_address.address1', $direction);
    }

    /**
     * get max CustomerNumber
     * @return integer
     */
    public static function getCustomerNumber()
    {
        $act_no = "11300";
        $result = Customer::select(DB::raw("MAX(CAST(customer as UNSIGNED))  as customer"))->first();
        if (isset($result->customer) && $result->customer >= 11300) {
            $customer = $result->customer;
            $customer = sprintf('%05d', $customer + 1);
        } else {
            $customer = '11300';
        }

        return $customer;
    }

    // get customer detail with invoice address
    public static function getCustomerDetail($id)
    {
        $customer_name = Customer::where('id', '=', $id)->with('invoiceCustomerAddress', 'departmentCustomerAddress', 'mainCustomerAddress')->first();
        /*dd($customer_name);*/
        return $customer_name;
    }

    // get customer details
    public static function getCustomer($id)
    {
        $customer_name = Customer::where('id', '=', $id)->first();
        return $customer_name;
    }

    // get order details from customer
    public static function getOrderDetailsFromCustomerID($customer_id)
    {
        $order_details = Order::where('customer_id', "=", $customer_id)->first();
        return $order_details;
    }

    /**
     * getEquipmentFromCustomerID
     * @param  String $customer_id
     * @return object
     */
    public static function getEquipmentFromCustomerID($customer_id = false)
    {
        $equipment_details = Equipment::where('customer_id', '=', $customer_id)->first();
        return $equipment_details;
    }

    /**
     * [getCustomers description]
     * @return [type] [description]
     */
    public static function getCustomers()
    {
        try {
            return Customer::where('status', '=', '0')->orderby('name', 'asc')->pluck('name', 'id');
        } catch (Exception $e) {
            echo $e;die;
        }
    }

    /**
     * [createOrEdit description]
     * @return Object
     */
    public static function createOrEdit($id = false)
    {
        $data['customer']            = new Customer();
        $data['customer']->pmt_terms = "14";
        $language                    = Session::get('language') ? Session::get('language') : 'no';
        $data['pmt_terms']           = DropdownHelper::where('language', $language)->where('groupcode', '009')->orderby('keycode', 'asc')->pluck('label', 'keycode');
        if ($id) {
            $data['customer']              = Customer::where('id', $id)->first();
            $data['customer']->creditlimit = str_replace(".", ",", $data['customer']->creditlimit ? $data['customer']->creditlimit + 0 : null);
            $data['has_orders']            = Customer::getOrderDetailsFromCustomerID($id) ? true : false;
            $data['has_equipment']         = Customer::getEquipmentFromCustomerID($id) ? true : false;
            $data['uni_customers'] = UNICustomers::selectRaw('concat(if(customer_number is null, "", concat(customer_number, " - ")) ,if(name is null, "", name)) as name, uni_id')->orderBy('customer_number', 'asc')->pluck('name', 'uni_id');
        }
        $data['customer_address_types'] = CustomerAddress::getCustomerAddressTypes($id);
        $data['countries']              = Country::pluck('name', 'id');
        $data['currency_list']          = DropdownHelper::where('language', $language)->where('groupcode', '015')->orderBy('keycode', 'asc')->pluck('label', 'keycode')->toArray();
        return $data;
    }

    /**
     * [fetchCustomers description]
     * @param  [type] $searchval [description]
     * @return [type]            [description]
     */
    public static function fetchCustomers($searchval)
    {
        $searchval = urlencode($searchval);
        if ($searchval) {
            $curl = curl_init();
            $url  = "https://data.brreg.no/enhetsregisteret/api/enheter?navn=" . $searchval . "&size=1000";
            curl_setopt_array($curl, array(
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_ENCODING       => "",
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => "GET",
                CURLOPT_POSTFIELDS     => "",
            ));
            $response = curl_exec($curl);
            $err      = curl_error($curl);
            curl_close($curl);
            if ($err) {
                echo $err;
            } else {
                $result = json_decode($response);
                if (@$result->_embedded && @$result->_embedded->enheter) {
                    return json_encode($result->_embedded->enheter);
                }
            }
        }
        return [];
    }

    /**
     * [createOrUpdateCustomer description]
     * @param  [type]  $input [description]
     * @param  boolean $id    [description]
     * @return [type]         [description]
     */
    public static function createOrUpdateCustomer($input, $id = false)
    {
        $input['customer']    = Customer::getCustomerNumber();
        $input['is_supplier'] = @$input['is_supplier'] ? 1 : 0;
        $input['creditlimit'] = @$input['creditlimit'] != "" ? str_replace(",", ".", $input['creditlimit']) : 0.00;
        if ($id) {
            $input['updated_by'] = Session::get('currentUserID');
            $customer            = Customer::find($id);
            $customer->fill($input);
            $customer->save();
        } else {
            $input['id']       = GanticHelper::gen_uuid();
            $input['added_by'] = Session::get('currentUserID');
            $customer          = Customer::create($input);
            CustomerAddress::storeAddress($customer['id'], $customer['VAT']);
        }
        return $customer;
    }

    /**
     * [getUNICustomers description]
     * @param  boolean $vat [description]
     * @return [type]       [description]
     */
    public static function getUNICustomers($vat = false)
    {
        $customers        = UNICustomers::get();
        $dropdown_options = '<option value="">' . __('main.selected') . '</option>';
        $i = 0;
        foreach ($customers as $key => $value) {
            if ($i == 0 && $value->org_number == $vat) {
                $dropdown_options .= '<option value=' . $value->uni_id . ' selected="selected">' . $value->customer_number . ' - ' . $value->name . '</option>';
                $i++;
            } else {
                $dropdown_options .= '<option value=' . $value->uni_id . '>' . $value->customer_number . ' - ' . $value->name . '</option>';
            }
        }
        return json_encode(array("dropdown_options" => $dropdown_options));
    }
}

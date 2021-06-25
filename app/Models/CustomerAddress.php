<?php

namespace App\Models;

use App\Helpers\GanticHelper;
use App\Models\DropdownHelper;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Session;

class CustomerAddress extends Model
{
    protected $table     = 'customer_address';
    public $incrementing = false;
    public $timestamps   = true;
    use SoftDeletes;
    protected $fillable = array('id', 'customer_id', 'type', 'main', 'address1', 'address2', 'zip', 'city', 'country', 'deleted_at', 'added_by', 'updated_by');

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Country');
    }

    public static function getCustomerAddressDetails($conditions = false, $orderby = 'updated_at', $order = 'asc', $id = false)
    {
        $customerAddress = CustomerAddress::leftjoin('customer', 'customer.id', '=', 'customer_address.customer_id');
        $customerAddress->select('customer.name as customerName', 'customer_address.*');
        $customerAddress->orderBy($orderby, $order);
        if ($id) {
            $customerAddress->where('customer_address.customer_id', '=', $id);
        }
        $customerAddress       = $customerAddress->get();
        $customerAddress->main = false;
        for ($i = 0; $i < count($customerAddress); $i++) {
            if ($customerAddress[$i]->main == "1") {
                $customerAddress->main = true;
            }
        }
        return $customerAddress;
    }

    // get CustomerAddress person details
    public static function getCustomerAddressPerson($id)
    {
        $CustomerAddress_person_details = CustomerAddress::where('id', "=", $id)->get();
        return $CustomerAddress_person_details;
    }

    // update main address
    public static function updateMainAddress($id = false, $customer_id = false)
    {
        if ($id && $customer_id) {
            DB::table('customer_address')->where('customer_id', '=', $customer_id)->update(['main' => 0]);
            DB::table('customer_address')->where('id', '=', $id)->update(['main' => 1]);
            DB::table('customer_address')->where('id', '=', $id)->update(['updated_by' => Session::get('currentUserID')]);
            return true;
        } else {
            return false;
        }
    }

    // delete CustomerAddress using customer id
    public static function deleteCustomerAddressUsingCustomerID($customer_id)
    {
        if ($customer_id) {
            try {
                DB::update('update `customer_address` set deleted_at="' . date("Y-m-d h:i:s") . '" where customer_id="' . $customer_id . '"');
            } catch (Exception $e) {
            }
        }
    }

    // get customer address
    public static function getCustomerAddresstypes($customer_id)
    {
        try {
            // $customer_address_details = CustomerAddress::where('customer_id', '=', $customer_id)->get();
            $language                 = Session::get('language') ? Session::get('language') : 'no';
            // if ($customer_address_details) {
            //     $has_department_address = false;
            //     $has_invoice_address    = false;
            //     foreach ($customer_address_details as $key => $value) {
            //         if ($value->type == 1) {
            //             $has_department_address = true;
            //         } else if ($value->type == 2) {
            //             $has_invoice_address = true;
            //         }
            //     }
            //     return DropdownHelper::where('language', $language)->where('groupcode', '017')->where(function ($query) use ($has_department_address, $has_invoice_address) {
            //     })->orderby('keycode', 'asc')->pluck('label', 'keycode');
            // } else {
                return DropdownHelper::where('language', $language)->where('groupcode', '017')->orderby('keycode', 'asc')->pluck('label', 'keycode');
            // }
        } catch (Exception $e) {

        }
    }

    public static function storeAddress($id, $vat)
    {
        $curl = curl_init();
        if ($vat) {
            curl_setopt_array($curl, array(
                CURLOPT_URL            => "https://data.brreg.no/enhetsregisteret/api/enheter/" . $vat,
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
            if (!$err) {
                $result = json_decode($response);
                if (@$result->forretningsadresse) {
                    $input                = [];
                    $input['type']        = 2;
                    $input['id']          = GanticHelper::gen_uuid();
                    $input['address1']    = @$result->forretningsadresse->adresse[0];
                    $input['address2']    = @$result->forretningsadresse->adresse[1];
                    $input['city']        = @$result->forretningsadresse->poststed;
                    $input['zip']         = @$result->forretningsadresse->postnummer;
                    $input['customer_id'] = $id;
                    CustomerAddress::create($input);
                }
            }
        }

    }
}

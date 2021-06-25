<?php

namespace App\Http\Controllers;

use App\Helpers\GanticHelper;
use App\Models\Country;
use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Redirect;
use Session;

class CustomerAddressController extends Controller
{
    protected $customer_view_route = 'main.customer.edit';
    protected $success             = 'success';
    protected $error               = 'error';
    protected $notfound            = 'main.notfound';
    protected $createmsg           = 'main.customer_address_createsuccess';
    protected $updatemsg           = 'main.customer_address_updatesuccess';
    protected $deletemsg           = 'main.customer_address_deletesuccess';

    public function loadCustomerAddressView()
    {
        $data = @\Request::input();
        if ($data['id']) {
            $data['customerAddress'] = CustomerAddress::where('id', $data['id'])->first();
        }
        $data['countries']              = Country::pluck('name', 'id');
        $data['customer_address_types'] = CustomerAddress::getCustomerAddressTypes($data['customer_id']);
        return view('customer/customer_address/quickCreate', $data);
    }

    /**
     * Creates an or update customerAddress.
     *
     * @param      \Illuminate\Http\Request  $request  The request
     *
     * @return     <type>                    ( description_of_the_return_value )
     */
    public function createOrUpdateCustomerAddress(Request $request)
    {
        $input             = $request->all();
        $input['added_by'] = Session::get('currentUserID');
        if (@$input['id']) {
            $customerAddress     = CustomerAddress::find($input['id']);
            $input['updated_by'] = Session::get('currentUserID');
            $customerAddress->fill($input);
            $customerAddress->save();
            return Redirect::route($this->customer_view_route, $input['customer_id'])->with($this->success, __($this->updatemsg));
        } else {
            $input['id']     = GanticHelper::gen_uuid();
            $customerAddress = CustomerAddress::create($input);
            return Redirect::route($this->customer_view_route, $input['customer_id'])->with($this->success, __($this->createmsg));
        }

    }

    /**
     * Deletes the given identifier.
     *
     * @param      <type>  $id     The identifier
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function delete($id)
    {
        $customerAddress = CustomerAddress::find($id);
        if (!$customerAddress) {
            return Redirect::back()->with($this->error, __($this->notfound));
        }
        $customerAddress->delete();
        return Redirect::back()->with($this->success, __($this->deletemsg));
    }

    /**
     * { function_description }
     *
     * @param      boolean  $id           The identifier
     * @param      boolean  $customer_id  The customer identifier
     */
    public function updateMainAddress($id = false, $customer_id = false)
    {
        if ($id) {
            $result = CustomerAddress::updateMainAddress($id, $customer_id);
            if ($result) {
                echo json_encode(array("result" => "success"));
            } else {
                echo json_encode(array("result" => "fail"));
            }
        }
    }

    /**
     * [getCustomerAddressDetails description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getCustomerAddressDetails($id)
    {
        if ($id) {
            $customer_address = CustomerAddress::where('id', '=', $id)->first();
            echo json_encode(array("data" => $customer_address, "result" => "success"));
        } else {
            echo json_encode(array("result" => "fail"));
        }
    }

}

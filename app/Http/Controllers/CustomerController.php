<?php
namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\UniIntegration;
use Redirect;
use Session;

class CustomerController extends Controller
{
    protected $route             = 'main.customer.index';
    protected $supplierRoute     = 'main.supplier.index';
    protected $success           = 'success';
    protected $error             = 'error';
    protected $notfound          = 'main.notfound';
    protected $createmsg         = 'main.customer_createsuccess';
    protected $updatemsg         = 'main.customer_updatesuccess';
    protected $deletemsg         = 'main.customer_deletesuccess';
    protected $suppliercreatemsg = 'main.supplier_createsuccess';
    protected $supplierupdatemsg = 'main.supplier_updatesuccess';
    protected $supplierdeletemsg = 'main.supplier_deletesuccess';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data = @Session::get('customer_search') ? @Session::get('customer_search') : [];
        @\Request::input() ? Session::put('customer_search', array_merge($data, @\Request::input())) : '';
        $data['customers'] = Customer::getCustomersDetails(Session::get('customer_search'));
        return view('customer.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $data = Customer::createOrEdit();
        return view('customer.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(CustomerRequest $request)
    {

        $input  = $request->all();
        $result = Customer::createOrUpdateCustomer($input);
        if ($input['customer_submit_btn'] == 'close') {
            if ($input['createSupplier'] == 1) {
                return Redirect::route($this->supplierRoute)->with($this->success, __($this->suppliercreatemsg));
            }
            return Redirect::route($this->route)->with($this->success, __($this->createmsg));
        } else {
            if ($input['createSupplier'] == 1) {
                return Redirect::route('main.supplier.edit', $result['id'])->with($this->success, __($this->suppliercreatemsg));
            }
            return Redirect::route('main.customer.edit', $result['id'])->with($this->success, __($this->createmsg));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = Customer::createOrEdit($id);
        return view('customer.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(CustomerRequest $request, $id)
    {
        $input  = $request->all();
        $result = Customer::createOrUpdateCustomer($input, $id);
        if ($input['customer_submit_btn'] == 'close') {
            if ($input['createSupplier'] == 1) {
                return Redirect::route($this->supplierRoute)->with($this->success, __($this->supplierupdatemsg));
            }
            return Redirect::route($this->route)->with($this->success, __($this->updatemsg));
        } else {
            if ($input['createSupplier'] == 1) {
                return Redirect::route('main.supplier.edit', $id)->with($this->success, __($this->supplierupdatemsg));
            }
            return Redirect::route('main.customer.edit', $id)->with($this->success, __($this->updatemsg));
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return Redirect::route($this->route)->with($this->error, __($this->notfound));
        }
        $customer->delete();
        Contact::deleteContactUsingCustomerID($id);
        return Redirect::route($this->route)->with($this->success, __($this->deletemsg));
    }

    /**
     * [getCustomers description]
     * @return [type] [description]
     */
    public function getCustomers()
    {
        return Customer::fetchCustomers(request('searchValue'));
    }

    /**
     * [getCustomersFromUni description]
     * @param  boolean $vat [description]
     * @return [type]       [description]
     */
    public function getCustomersFromUni($vat = false)
    {
        return Customer::getUNICustomers($vat);
    }

    /**
     * [syncCustomers description]
     * @return [type] [description]
     */
    public function syncCustomers()
    {
        $result = UniIntegration::fetchUniCustomers();
        switch ($result) {
            case 0:
                return Redirect::route($this->route)->with($this->error, __('main.something_went_wrong'));
            case 1:
                return Redirect::route($this->route)->with($this->success, __('main.sync_success'));
            case 2:
                return redirect()->away(config('app.UNI_CODE_URL'));
        }
    }

}

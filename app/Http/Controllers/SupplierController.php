<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use Session;

class SupplierController extends Controller
{
    protected $route     = 'main.supplier.index';
    protected $success   = 'success';
    protected $error     = 'error';
    protected $notfound  = 'main.notfound';
    protected $createmsg = 'main.supplier_createsuccess';
    protected $updatemsg = 'main.supplier_updatesuccess';
    protected $deletemsg = 'main.supplier_deletesuccess';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data = @Session::get('supplier_search') ? @Session::get('supplier_search') : [];
        @\Request::input() ? Session::put('supplier_search', array_merge($data, @\Request::input())) : '';
        $data['suppliers'] = Customer::getCustomersDetails(Session::get('supplier_search'), false, false, 1);
        return view('supplier.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $data                   = Customer::createOrEdit();
        $data['createSupplier'] = 1;
        return view('customer.form', $data);
    }

    public function edit($id)
    {
        $data                   = Customer::createOrEdit($id);
        $data['createSupplier'] = 1;
        return view('customer.edit', $data);
    }

    public function getSupplierCurrency($id)
    {   
        $currency_details = Customer::whereId($id)->select('currency')->first();
        return json_encode(array('currency_details' => $currency_details));
    }
}

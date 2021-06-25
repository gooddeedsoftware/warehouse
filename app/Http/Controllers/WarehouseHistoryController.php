<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Location;
use App\Models\Order;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseOrder;
use App\Models\WhsHistory;
use Illuminate\Http\Request;
use Session;

class WarehouseHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = @Session::get('warehouse_history_search') ? @Session::get('warehouse_history_search') : [];
        @\Request::input() ? Session::put('warehouse_history_search', array_merge($data, @\Request::input())) : '';
        $data['products']    = WhsHistory::getHistoryProdcuts(@Session::get('warehouse_history_search'));
        $data['users']       = User::getUsersDropDown();
        $data['order_types'] = ['1' => trans('main.transfer_order'), '2' => trans('main.adjustment_order'), '3' => trans('main.supplier_order'), '4' => trans('main.return_order'), '5' => trans('main.customer_order')];
        $data['warehouses']  = Warehouse::withTrashed()->pluck('shortname', 'id');
        $data['locations']   = Location::withTrashed()->orderby('name', 'asc')->pluck('name', 'id');
        $data['customers']   = Customer::withTrashed()->pluck('name', 'id');
        $data['whs_orders']  = WarehouseOrder::withTrashed()->pluck('order_number', 'id');
        $data['sale_orders'] = Order::withTrashed()->pluck('order_number', 'id');
        $data['suppliers']   = Customer::withTrashed()->pluck('customer', 'id');
        return view('warehousedetails/history/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

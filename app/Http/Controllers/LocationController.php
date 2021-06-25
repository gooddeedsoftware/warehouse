<?php

namespace App\Http\Controllers;

use App\Helpers\GanticHelper;
use App\Http\Requests\LocationRequest;
use App\Models\DropdownHelper;
use App\Models\Location;
use App\Models\ProductLocation;
use App\Models\Warehouse;
use Redirect;
use Request;
use Session;

class LocationController extends Controller
{
    protected $route     = 'main.location.index';
    protected $success   = 'success';
    protected $error     = 'error';
    protected $notfound  = 'main.notfound';
    protected $createmsg = 'main.location_createsuccess';
    protected $updatemsg = 'main.location_updatesuccess';
    protected $deletemsg = 'main.location_deletesuccess';
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data = @Session::get('location_search') ? @Session::get('location_search') : [];
        @\Request::input() ? Session::put('location_search', array_merge($data, @\Request::input())) : '';
        $language                       = Session::get('language') ? Session::get('language') : 'no';
        $data['yesorno_language_array'] = DropdownHelper::where('language', $language)->where('groupcode', '002')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        $data['locations']              = Location::getLocations(@Session::get('location_search'));
        return view('location.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $data['location']               = new Location();
        $language                       = Session::get('language') ? Session::get('language') : 'no';
        $data['yesorno_language_array'] = DropdownHelper::where('language', $language)->where('groupcode', '002')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        $data['warehouses']             = Warehouse::orderBy("shortname", "asc")->pluck("shortname", "id");
        return view('location.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(LocationRequest $request)
    {
        $input             = $request->all();
        $input['id']       = GanticHelper::gen_uuid();
        $input['added_by'] = Session::get('currentUserID');
        Location::create($input);
        return Redirect::route($this->route)->with($this->success, __($this->createmsg));
    }

    public function show($id)
    {
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data['location']               = Location::findorFail($id);
        $language                       = Session::get('language') ? Session::get('language') : 'no';
        $data['yesorno_language_array'] = DropdownHelper::where('language', $language)->where('groupcode', '002')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        $data['warehouses']             = Warehouse::orderBy("shortname", "asc")->pluck("shortname", "id");
        return view('location.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(LocationRequest $request, $id)
    {
        $location            = Location::findOrFail($id);
        $input               = $request->all();
        $input['updated_by'] = Session::get('currentUserID');
        $location->fill($input);
        $location->save();
        return Redirect::route($this->route)->with($this->success, __($this->updatemsg));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $location = Location::where('id', '=', $id)->first();
        if (!$location) {
            return Redirect::route($this->route)->with($this->error, __($this->notfound));
        }
        $location->delete();
        return Redirect::route($this->route)->with($this->success, __($this->deletemsg));
    }
    /**
     * [getLocationsByWarehouse description]
     * @param  [type] $warehouse_id [description]
     * @return [type]               [description]
     */
    public function getLocationsByWarehouse($warehouse_id)
    {
        try {
            $location_details = '';
            $input            = @Request::input();
            if (@$warehouse_id && $input['product_id']) {
                $product_location_details = ProductLocation::where('product_id', $input['product_id'])->where('warehouse_id', $warehouse_id)->pluck('location_id');
                $location_options         = "";
                if (count($product_location_details) > 0) {
                    $location_options .= '<option selected id="">' . __('main.selected') . '</option>';
                    $location_details = Location::whereIn('id', $product_location_details)->get();
                    foreach ($location_details as $key => $value) {
                        if (count($location_details) == 1) {
                            $location_options .= '<option id=' . $value->id . ' selected=selected value=' . $value->name . '>' . $value->name . '</option>';
                        } else {
                            $location_options .= '<option id=' . $value->id . ' value=' . $value->name . '>' . $value->name . '</option>';
                        }
                    }
                } else {
                    $location_details = Location::whereName('Undefined')->where('warehouse_id', $warehouse_id)->first();
                    $location_options .= '<option id="' . $location_details->id . '" value="' . $location_details->name . '" selected=selected>' . $location_details->name . '</option>';
                }
            }
            return json_encode(array("status" => "success", 'location_details' => $location_options));
        } catch (\Exception $e) {
            return json_encode(array('location_details' => '', "status" => "error"));
        }
    }

    /**
     * [getLocationsByWarehouseForReturnOrder description]
     * @param  [type] $warehouse_id [description]
     * @return [type]               [description]
     */
    public function getLocationsByWarehouseForReturnOrder($warehouse_id)
    {
        try {
            $type             = Request::get('type');
            $location_details = '';
            if (@$warehouse_id) {
                $location_details = Location::where('warehouse_id', '=', $warehouse_id);
                if ($type == 1) {
                    $location_details->where('name', '!=', 'Undefined');
                }
                $location_details = $location_details->get();
                $location_options = "";
                $location_options .= '<option selected="selected" value="">' . __('main.selected') . '</option>';
                foreach ($location_details as $key => $value) {
                    $location_options .= '<option id=' . $value->id . ' value=' . $value->id . '>' . $value->name . '</option>';
                }
            }
            return json_encode(array("status" => "success", 'location_details' => $location_options));
        } catch (\Exception $e) {
            return json_encode(array('location_details' => '', "status" => "error"));
        }
    }

    public function checkLocationByWarehouse()
    {
        try {
            $data            = Request::all();
            $location_detail = Location::where('warehouse_id', '=', @$data['warhouse_id'])->where('name', '=', @$data['location'])->first();
            if ($location_detail) {
                return json_encode(array('location_value' => 1, "status" => "success"));
            } else {
                return json_encode(array('location_value' => 3, "status" => "success"));
            }
        } catch (\Exception $e) {
            return json_encode(array('location_value' => 0, "status" => "error"));
        }
    }
}

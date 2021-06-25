<?php

namespace App\Http\Controllers;

use App\Helpers\GanticHelper;
use App\Http\Requests\WarehouseRequest;
use App\Models\DropdownHelper;
use App\Models\Location;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseResponsible;
use DB;
use Input;
use Redirect;
use Session;

class WarehouseController extends Controller
{

    protected $route     = 'main.warehouse.index';
    protected $success   = 'success';
    protected $error     = 'error';
    protected $notfound  = 'main.notfound';
    protected $createmsg = 'main.warehouse_createsuccess';
    protected $updatemsg = 'main.warehouse_updatesuccess';
    protected $deletemsg = 'main.warehouse_deletesuccess';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

        $data = @Session::get('warehouse_search') ? @Session::get('warehouse_search') : [];
        @\Request::input() ? Session::put('warehouse_search', array_merge($data, @\Request::input())) : '';
        $language                    = Session::get('language') ? Session::get('language') : 'no';
        $data['warehousemain_array'] = DropdownHelper::where('language', $language)->where('groupcode', '002')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        $data['warehouses']          = Warehouse::getWarehouseDetails(@Session::get('warehouse_search'));
        $data['users']               = User::select(DB::Raw("concat(first_name ,' ', last_name) AS name, id"))->where('activated', '=', 0)->orderBy('first_name', 'asc')->pluck('name', 'id');
        return view('warehouse.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $data['warehouse']           = new Warehouse();
        $language                    = Session::get('language') ? Session::get('language') : 'no';
        $data['warehousemain_array'] = DropdownHelper::where('language', $language)->where('groupcode', '002')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        $data['users']               = User::select(DB::Raw("concat(first_name ,' ', last_name) AS name, id"))->where('activated', '=', 0)->orderBy('first_name', 'asc')->pluck('name', 'id');
        return view('warehouse.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(WarehouseRequest $request)
    {
        $input             = $request->all();
        $input['id']       = GanticHelper::gen_uuid();
        $input['added_by'] = Session::get('currentUserID');
        $warehouse         = Warehouse::create($input);
        if (isset($input['responsible']) && $input['responsible']) {
            foreach ($input['responsible'] as $key => $value) {
                $responsible_input['warehouse_id'] = $input['id'];
                $responsible_input['user_id']      = $value;
                WarehouseResponsible::create($responsible_input);
            }

        }
        $undefined_location = Warehouse::createDefaultLocation($input['id']);
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
        $data['warehouse']              = Warehouse::findorFail($id);
        $language                       = Session::get('language') ? Session::get('language') : 'no';
        $data['warehousemain_array']    = DropdownHelper::where('language', $language)->where('groupcode', '002')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        $data['users']                  = User::select(DB::Raw("concat(first_name ,' ', last_name) AS name, id"))->where('activated', '=', 0)->orderBy('first_name', 'asc')->pluck('name', 'id');
        $responsible                    = WarehouseResponsible::getResponsibleUserId($id);
        $data['warehouse']->responsible = $responsible;
        return view('warehouse.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(WarehouseRequest $request, $id)
    {
        $warehouse = Warehouse::findOrFail($id);
        $input     = $request->all();
        //$input['main'] = $request->input('main')?"1":"0";
        $input['updated_by'] = Session::get('currentUserID');
        $warehouse->fill($input);
        $warehouse->save();
        // store responsible person in warehouse_responsible pivot table
        WarehouseResponsible::where('warehouse_id', $id)->delete();
        if (isset($input['responsible']) && $input['responsible']) {

            foreach ($input['responsible'] as $key => $value) {
                $responsible_input['warehouse_id'] = $id;
                $responsible_input['user_id']      = $value;
                WarehouseResponsible::create($responsible_input);
            }
        }
        $default_location = Location::whereName('Undefined')->where('warehouse_id', $id)->count();
        if ($default_location == 0) {
            $undefined_location = Warehouse::createDefaultLocation($id);
        }
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
        $warehouse = Warehouse::findOrFail($id);
        if (!$warehouse) {
            return Redirect::route($this->route)->with($this->error, __($this->notfound));
        }
        $warehouse->delete();
        return Redirect::route($this->route)->with($this->success, __($this->deletemsg));
    }
}

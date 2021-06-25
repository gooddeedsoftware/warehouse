<?php
namespace App\Http\Controllers;

use App\Helpers\GanticHelper;
use App\Http\Requests\EquipmentRequest;
use App\Models\Customer;
use App\Models\DropdownHelper;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\EquipmentChild;
use App\Models\Order;
use DB;
use Redirect;
use Request;
use Response;
use Session;

class EquipmentController extends Controller
{

    protected $folder           = 'equipment';
    protected $route            = 'main.equipment.index';
    protected $title            = 'main.equipment';
    protected $success          = 'success';
    protected $error            = 'error';
    protected $notfound         = 'main.notfound';
    protected $createmsg        = 'main.equipment_createsuccess';
    protected $updatemsg        = 'main.equipment_updatesuccess';
    protected $deletemsg        = 'main.equipment_deletesuccess';
    protected $equipment        = 'main.equipment';
    protected $error_msg_prefix = 'main.error_msg_prefix';
    protected $error_msg_suffix = 'main.error_msg_suffix';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data = @Session::get('equipment_search') ? @Session::get('equipment_search') : [];
        @Request::all() ? Session::put('equipment_search', array_merge($data, @Request::all())) : '';
        $data['equipment_categories'] = EquipmentCategory::getAllEquipmentCategories();
        $data['equipments']           = Equipment::getEquipment(Session::get('equipment_search'), Request::get('customer_id'));
        return view('equipment.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $btn_value                                 = Request::get('btn_value');
        $data['customer']                          = Customer::select(DB::Raw("concat(customer ,' - ', IFNULL(name,'')) AS customer, id"))->orderby('name', 'asc')->pluck('customer', 'id');
        $data['equipment']                         = array();
        $language                                  = Session::get('language') ? Session::get('language') : 'no';
        $data['equipement_category_from_dropdown'] = DropdownHelper::where('language', $language)->where('groupcode', '012')->orderBy('keycode', 'asc')->pluck('label', 'keycode')->toArray();
        $data['equipment_category']                = EquipmentCategory::orderby('type', 'asc')->pluck('type', 'id');
        $data['equipments']                        = array();
        $data['btn_value']                         = $btn_value;
        if ($btn_value != 1) {
            $data['customer_id']      = Equipment::select('customer_id')->where('id', '=', $btn_value)->first();
            $data['customer_id']      = $data['customer_id']->customer_id;
            $data['parent_equipment'] = $btn_value;
            $data['equipments']       = Equipment::where('customer_id', '=', $data['customer_id'])->pluck('description', 'id');
        }
        return view('equipment.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(EquipmentRequest $request)
    {
        $input                     = $request->all();
        $input['id']               = GanticHelper::gen_uuid();
        $input['install_date']     = $request->get('install_date') ? date('Y-m-d', strtotime($input['install_date'])) : null;
        $input['last_repair_date'] = $request->get('last_repair_date') ? date('Y-m-d', strtotime($input['last_repair_date'])) : null;
        $input['added_by']         = Session::get('currentUserID');
        Equipment::create($input);
        if (@$request->get('child_equipment_id')) {
            $child_equipment_id                               = $request->get('child_equipment_id');
            $child_equipment_id_details['child_equipment_id'] = $input['id'];
            $child_equipment_id_details['equipment_id']       = $child_equipment_id;
            EquipmentChild::create($child_equipment_id_details);
        }
        if ($input['btn_value'] == 1) {
            return Redirect::route($this->route)->with($this->success, trans($this->createmsg));
        }
        if ($input['btn_value'] != 1) {
            return Redirect::action('EquipmentController@edit', [$input['btn_value']])->with($this->success, trans($this->createmsg));
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
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
        $data['equipment']                         = Equipment::findorFail($id);
        $data['customer']                          = Customer::select(DB::Raw("concat(customer ,' - ', IFNULL(name,'')) AS customer, id"))->orderby('name', 'asc')->pluck('customer', 'id');
        $data['equipment']->install_date           = $data['equipment']->install_date ? date('d.m.Y', strtotime($data['equipment']->install_date)) : "";
        $data['equipment']->last_repair_date       = $data['equipment']->last_repair_date ? date('d.m.Y', strtotime($data['equipment']->last_repair_date)) : "";
        $language                                  = Session::get('language') ? Session::get('language') : 'no';
        $data['equipement_category_from_dropdown'] = DropdownHelper::where('language', $language)->where('groupcode', '012')->orderBy('keycode', 'asc')->pluck('label', 'keycode')->toArray();
        // get deleted equipment category also
        $data['equipment_category'] = EquipmentCategory::getAllEquipmentCategories();
        $parentequipemnts           = EquipmentChild::select('equipment_id')->where('equipment_id', '=', $id)->get();
        $parentequipemnts_array     = array();
        foreach ($parentequipemnts as $key => $value) {
            $parentequipemnts_array[] = $value->equipment_id;
            $parent_equipment_id      = Equipment::getParentEquipments($parentequipemnts_array, $value->equipment_id);
        }

        $order_details       = Order::Where('equipment_id', '=', $id)->get();
        $data['order_count'] = count($order_details);

        if (@$parent_equipment_id) {
            $data['equipments'] = Equipment::select(DB::Raw("concat(internalnbr ,' - ', IFNULL(description,'')) AS description, id"))->where('customer_id', '=', $data['equipment']->customer_id)->where('id', '!=', $id)->whereNotIn('id', @$parent_equipment_id)->pluck('description', 'id');
        } else {
            $data['equipments'] = Equipment::select(DB::Raw("concat(internalnbr ,' - ', IFNULL(description,'')) AS description, id"))->where('customer_id', '=', $data['equipment']->customer_id)->where('id', '!=', $id)->pluck('description', 'id');
        }
        $data['selected_child_equipments'] = [];
        $data['child_equipments']          = EquipmentChild::where('equipment_id', '=', $id)->get();
        if ($data['child_equipments'] != '') {
            foreach ($data['child_equipments'] as $key => $value) {
                $data['selected_child_equipments'][] = $value->child_equipment_id;
            }
        }
        //Added on 19.1.2018
        $data['parent_equipents'] = EquipmentChild::where('child_equipment_id', '=', $id)->get();
        if (@$data['parent_equipents']) {
            foreach ($data['parent_equipents'] as $key => $value) {
                $data['selected_parent_equipment'] = $value->equipment_id;
            }
        }

        $data['btn_value']      = 1;
        $child_equipments_array = EquipmentChild::select('equipment_id')->where('child_equipment_id', '=', $id)->get();
        if (@$child_equipments_array->toArray()) {
            $data['btn_value'] = 3;
        }
        $data['child_equipments_details'] = Equipment::whereIn('id', $data['selected_child_equipments'])->get();
        $data['orders']                   = Order::where('equipment_id', '=', $id)->get();
        $data['customers']                = Customer::where('status', '=', '0')->orderby('name', 'asc')->pluck('name', 'id');
        $language                         = Session::get('language') ? Session::get('language') : 'no';
        $data['order_status']             = DropdownHelper::where('language', $language)->where('groupcode', '005')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        return view('equipment.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(EquipmentRequest $request, $id)
    {
        $input           = $request->all();
        $equipment       = Equipment::findorFail($id);
        $old_customer_id = $equipment->customer_id;

        if ($input['customer_id'] != $old_customer_id) {

            $child_equipment_ids = EquipmentChild::select('child_equipment_id')->where('equipment_id', '=', $id)->get();
            if (@$child_equipment_ids) {
                foreach ($child_equipment_ids as $key => $value) {
                    Equipment::updateCustomerByParent($value->child_equipment_id, $input['customer_id']);
                    Equipment::where('id', $value->child_equipment_id)->update(['customer_id' => $input['customer_id']]);
                }
            }
        }

        $input['install_date']     = $request->get('install_date') ? date('Y-m-d', strtotime($input['install_date'])) : null;
        $input['last_repair_date'] = $request->get('last_repair_date') ? date('Y-m-d', strtotime($input['last_repair_date'])) : null;
        $input['updated_by']       = Session::get('currentUserID');
        $equipment->fill($input);
        $equipment->save();

        EquipmentChild::where('child_equipment_id', $id)->delete();

        if (@$request->get('child_equipment_id')) {
            $child_equipment_id = $request->get('child_equipment_id');

            $child_equipment_id_details['equipment_id'] = $child_equipment_id;

            $child_equipment_id_details['child_equipment_id'] = $id;

            EquipmentChild::create($child_equipment_id_details);

        }

        return Redirect::route($this->route)->with($this->success, trans($this->updatemsg));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $equipment = Equipment::findorFail($id);
        EquipmentChild::where('child_equipment_id', $id)->delete();
        if (!$equipment) {
            return Redirect::route($this->route)->with($this->error, trans($this->notfound));
        }
        $equipment->delete();
        return Redirect::route($this->route)->with($this->success, trans($this->deletemsg));
    }

    /**
     * [getChildEquipments description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getChildEquipments($customer_id = false)
    {
        try {
            if (@$customer_id) {
                $equipment_array = Equipment::select(DB::Raw("concat(IFNULL(internalnbr, ' ') ,' - ', IFNULL(description,'')) AS description, id"))->where('customer_id', '=', $customer_id)->pluck('description', 'id');
                return json_encode(array("status" => "success", "values" => $equipment_array));
            }
        } catch (Exception $e) {

        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Helpers\GanticHelper;
use App\Http\Requests\CCSheetRequest;
use App\Models\CCSheet;
use App\Models\CCSheetCurrency;
use App\Models\CCSheetDetails;
use App\Models\CcSheetScannedProduct;
use App\Models\Currency;
use App\Models\DropdownHelper;
use App\Models\Location;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseInventory;
use Input;
use Redirect;
use Request;
use Response;
use Session;

class CCSheetController extends Controller
{

    protected $folder    = 'ccsheet';
    protected $route     = 'main.ccsheet.index';
    protected $success   = 'success';
    protected $error     = 'error';
    protected $notfound  = 'main.notfound';
    protected $createmsg = 'main.ccsheet_createsuccess';
    protected $updatemsg = 'main.ccsheet_updatesuccess';
    protected $deletemsg = 'main.ccsheet_deletesuccess';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     **/
    public function index()
    {
        $data = @Session::get('ccsheet_search') ? @Session::get('ccsheet_search') : [];
        @\Request::input() ? Session::put('ccsheet_search', array_merge($data, @\Request::input())) : '';
        $language               = Session::get('language') ? Session::get('language') : 'no';
        $data['ccsheets']       = CCSheet::getCCSheets(@Session::get('ccsheet_search'));
        $data['warehouses']     = Warehouse::orderby('shortname', 'asc')->pluck('shortname', 'id');
        $data['ccsheet_status'] = DropdownHelper::where('language', $language)->where('groupcode', '018')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        return view('ccsheet.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     **/
    public function create()
    {
        $language                 = Session::get('language') ? Session::get('language') : 'no';
        $ccsheet                  = new CCSheet();
        $data['warehouse']        = $ccsheet->getWarehouses();
        $data['currency_details'] = Currency::getRecentCurrencyDetails();
        $data['currency_list']    = DropdownHelper::where('language', $language)->where('groupcode', '015')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        return view('ccsheet.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     **/
    public function store(CCSheetRequest $request)
    {
        $input               = $request->all();
        $input['status']     = 1;
        $input['created_by'] = Session::get('currentUserID');
        $data                = CCSheet::create($input);
        CCSheetCurrency::storeCCSheetCurrency($data->id);
        CCSheetDetails::getOrCreateCCSheetDetails($data->id);
        return Redirect::route($this->route)->with($this->success, trans($this->createmsg));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     **/
    public function destroy($id)
    {
        $ccsheet = CCSheet::findOrFail($id);
        if (@$ccsheet->recount_of) {
            $ccsheet->forceDelete($id);
        } else {
            $ccsheet->delete($id);
        }
        return Redirect::route($this->route)->with($this->success, trans($this->deletemsg));
    }

    /**
     *   Display ccsheet details
     *   @param ccsheet id
     *   @return object, ccsheet details page
     **/
    public function ccsheetDetails($ccsheetid = false)
    {
        $ccsheet = CCSheet::where('id', '=', $ccsheetid)->first();
        if ($ccsheet->status != 5) {
            CCSheetDetails::updateCCsheetDetails($ccsheetid);
        }
        $data            = CCSheetDetails::getOrCreateCCSheetDetails($ccsheetid);
        $data['recount'] = 0;
        $data['ccsheet'] = $ccsheet = CCSheet::where('id', '=', $ccsheetid)->with('warehouse')->first();
        if ($ccsheet->recount_of != '') {
            $data['recount'] = 1;
        }
        $data['ccsheet']->created_date = $data['ccsheet']->created_at->format('d.m.Y');
        $product_id_details            = CCSheetDetails::select('product_id')->where('ccsheet_id', '=', $ccsheetid)->get()->toArray();
        return view('ccsheet.ccsheet_details', $data);
    }

    /**
     *   Display ccsheet details
     *   @param ccsheet id
     *   @return object, ccsheet details page
     **/
    public function setCounted()
    {
        try {
            $id              = \Request::get('id');
            $counted         = \Request::get('counted');
            $counted_by      = \Request::get('counted_by');
            $recount         = \Request::get('recount');
            $input_array     = array('id' => $id, 'counted' => $counted, 'counted_by' => $counted_by, 'recount' => $recount);
            $ccsheet_details = CCSheetDetails::updateCounted($input_array);
            if ($ccsheet_details) {
                echo json_encode(array("status" => "success", "data" => $ccsheet_details));
            } else {
                echo json_encode(array("status" => "error", "data" => "No data found"));
            }
        } catch (Exception $e) {
            echo json_encode(array("status" => "error", "data" => "No data found"));
        }
    }

    /**
     *   updateCCSheetStatus
     *   @param ccsheet id
     *   @return index route view.
     **/
    public function updateCCSheetStatus($id)
    {
        try {
            $ccsheet_details = CCSheet::updateStatus($id);
            return Redirect::route($this->route)->with($this->success, trans($this->updatemsg));
        } catch (Exception $e) {
        }
    }

    /**
     *   Display mismatch ccsheet details
     *   @param ccsheet id integer
     *   @return object, ccsheet details page
     **/
    public function recountCCSheetDetails($ccsheetid = false)
    {
        $exist_rec   = CCSheet::where('recount_of', $ccsheetid)->first();
        $inserted_id = @$exist_rec ? $exist_rec->id : CCSheetDetails::createRecount($ccsheetid);
        if ($inserted_id) {
            $data = CCSheetDetails::getOrCreateCCSheetDetails($inserted_id);
        }
        $data['ccsheet'] = $ccsheet = CCSheet::where('id', '=', $inserted_id)->with('warehouse')->first();
        $data['recount'] = 1;
        return view('ccsheet.ccsheet_details', $data);
    }

    /**
     *   Create ccsheet Report
     *   @param ccsheet id integer
     *   @return object, ccsheet details page
     **/
    public function createCCSheetReport($ccsheetid)
    {
        try {
            $ccsheet_file = CCSheet::createReport($ccsheetid);
            $fileName     = "ccsheet.pdf";
            $headers      = array('Content-Type: application/pdf');
            return Response::download($ccsheet_file, $fileName, $headers);
        } catch (Exception $e) {

        }
    }

    /**
     *   Create adjustment order from mismatched products
     *   @param id int
     *   @return void
     **/
    public function createAdjustmentOrder($id, $warehouse_id)
    {
        if ($id) {
            $language                               = Session::get('language') ? Session::get('language') : 'no';
            $data                                   = array();
            $data['warehouseorder_product_details'] = CCSheetDetails::getMismatchedProducts($id);
            $data['warehouseorder']                 = array();
            $data['warehouseorder']['warehouse']    = $warehouse_id;
            $data['btn']                            = trans('main.create');
            $data['ccsheet_id']                     = $id;
            $data['warehouses']                     = Warehouse::orderby('shortname', 'asc')->pluck('shortname', 'id');
            $data['priorities']                     = DropdownHelper::where('groupcode', '=', '006')->where('language', $language)->orderby('keycode', 'asc')->pluck('label', 'keycode');
            $data['status']                         = DropdownHelper::where('groupcode', '=', '013')->where('language', $language)->where('keycode', '3')->orderby('keycode', 'asc')->pluck('label', 'keycode');
            $data['destination_locations']          = Location::orderby('name', 'asc')->where('warehouse_id', '=', $warehouse_id)->pluck('name', 'id');
            return view('warehousedetails/order/partials/CCsheetAdjustmentOrder', $data);
        }
    }

    /**
     * getProducts
     * @return object
     */
    public function getProducts()
    {
        $products = Product::orderBy('name')->get();
    }

    /**
     * saveCCSheetRecord
     *
     */
    public function saveCCSheetRecord(Request $request)
    {
        try {
            $input                        = \Request::all();
            $product_details              = Product::where('id', '=', $input['product_id'])->first();
            $input['product_number']      = $product_details->product_number;
            $input['description']         = $product_details->description;
            $input['vendor_price']        = $product_details->vendor_price;
            $input['sn_required']         = $product_details->sn_required;
            $input['curr_iso']            = @$product_details->curr_iso_name;
            $input['counted_by']          = Session::get('currentUserID');
            $input['recounted_by']        = Session::get('currentUserID');
            $input['added_by']            = Session::get('currentUserID');
            $input['inv_id']              = '';
            $input['counted_at']          = date('Y-m-d H:i:s');
            $ccsheet_details              = CCSheetDetails::create($input);
            $location_text                = Location::select('name')->where('id', '=', $ccsheet_details->location_id)->first();
            $ccsheet_details->location_id = $location_text->name;
            if ($ccsheet_details) {
                echo json_encode(array("status" => "success", "data" => $ccsheet_details));
            } else {
                echo json_encode(array("status" => "error", "data" => "No data found"));
            }
        } catch (Exception $e) {

        }
    }

    /**
     * [deleteCCSheetProduct description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function deleteCCSheetProduct(Request $request)
    {
        try {
            $result       = 2;
            $input        = \Request::all();
            $ccsheet_id   = $input['ccsheetdetail_id'];
            $product_id   = $input['product_id'];
            $location_id  = $input['location_id'];
            $inventory_id = CCSheetDetails::where('id', '=', $ccsheet_id)->first();
            if (@$inventory_id) {
                if (@$inventory_id->inv_id) {
                    $warehouse_inventory_details = WarehouseInventory::where('id', '=', $inventory_id->inv_id)->first();
                    if (@$warehouse_inventory_details) {
                        if ($warehouse_inventory_details->qty == 0 && $warehouse_inventory_details->ordered == 0 || $warehouse_inventory_details->qty == "" && $warehouse_inventory_details->ordered == "") {
                            WarehouseInventory::where('id', '=', $inventory_id->inv_id)->delete();
                            CCSheetDetails::where('id', '=', $ccsheet_id)->delete();
                            $result = 1;
                        }
                    }
                } else {
                    CCSheetDetails::where('id', '=', $ccsheet_id)->delete();
                    $result = 1;
                }
            }
            echo json_encode(array("status" => "success", "data" => $result));

        } catch (Exception $e) {

        }
    }

    /**
     * [scannerView description]
     * @param  boolean $ccsheet_id [description]
     * @return [type]              [description]
     */
    public function scannerView($ccsheet_id = false)
    {
        try {
            $data['ccsheet']               = CCSheet::where('id', '=', $ccsheet_id)->with('warehouse')->first();
            $data['locations']             = Location::where('warehouse_id', '=', $data['ccsheet']->whs_id)->get();
            $data['scanned_products']      = CcSheetScannedProduct::where('ccsheet_id', '=', $ccsheet_id)->where('counted', '=', 0)->with('productDetail', 'locationDetail')->get();
            $data['scanned_product_count'] = count($data['scanned_products']);
            return view('ccsheet.scanner_view', $data);
        } catch (Exception $e) {
            $error_message = '"MOdule: CCSheet scanner" , Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            GanticHelper::errorLog($error_message);
            return Redirect::back()->with('error', trans('main.something_went_wrong'));
        }
    }

    /**
     * [storeScannedProduct description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function storeScannedProduct(Request $request)
    {
        try {
            $input             = \Request::all();
            $product_detail    = Product::where('product_number', '=', $input['product_number'])->first();
            $input['product']  = $product_detail->id;
            $location_detail   = Location::where('name', '=', $input['location_name'])->where('warehouse_id', '=', @$input['warehouse'])->first();
            $input['location'] = $location_detail->id;
            $scanned_product   = CcSheetScannedProduct::create($input);
            echo json_encode(array("status" => "success", "data" => $scanned_product));
        } catch (Exception $e) {
            $error_message = '"MOdule: CCSheet scanner" , Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            GanticHelper::errorLog($error_message);
            echo json_encode(array("status" => "error", "data" => null));
        }
    }

    /**
     * [checkLocationCounted description]
     * @param  [type] $location_name [description]
     * @param  [type] $ccsheet_id    [description]
     * @param  [type] $warehouse_id  [description]
     * @return [type]                [description]
     */
    public function checkLocationCounted($location_name, $ccsheet_id, $warehouse_id)
    {
        try {
            $location_detail = Location::where('name', '=', $location_name)->where('warehouse_id', '=', $warehouse_id)->first();
            $product_details = CcSheetScannedProduct::where('ccsheet_id', '=', $ccsheet_id)->where('location', '=', $location_detail->id)->get();
            if (count($product_details) > 0) {
                echo json_encode(array("status" => "success", "data" => 1));
            } else {
                echo json_encode(array("status" => "success", "data" => 0));
            }

        } catch (Exception $e) {
            $error_message = '"MOdule: CCSheet scanner" , Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            GanticHelper::errorLog($error_message);
            echo json_encode(array("status" => "error", "data" => null));
        }

    }

    /**
     * [completeCounting description]
     * @param  [type] $ccsheet_id [description]
     * @return [type]             [description]
     */
    public function completeCounting($ccsheet_id)
    {
        try {
            $result = CCSheet::updateCCsheetFromScannedProduct($ccsheet_id);
            if ($result) {
                return \Redirect::route('main.ccsheet.ccsheetDetails', [$ccsheet_id])->with('success', trans('main.counted_success_msg'));
            } else {
                return Redirect::back()->with('error', trans('main.something_went_wrong'));
            }
        } catch (Exception $e) {
            $error_message = '"MOdule: CCSheet scanner" , Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            GanticHelper::errorLog($error_message);
            return Redirect::back()->with('error', trans('main.something_went_wrong'));
        }
    }

    /**
     * [resetScannedProduct description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function resetScannedProduct(Request $request)
    {
        try {
            $input  = \Request::all();
            $result = CCSheet::resetCCsheetFromScannedProduct($input['ccsheet_id'], $input['location_name']);
        } catch (Exception $e) {
            $error_message = '"MOdule: CCSheet scanner" , Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            GanticHelper::errorLog($error_message);
            echo json_encode(array("status" => "error", "data" => null));
        }
    }
}

<?php
namespace App\models;

use App\Helpers\GanticHelper;
use App\Models\CCSheetDetails;
use App\Models\CcSheetScannedProduct;
use App\Models\Currency;
use App\Models\DropdownHelper;
use App\Models\Location;
use App\Models\Product;
use App\Models\Warehouse;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use PDF;
use Session;
use View;

class CCSheet extends Model
{

    use SoftDeletes;
    use Sortable;

    protected $table    = 'ccsheet';
    public $timestamps  = true;
    protected $dates    = ['deleted_at'];
    protected $fillable = array('whs_id', 'status', 'completed_at', 'completed_by', 'created_by', 'updated_by', 'recount_of', 'comments', 'blind_count', 'deleted_at', 'whs_order_id');
    protected $sortable = array('created_at', 'status');

    /**
     *   Get CCSheets product details
     *   @return object
     */
    public function ccsheetDetailsGroupbyLocation()
    {
        return $this->hasMany('App\Models\CCSheetDetails', 'ccsheet_id', 'id');
    }

    /**
     *   Get CCSheets product details
     *   @return object
     */
    public function ccsheet_details()
    {
        return $this->hasMany('App\Models\CCSheetDetails', 'ccsheet_id', 'id')->orderby('product_number', 'asc');
    }

    /**
     *   Get CCSheets product details
     *   @return object
     */
    public function group_ccsheet_details()
    {
        return $this->hasMany('App\Models\CCSheetDetails', 'ccsheet_id', 'id')->orderby('product_number', 'asc');
    }

    /**
     *   Get warehouse
     *   @return object
     */
    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse', 'whs_id', 'id');
    }

    /**
     *   Get completed by user
     *   @return object
     */
    public function completedUser()
    {
        return $this->belongsTo('App\Models\User', 'completed_by', 'id');
    }

    /**
     *   Get status
     *   @return object
     */
    public function status()
    {
        return $this->belongsTo('App\Models\DropdownHelper', 'status', 'keycode')->where('groupcode', '=', '018');
    }

    /**
     *    Get CCSheets
     *
     *    @param  conditions
     *     @param  order by fields
     *    @param  Sorting order
     *     @return ccsheet details[object]
     *
     */
    public static function getCCSheets($conditions = false, $orderby = 'created_at', $order = 'desc')
    {
        $ccsheet_details = CCSheet::select('ccsheet.*');
        $ccsheet_details->selectRaw('ccsheet.id, ccsheet.whs_id, ccsheet.status, ccsheet.completed_at, ccsheet.completed_by, ccsheet.recount_of, ccsheet.whs_order_id, ccsheet.comments, ccsheet.updated_by, ccsheet.blind_count,b.total, b.diff,c.mismatch');
        $ccsheet_details->leftjoin(DB::raw('(select ccsheet_id, sum(on_stock_qty * vendor_price) as total, sum(counted_qty * vendor_price) as diff from ccsheet_details group by ccsheet_id ) as b '), 'ccsheet.id', '=', 'b.ccsheet_id');
        $ccsheet_details->leftjoin(DB::raw('(select ccsheet_id, count(*) as mismatch from ccsheet_details where on_stock_qty != counted_qty group by ccsheet_id ) as c'), 'ccsheet.id', '=', 'c.ccsheet_id');
        $ccsheet_details->leftjoin('ccsheet as c', 'ccsheet.id', '=', 'c.recount_of');
        $ccsheet_details->leftjoin('warehouse', 'ccsheet.whs_id', '=', 'warehouse.id');
        $ccsheet_details->leftjoin('dropdown_helper', 'ccsheet.status', '=', 'dropdown_helper.key_code');
        $ccsheet_details->groupby('ccsheet.id');

        // search in ccsheet
        if (isset($conditions['search']) && $conditions['search'] != '') {
            $search = $conditions['search'];
            $ccsheet_details->where(function ($query) use ($search) {
                $query->orwhere('ccsheet.status', 'LIKE', '%' . $search . '%');
                $query->orwhere('dropdown_helper.value_en', 'LIKE', '%' . $search . '%');
                $query->orwhere('dropdown_helper.value_no', 'LIKE', '%' . $search . '%');
                $query->orwhere('warehouse.shortname', 'LIKE', '%' . $search . '%');
                $query->orwhere('b.total', 'LIKE', '%' . str_replace(",", ".", $search) . '%');
                $query->orwhere('b.diff', 'LIKE', '%' . str_replace(",", ".", $search) . '%');
                $query->orwhere('c.mismatch', 'LIKE', '%' . str_replace(",", ".", $search) . '%');
                $query->orwhere('ccsheet.created_at', 'LIKE', '%' . formatSearchDate($search) . '%');
            });
        }

        // filter ccsheet using warehouse
        if (isset($conditions['warehouse']) && $conditions['warehouse'] != '') {
            $warehosue = $conditions['warehouse'];
            $ccsheet_details->where('ccsheet.whs_id', '=', $warehosue);
        }

        // filter ccsheet using created_at(startdate)
        if (isset($conditions['start_date']) && $conditions['start_date'] != '') {
            $startdate = GanticHelper::formatDate($conditions['start_date'], 'Y-m-d');
            $ccsheet_details->where('ccsheet.created_at', '>=', $startdate);
        }

        // filter ccsheet using created_at(end date)
        if (isset($conditions['end_date']) && $conditions['end_date'] != '') {
            $enddate = GanticHelper::formatDate($conditions['end_date'], 'Y-m-d') . " 23:59:59";
            $ccsheet_details->where('ccsheet.created_at', '<=', $enddate);
        }
        $show_recount_ids = array();
        $paginate_size    = Session::get('paginate_size') ? Session::get('paginate_size') : 10;
        $ccsheet_details  = $ccsheet_details->whereNull('c.recount_of')->sortable(['created_at' => 'desc'])->paginate($paginate_size);
        for ($i = 0; $i < count($ccsheet_details); $i++) {
            $ccsheet_details[$i]['completed_at'] = GanticHelper::formatDate($ccsheet_details[$i]['completed_at']);
            $ccsheet_details[$i]['created_date'] = $ccsheet_details[$i]['created_at']->format('d.m.Y');
            $ccsheet_details[$i]['show_recount'] = (($ccsheet_details[$i]['total'] != $ccsheet_details[$i]['diff'] && $ccsheet_details[$i]['mismatch'] != 0) ? true : false);
            $responsible_details                 = array();
            if (@$ccsheet_details[$i]->warehouse->warehouseResponsible) {
                foreach ($ccsheet_details[$i]->warehouse->warehouseResponsible as $key => $value) {
                    $responsible_details[] = $value->user_id;
                }
            }
            if (in_array(Session::get('currentUserID'), $responsible_details) || Session::get('usertype') == "Admin") {
                $difference                   = $ccsheet_details[$i]['diff'] - $ccsheet_details[$i]['total'];
                $ccsheet_details[$i]['total'] = Number_format($ccsheet_details[$i]['total'], 2, ',', '');
                $ccsheet_details[$i]['diff']  = Number_format($difference, 2, ',', '');
            } else {
                $ccsheet_details[$i]['total'] = '';
                $ccsheet_details[$i]['diff']  = '';
            }

            if ($ccsheet_details[$i]['whs_order_id'] != '') {
                $ccsheet_details[$i]['show_recount'] = false;
            } else {
                $ccsheet_details[$i]['show_recount'] = ($ccsheet_details[$i]['mismatch'] != 0) ? true : false;
            }
            // check the ccsheet is completed
            if ($ccsheet_details[$i]['status'] == 5 && $ccsheet_details[$i]['show_recount']) {
                if (!array_key_exists($ccsheet_details[$i]['whs_id'], $show_recount_ids)) {
                    $show_recount_ids[$ccsheet_details[$i]['whs_id']] = $ccsheet_details[$i]['id'];
                    $ccsheet_details[$i]['show_recount']              = true;
                } else {
                    $ccsheet_details[$i]['show_recount'] = false;
                }
            }
        }
        return $ccsheet_details;
    }

    /**
     * [departmentSortable description]
     * @param  [type] $query     [description]
     * @param  [type] $direction [description]
     * @return [type]            [description]
     */
    public function warehouseSortable($query, $direction)
    {
        $query->orderby('warehouse.shortname', $direction);
    }

    /**
     * [statusSortable description]
     * @param  [type] $query     [description]
     * @param  [type] $direction [description]
     * @return [type]            [description]
     */
    public function statusSortable($query, $direction)
    {
        $language = Session::get('language') ? Session::get('language') : 'no';
        if ($language == 'no') {
            $query->orderby('dropdown_helper.value_no', $direction);
        } else {
            $query->orderby('dropdown_helper.value_en', $direction);
        }

    }
    /**
     *   Get warehouses
     *   @return object
     **/
    public function getWarehouses()
    {

        $ccsheet_whs_details = DB::select("select max(ccsheet.id) as ccsheetID, whs_id from ccsheet left join ccsheet_details on ccsheet_id = ccsheet.id left join whs_transfer_order on whs_transfer_order.id = ccsheet.whs_order_id group by whs_id");
        $warehouse_ids       = array();

        foreach ($ccsheet_whs_details as $key => $value) {

            $ccsheet = CCSheet::where('id', '=', $value->ccsheetID)->first();
            if (@$ccsheet) {
                if ($ccsheet->recount_of && $ccsheet->whs_order_id) {
                    $ccsheet_counts = CCSheet::leftjoin('whs_transfer_order', 'whs_transfer_order.id', '=', 'ccsheet.whs_id')->where('whs_transfer_order.order_status', '=', 5)->first();
                    if (@$ccsheet_counts) {
                        $warehouse_ids[] = $ccsheet->whs_id;
                    } else {
                        continue;
                    }
                } else {
                    $ccsheet_counts = CCSheet::leftjoin('ccsheet_details', 'ccsheet_details.ccsheet_id', '=', 'ccsheet.id')->where('ccsheet.id', $ccsheet->id)->where('status', '=', '5')->whereRaw('(select count(*) from ccsheet_details where ccsheet_id = ccsheet.id and on_stock_qty != counted_qty)  = 0')->get();

                    if (count($ccsheet_counts)) {
                        continue;
                    } else {
                        $warehouse_ids[] = $ccsheet->whs_id;
                    }
                }
            }
        }
        $warehouse_details = Warehouse::whereNotIn('id', $warehouse_ids)->get();
        $warehouses        = array();
        foreach ($warehouse_details as $key => $value) {
            $warehouses[$value->id] = $value->shortname;
        }
        return $warehouses;
    }

    /**
     *   Update ccsheet status
     *   @param  id
     *
     **/
    public static function updateStatus($id)
    {
        try {
            if ($id) {
                CCSheet::where('id', '=', $id)->update(['status' => 5, 'completed_by' => Session::get('currentUserID'), 'completed_at' => date('Y-m-d H:i:s')]);
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     *   Update ccsheet status
     *   @param  id
     *
     **/
    public static function updateOrderNumberInCCSheet($id, $order_id)
    {
        try {
            if ($id) {
                CCSheet::where('id', '=', $id)->update(['whs_order_id' => $order_id]);
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     *   create ccsheet report
     *   @param  id int
     *
     **/
    public static function createReport($id, $return_content = false)
    {
        try {
            if ($id) {
                $ccsheet          = new CCSheet();
                $dynamic_data     = $ccsheet->constructDataForReport($id);
                $currencies       = '';
                $currency_details = Currency::getRecentCurrencyDetails();
                if ($currency_details) {
                    foreach ($currency_details as $key => $value) {
                        if ($value->curr_iso_name != 'NOK') {
                            $currencies = $currencies . ' 1 ' . $value->curr_iso_name . ' = ' . number_format($value->exch_rate, 2, ',', ' ');
                        }
                    }
                }
                $dynamic_data['currencies'] = $currencies;
                $ccsheet_report_view        = View::make('ccsheet.ccsheetReport', $dynamic_data);
                $ccsheet_report_contents    = $ccsheet_report_view->render();
                // if ($return_content) {
                //     $result = CCSheet::where('id', '=', $id)->first();
                //     if (@$result->whs_order_id && $result->whs_order_id) {
                //         $warehouseorder = new WarehouseOrder();
                //         $ccsheet_report_contents .= $warehouseorder->createWarehouseReport($result->whs_order_id, true);
                //     }
                // }
                $temp_pdf_file_name = GanticHelper::createTempFile("pdf");
                PDF::loadHTML($ccsheet_report_contents)->setOrientation('landscape')->save($temp_pdf_file_name);
                return $temp_pdf_file_name;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * constructDataForReport
     * @param  int $id
     * @return array
     */
    public function constructDataForReport($id)
    {
        $data = CCSheet::where('id', '=', $id) /*->orWhereRaw('id = (select recount_of from ccsheet where id="'.$id.'")')*/->with('ccsheet_details.recounted_user', 'ccsheet_details.counted_user', 'ccsheet_details.location', 'warehouse', 'completedUser')->with(['group_ccsheet_details' => function ($query) use ($id) {
            $query->select('*')->addSelect(DB::Raw('SUM(counted_qty) as counted_qty'));
            $query->orWhere('ccsheet_id', '=', $id);
            $query->groupBy('product_id', 'location_id');
        }])->first();
        $dynamic_data              = array();
        $split_ccsheet_by_location = array();

        foreach ($data['group_ccsheet_details'] as $key => $value) {
            if ($value->curr_iso != 'NOK') {
                $new_vendor_price = Currency::where('curr_iso_name', '=', $value->curr_iso)->orderBy('created_at', 'desc')->first();
                if (@$new_vendor_price) {
                    $new_vendor_price    = $new_vendor_price->exch_rate * $value->vendor_price;
                    $value->vendor_price = $new_vendor_price;
                }
            }
            $split_ccsheet_by_location[$value->location->name][] = $value;
        }
        ksort($split_ccsheet_by_location);
        $dynamic_data['completed_by'] = (@$data->completedUser ? $data->completedUser->first_name . ' ' . $data->completedUser->last_name : '');
        $dynamic_data['completed_at'] = $data->completed_at;
        $dynamic_data['warehouse']    = $data->warehouse->shortname;
        $dynamic_data['comments']     = $data->comments;
        $dynamic_data['whs_order_id'] = $data->whs_order_id;
        $dynamic_data['locations']    = $split_ccsheet_by_location;

        /*$result = CCSheet::select('comments')->where('id', '=', $id)->first();
        $dynamic_data['comments'] = $result->comments;*/
        return $dynamic_data;
    }

    /**
     * getCCSheetDatesForWarehouse
     * @param  string $warehouse_id
     * @return object
     */
    public function getCCSheetDatesForWarehouse($warehouse_id)
    {
        $ccsheet_date_array = array();
        if ($warehouse_id) {
            $ccsheet_results = CCSheet::select('ccsheet.*')->addSelect('ccsheet.created_at AS ccsheet_created_date')->leftjoin('whs_transfer_order', 'whs_transfer_order.id', '=', 'ccsheet.whs_order_id')->where('whs_id', '=', $warehouse_id)->where('status', '=', '5');
            $ccsheet_results->whereNotIn('ccsheet.id', CCSheet::select('recount_of')->where('ccsheet.whs_id', '=', $warehouse_id)->where('recount_of', '!=', "")->get());
            $ccsheet_results->where('whs_transfer_order.order_status', '=', '5');
            $ccsheet_results->orWhere('whs_id', '=', $warehouse_id)->where('status', '=', '5');
            $ccsheet_results->whereNotIn('ccsheet.id', CCSheet::select('recount_of')->where('ccsheet.whs_id', '=', $warehouse_id)->where('recount_of', '!=', "")->get())->whereRaw('(select count(*) from ccsheet_details where ccsheet_id = ccsheet.id and on_stock_qty != counted_qty)  = 0');
            $ccsheet_results = $ccsheet_results->orderBy("ccsheet_created_date", "DESC")->get();
            if ($ccsheet_results) {
                foreach ($ccsheet_results as $key => $value) {
                    $date_array["id"]   = $value->id;
                    $date_array["name"] = GanticHelper::formatDate($value->ccsheet_created_date, 'd.m.Y');
                    array_push($ccsheet_date_array, $date_array);
                }
            }
            return $ccsheet_date_array;
        } else {
            return false;
        }
    }

    /**
     * [updateCCsheetFromScannedProduct description]
     * @param  boolean $ccsheet_id [description]
     * @return [type]              [description]
     */
    public static function updateCCsheetFromScannedProduct($ccsheet_id = false)
    {
        try {
            $product_details = CcSheetScannedProduct::where('ccsheet_id', '=', @$ccsheet_id)->where('counted', '=', 0)->get();
            foreach ($product_details as $key => $value) {
                $ccsheet_details = CCSheetDetails::where('ccsheet_id', '=', $ccsheet_id)->where('product_id', '=', $value->product)->where('location_id', '=', $value->location)->first();
                if ($ccsheet_details) {
                    $qty            = $ccsheet_details->counted_qty + $value->qty;
                    $updated_result = CCSheetDetails::where('id', '=', $ccsheet_details->id)->update(['counted_qty' => $qty, 'counted_by' => Session::get('currentUserID'), 'counted_at' => date('Y-m-d H:i:s')]);
                } else {
                    $created_result = CCSheet::createNewProduct($ccsheet_id, $value, $value->qty);
                }
                $updateScannedProduct = CcSheetScannedProduct::where('id', '=', $value->id)->update(['counted' => 1]);
            }
            return 1;
        } catch (\Exception $e) {
            $error_message = '"MOdule: CCSheet scanner" , Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            GanticHelper::errorLog($error_message);
            return null;
        }
    }

    /**
     * [createNewProduct description]
     * @param  [type] $ccsheet_id [description]
     * @param  [type] $data       [description]
     * @param  [type] $qty        [description]
     * @return [type]             [description]
     */
    public static function createNewProduct($ccsheet_id, $data, $qty)
    {
        try {
            $product_details         = Product::where('id', '=', $data->product)->first();
            $language                = Session::get('language') ? Session::get('language') : 'no';
            $unit                    = DropdownHelper::where("groupcode", "=", "010")->where('language', '=', $language)->where('keycode', '=', @$product_details->unit)->first();
            $input['location_id']    = $data->location;
            $input['product_id']     = $product_details->id;
            $input['counted_qty']    = $qty;
            $input['on_stock_qty']   = '0.00';
            $input['ccsheet_id']     = $ccsheet_id;
            $input['unit']           = @$unit->label;
            $input['product_number'] = $product_details->product_number;
            $input['description']    = $product_details->description;
            $input['vendor_price']   = $product_details->vendor_price;
            $input['sn_required']    = $product_details->sn_required;
            $input['curr_iso']       = @$product_details->curr_iso_name;
            $input['counted_by']     = Session::get('currentUserID');
            $input['added_by']       = Session::get('currentUserID');
            $input['inv_id']         = '';
            $input['counted_at']     = date('Y-m-d H:i:s');
            $ccsheet_details         = CCSheetDetails::create($input);
            return $ccsheet_details;
        } catch (\Exception $e) {
            $error_message = '"MOdule: CCSheet scanner" , Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            GanticHelper::errorLog($error_message);
            return null;
        }
    }

    /**
     * [resetCCsheetFromScannedProduct description]
     * @param  [type] $ccsheet_id [description]
     * @param  [type] $location   [description]
     * @return [type]             [description]
     */
    public static function resetCCsheetFromScannedProduct($ccsheet_id, $location)
    {
        try {
            $ccsheet         = CCSheet::where('id', $ccsheet_id)->first();
            $location_detail = Location::where('name', '=', $location)->where('warehouse_id', $ccsheet->whs_id)->first();
            //Delete Not counted products
            $not_counted_products = CcSheetScannedProduct::where('ccsheet_id', '=', $ccsheet_id)->where('location', '=', @$location_detail->id)->where('counted', '=', 0)->delete();
            //Reset the counted values
            $counted_products = CcSheetScannedProduct::where('ccsheet_id', '=', $ccsheet_id)->where('location', '=', @$location_detail->id)->where('counted', '=', 1)->get();
            foreach ($counted_products as $key => $value) {
                $ccsheet_details = CCSheetDetails::where('ccsheet_id', '=', $ccsheet_id)->where('product_id', '=', $value->product)->where('location_id', '=', $value->location)->first();
                if ($ccsheet_details) {
                    $qty            = $ccsheet_details->counted_qty - $value->qty;
                    $updated_result = CCSheetDetails::where('id', '=', $ccsheet_details->id)->update(['counted_qty' => $qty, 'counted_by' => Session::get('currentUserID'), 'counted_at' => date('Y-m-d H:i:s')]);
                }
                $updateScannedProduct = CcSheetScannedProduct::where('id', '=', $value->id)->delete();
            }

        } catch (\Exception $e) {
            $error_message = '"MOdule: CCSheet scanner" , Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            GanticHelper::errorLog($error_message);
            return null;
        }
    }
}

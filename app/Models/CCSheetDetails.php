<?php
namespace App\models;

use App\Models\CCSheet;
use App\Models\DropdownHelper;
use App\Models\Location;
use App\Models\WarehouseInventory;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Session;

class CCSheetDetails extends Model
{

    use softDeletes;
    protected $table    = 'ccsheet_details';
    public $timestamps  = true;
    protected $dates    = ['deleted_at'];
    protected $fillable = array('ccsheet_id', 'inv_id', 'location_id', 'product_id', 'product_number', 'nobb', 'description', 'unit', 'curr_iso', 'vendor_price', 'on_stock_qty', 'counted_qty', 'counted_at', 'counted_by', 'recounted_at', 'recounted_by', 'added_by', 'comments', 'deleted_at', 'mismatched');

    /**
     *    product
     *    Get product details that belongs to the ccsheetdetails
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }

    /**
     *    location
     *    Get location details that belongs to the ccsheetdetails
     */
    public function location()
    {
        return $this->belongsTo('App\Models\Location', 'location_id', 'id')->orderby('name', 'asc');
    }

    /**
     *    recounted user
     *    Get user details that belongs to the ccsheetdetails
     */
    public function recounted_user()
    {
        return $this->belongsTo('App\Models\User', 'recounted_by', 'id');
    }

    /**
     *    recounted user
     *    Get user details that belongs to the ccsheetdetails
     */
    public function counted_user()
    {
        return $this->belongsTo('App\Models\User', 'counted_by', 'id');
    }

    /**
     *   ccsheet user
     *   Get user details that belongs to the ccsheetdetails
     */
    public function ccsheet()
    {
        return $this->hasOne('App\Models\CCSheet', 'id', 'ccsheet_id');
    }

    /**
     *    get or create ccsheet details
     *    @param ccsheeid integer
     *    @param recount boolean
     *    @return object
     */
    public static function getOrCreateCCSheetDetails($ccsheetid = false, $recount = false)
    {
        if ($ccsheetid) {
            $ccsheet_results = CCSheet::where('id', '=', $ccsheetid)->first();
            // check ccsheet id is exist or not in ccsheet table
            $ccsheet_details = CCSheetDetails::where('ccsheet_id', '=', $ccsheetid)->first();
            if ($ccsheet_details) { // if ccsheet details exist then retun those details
            } else {
                CCSheetDetails::getProductFromWarehouse($ccsheetid);
            }
            $ccsheet_details = CCSheetDetails::select('ccsheet_details.id as ccsheet_detail_id', 'ccsheet_id', 'inv_id', 'ccsheet_details.location_id', 'ccsheet_details.product_id', 'ccsheet_details.product_number', 'ccsheet_details.description', 'ccsheet_details.unit', 'ccsheet_details.curr_iso', 'on_stock_qty', 'counted_qty', 'counted_at', 'counted_by', 'recounted_at', 'recounted_by', 'mismatched', 'whs_inventory.qty as qty', 'whs_inventory.ordered as ordered')->where('ccsheet_id', '=', $ccsheetid)->with(array('location' => function ($query) {
                $query->orderBy('name', 'asc');
            }))->with('ccsheet');
            $ccsheet_details->leftjoin('product', 'product.id', '=', 'ccsheet_details.product_id');
            $ccsheet_details->leftjoin('whs_location', 'whs_location.id', '=', 'ccsheet_details.location_id');
            $ccsheet_details->leftjoin('whs_inventory', 'whs_inventory.id', '=', 'ccsheet_details.inv_id');
            $ccsheet_details->orderBy('whs_location.name', 'asc');
            $ccsheet_details->orderBy('product.product_number', 'asc');
            if ($recount) {
                $ccsheet_details->whereRaw('ccsheet_details.on_stock_qty != ccsheet_details.counted_qty');
            }
            if ($ccsheet_results->recount_of && $ccsheet_results->status == 1) {
                $ccsheet_details->where('ccsheet_details.mismatched', '=', 1);
            }
            $locations       = array();
            $ccsheet_details = $ccsheet_details->get();
            for ($i = 0; $i < count($ccsheet_details); $i++) {
                $ccsheet_details[$i]->on_stock_qty                = number_format($ccsheet_details[$i]->on_stock_qty, 2, ',', '');
                $ccsheet_details[$i]->counted                     = $counted_qty                     = $ccsheet_details[$i]->counted_qty ? number_format($ccsheet_details[$i]->counted_qty, 2, ',', '') : 0;
                $locations[@$ccsheet_details[$i]->location->name] = @$ccsheet_details[$i]->location->name;
                if (@$ccsheet_details[$i]->qty > 0 || @$ccsheet_details[$i]->ordered > 0 || $counted_qty > 0) {
                    $ccsheet_details[$i]->delete_val = 0;
                } else {
                    $ccsheet_details[$i]->delete_val = 1;
                }
            }
            $data['ccsheet_details'] = $ccsheet_details;
            $data['locations']       = Location::orderBy('name', 'asc')->where('warehouse_id', '=', $ccsheet_results->whs_id)->pluck('name', 'id');
            return $data;
        }
    }

    /**
     *    create ccsheet details from warehouse inventory table
     *    @param ccsheeid integer
     *    @return
     */
    public static function getProductFromWarehouse($ccsheetid)
    {
        $ccsheet = CCSheet::where('id', '=', $ccsheetid)->first();
        if ($ccsheet) {
            $warehouse_id = $ccsheet->whs_id;
            if ($warehouse_id) {
                $warehouse_inventory_details = WarehouseInventory::select('whs_inventory.*')->selectRaw('product.product_number, product.description,product.unit, product.vendor_price,product.curr_iso_name');
                $warehouse_inventory_details->leftjoin('product', 'product.id', '=', 'whs_inventory.product_id');
                $warehouse_inventory_details->where('warehouse_id', '=', $warehouse_id);
                $warehouse_inventory_details = $warehouse_inventory_details->with('product', 'product.supplier')->get();
                $language                    = Session::get('language') ? Session::get('language') : 'no';
                $units                       = DropdownHelper::where("groupcode", "=", "010")->where('language', '=', $language)->orderby("keycode", "asc")->pluck("label", "keycode");
                $currency_list               = DropdownHelper::where('language', $language)->where('groupcode', '015')->orderBy('keycode', 'asc')->pluck('label', 'keycode')->toArray();
                if ($warehouse_inventory_details) {
                    foreach ($warehouse_inventory_details as $key => $value) {
                        $input['ccsheet_id']     = $ccsheetid;
                        $input['inv_id']         = $value->id;
                        $input['location_id']    = $value->location_id;
                        $input['product_id']     = $value->product_id;
                        $input['on_stock_qty']   = $value->qty;
                        $input['product_number'] = @$value->product->product_number ? $value->product->product_number : '';
                        $input['nobb']           = "";
                        $input['description']    = @$value->product->description ? $value->product->description : '';
                        $input['unit']           = @$value->product->unit ? $units[$value->product->unit] : '';
                        $input['curr_iso']       = @$value->product && @$value->product->supplier ? $value->product->supplier->curr_iso_name : '';
                        $input['vendor_price']   = @$value->product->vendor_price ? $value->product->vendor_price : '';
                        $input['added_by']       = Session::get('currentUserID');
                        $input['added_at']       = date('Y-m.d h:i:s');
                        CCSheetDetails::create($input);
                    }
                }
            }
        }
    }

    /**
     *    Update counted
     *    @param data array
     *    @return object
     */
    public static function updateCounted($data)
    {
        try {
            if ($data) {
                if ($data['recount'] == 1) {
                    // if data is recount then we will set the recount details
                    $ccsheet_details = CCSheetDetails::where('id', '=', $data['id'])->update(['counted_qty' => $data['counted'], 'recounted_by' => $data['counted_by'], 'recounted_at' => date('Y-m-d H:i:s')]);
                } else {
                    // if data is not recount then we will set the counted details
                    $ccsheet_details = CCSheetDetails::where('id', '=', $data['id'])->update(['counted_qty' => $data['counted'], 'counted_by' => $data['counted_by'], 'counted_at' => date('Y-m-d H:i:s')]);
                }
                return $ccsheet_details;
            }
        } catch (Exception $e) {

        }
    }

    /**
     *    create recounted
     *    @param ccsheetid int
     *    @return object
     */
    public static function createRecount($ccsheetid)
    {
        try {
            if ($ccsheetid) {
                $ccsheet = CCSheet::where('id', '=', $ccsheetid)->first();
                if ($ccsheet) {
                    $input['recount_of'] = $ccsheetid;
                    $input['created_by'] = Session::get('currentUserID');
                    $input['status']     = 1;
                    $input['whs_id']     = $ccsheet->whs_id;
                    $data                = CCSheet::create($input);
                    $ccsheet             = CCSheetDetails::insertRecountCCSheetData($ccsheetid, $data->id);
                    return $data->id;
                }

            }
        } catch (Exception $e) {

        }
    }

    /**
     *    insert recounted data in ccsheet details
     *    @param exist_ccsheetid int
     *    @param ccsheet_id int
     *    @return object
     */
    public static function insertRecountCCSheetData($exist_ccsheetid, $ccsheet_id = false)
    {
        try {
            if ($exist_ccsheetid) {
                $ccsheet_result = CCSheetDetails::where('ccsheet_id', '=', $exist_ccsheetid)->get();
                if ($ccsheet_result) {
                    foreach ($ccsheet_result as $key => $value) {
                        $input['ccsheet_id']     = $ccsheet_id;
                        $input['inv_id']         = $value->inv_id;
                        $input['location_id']    = $value->location_id;
                        $input['product_id']     = $value->product_id;
                        $input['on_stock_qty']   = $value->on_stock_qty;
                        $input['counted_qty']    = @$value->counted_qty;
                        $input['mismatched']     = ($value->on_stock_qty == $value->counted_qty ? 0 : 1);
                        $input['counted_by']     = $value->counted_by;
                        $input['counted_at']     = $value->counted_at;
                        $input['product_number'] = $value->product_number;
                        $input['nobb']           = "";
                        $input['description']    = $value->description;
                        $input['unit']           = $value->unit;
                        $input['curr_iso']       = $value->curr_iso;
                        $input['vendor_price']   = $value->vendor_price;
                        $input['added_by']       = Session::get('currentUserID');
                        $input['added_at']       = date('Y-m.d h:i:s');
                        CCSheetDetails::create($input);
                    }
                }
            }
        } catch (Exception $e) {

        }
    }

    /**
     *   Get mismatched from ccsheet_details
     *   @param ccsheet_id int
     *   @return product_details object
     **/
    public static function getMismatchedProducts($ccsheet_id)
    {
        $mismatch_products = CCSheetDetails::select(DB::Raw('sum(ifnull(counted_qty, 0) - ifnull(on_stock_qty, 0)) as qty'), 'product_number', 'product_id', 'location_id', 'inv_id')->where('ccsheet_id', '=', $ccsheet_id)->whereRaw('(ccsheet_details.on_stock_qty != ccsheet_details.counted_qty or ccsheet_details.counted_qty IS NULL)')->groupBy('product_id', 'location_id')->orderBy('product_number', 'asc')->with('location', 'product')->get();
        foreach ($mismatch_products as $key => $value) {
            $value->product_text = @$value->product->product_number.' - '.@$value->product->description.' - '.@$value->product->nobb;
        }
        return $mismatch_products;
    }

    public static function updateCCsheetDetails($ccsheetid)
    {
        if ($ccsheetid) {
            $data            = CCSheetDetails::getOrCreateCCSheetDetails($ccsheetid);
            $ccsheet_details = $data['ccsheet_details'];
            $ccsheet_results = CCSheet::where('id', '=', $ccsheetid)->first();
            for ($i = 0; $i < count($ccsheet_details); $i++) {
                if (@$ccsheet_details[$i]->delete_val == 1 && $ccsheet_results->recount_of == '') {
                    CCSheetDetails::where('id', '=', @$ccsheet_details[$i]->ccsheet_detail_id)->delete();
                } else {
                    $qty = @$ccsheet_details[$i]->qty ? @$ccsheet_details[$i]->qty : '0.00';
                    CCSheetDetails::where('id', '=', @$ccsheet_details[$i]->ccsheet_detail_id)->update(['on_stock_qty' => $qty]);
                }
            }
        }
    }
}

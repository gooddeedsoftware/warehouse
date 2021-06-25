<?php

namespace App\Models;

use App\Helpers\GanticHelper;
use App\Models\AccPlan;
use App\Models\Customer;
use App\Models\ProductLocation;
use App\Models\WarehouseInventory;
use config;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use PHPExcel_Style_Alignment;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Session;

class Product extends Model
{

    protected $table     = 'product';
    public $timestamps   = true;
    public $incrementing = false;
    use SoftDeletes;
    use Sortable;

    protected $dates    = ['deleted_at'];
    protected $fillable = array('id', 'product_number', 'warehouse', 'description', 'deleted_at', 'store_product_id', 'modell', 'supplier_id', 'unit', 'order_qty', 'ean', 'nobb', 'nrf', 'sale_price', 'vendor_price', 'tax', 'discount', 'acc_plan_id', 'curr_iso_name', 'list_price', 'calculated_vendor_price', 'is_package', 'costs', 'cost_factor', 'profit_percentage', 'profit', 'sale_price_with_vat',
        'dg', 'stockable', 'vendor_price_nok', 'cost_price', 'uni_id', 'product_group');

    protected $sortable = array('product_number', 'description', 'sale_price', 'vendor_price', 'acc_plan_id');

    /**
     * [user description]
     * @return [type] [description]
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'userid', 'id');
    }

    /**
     * [acc_plan description]
     * @return [type] [description]
     */
    public function acc_plan()
    {
        return $this->belongsTo('App\Models\AccPlan', 'acc_plan_id', 'id');
    }

    public function supplier()
    {
        return $this->hasOne('App\Models\ProductSupplier', 'product_id', 'id')->where('is_main', 1);
    }

    // List products (search, sort)
    public static function getProducts($conditions = false, $orderby = 'product_number', $order = 'asc', $supplier_id = false)
    {
        $product = Product::with('supplier', 'acc_plan');
        if ($supplier_id) {
            $product->whereHas('supplier', function ($query) use ($supplier_id) {
                $query->where('product_supplier.supplier', $supplier_id);
            });
        }
        if (isset($conditions['search']) && $conditions['search'] != '') {
            $search = $conditions['search'];
            $product->where(function ($query) use ($search) {
                $query->orwhere('product_number', 'LIKE', '%' . $search . '%');
                $query->orwhere('sale_price', 'LIKE', '%' . str_replace(",", ".", $search) . '%');
                $query->orwhere('vendor_price', 'LIKE', '%' . str_replace(",", ".", $search) . '%');
                $query->orwhere('description', 'LIKE', '%' . $search . '%');
                $query->orwhere('nobb', 'LIKE', '%' . $search . '%');
                $query->orwhereHas('acc_plan', function ($query) use ($search) {
                    $query->where('acc_plan.AccountNo', 'LIKE', '%' . $search . '%');
                });
            });
        }
        $product->where('is_package', '!=', 1);
        $paginate_size = Session::get('paginate_size') ? Session::get('paginate_size') : 10;
        $product       = $product->sortable(['product_number' => 'desc'])->paginate($paginate_size);
        return $product;
    }

    /**
     * [supplierSortable description]
     * @param  [type] $query     [description]
     * @param  [type] $direction [description]
     * @return [type]            [description]
     */
    public function supplierSortable($query, $direction)
    {
        $query->orderby('customer.name', $direction);
    }

    public function accountSortable($query, $direction)
    {
        $query->orderby('acc_plan.AccountNo', $direction);
    }

    public function customerNoSortable($query, $direction)
    {
        $query->orderby('customer.customer', $direction);
    }

    //get single product details(for edit)
    public static function getProductDetail($id, $order_type = false, $warehouse_id = false)
    {
        try {
            if ($id) {
                if ($order_type == "1") {
                    $product                = Product::selectRaw('product .*, sum(whs_inventory.qty)')->where('product.id', '=', $id)->where('warehouse_id', '=', $warehouse_id)->leftjoin('whs_inventory', 'product.id', '=', 'whs_inventory.product_id')->first();
                    $location_details       = WarehouseInventory::select(DB::Raw("CONCAT(whs_location.name , '(', replace(replace(FORMAT(SUM(if(whs_inventory.ordered is not null, whs_inventory.qty - whs_inventory.ordered, whs_inventory.qty)),2),  ',', ''), '.', ','), ')') AS NAME, whs_inventory.location_id AS ID"))->leftjoin('whs_location', 'location_id', '=', 'whs_location.id')->leftjoin('product', 'whs_inventory.product_id', '=', 'product.id')->where('whs_inventory.product_id', '=', $id)->where('whs_inventory.warehouse_id', '=', $warehouse_id)->groupBy('location_id')->get();
                    $location_details_array = array();
                    $sn_required            = 0;
                    if ($location_details) {
                        foreach ($location_details as $key => $value) {
                            $name                               = $value->NAME;
                            $location_details_array[$value->ID] = $name;
                        }
                        $product_details = Product::where('id', '=', $id)->first();
                    } else {
                        $product_details = Product::where('id', '=', $id)->first();
                    }
                    $location_array = array('sn_required' => $sn_required, 'serial_numbers' => $location_details);
                    return $location_array;
                } else if ($order_type == "2") {
                    $warehouse_locations = Location::where('warehouse_id', '=', $warehouse_id)->pluck('name', 'id');
                    foreach ($warehouse_locations as $key => $value) {
                        $whs_inventory = WarehouseInventory::where('warehouse_id', $warehouse_id)->where('location_id', $key)->where('product_id', $id)->first();
                        if (@$whs_inventory) {
                            $qty                       = $whs_inventory->qty - $whs_inventory->ordered;
                            $warehouse_locations[$key] = $value . '(' . number_format($qty, '2', ',', '') . ')';
                        } else {
                            $warehouse_locations[$key] = $value . '(0,00)';
                        }
                    }
                    return array('warehouse_locations' => $warehouse_locations);
                } else {
                    $product                      = Product::whereId($id)->first();
                    $product->sale_price          = Number_format($product->sale_price, "2", ",", "");
                    $product->list_price          = Number_format($product->list_price, "2", ",", "");
                    $product->vendor_price        = Number_format($product->vendor_price, "2", ",", "");
                    $product->tax                 = Number_format($product->tax, "2", ",", "");
                    $product->costs               = Number_format($product->costs, "2", ",", "");
                    $product->discount            = Number_format($product->discount, "2", ",", "");
                    $product->cost_factor         = Number_format($product->cost_factor, "2", ",", "");
                    $product->profit_percentage   = Number_format($product->profit_percentage, "2", ",", "");
                    $product->profit              = Number_format($product->profit, "2", ",", "");
                    $product->sale_price_with_vat = Number_format($product->sale_price_with_vat, "2", ",", "");
                    $product->dg                  = Number_format($product->dg, "2", ",", "");
                    $product->vendor_price_nok    = Number_format($product->vendor_price_nok, "2", ",", "");
                    $product->cost_price          = Number_format($product->cost_price, "2", ",", "");
                }
                return $product;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * [createOrEditProductData description]
     * @param  boolean $id   [description]
     * @param  boolean $type [description]
     * @return [type]        [description]
     */
    public static function createOrEditProductData($id = false, $type = false)
    {
        $data['accplans'] = AccPlan::select(DB::Raw("concat(AccountNo, ' - ', IFNULL(Name,'')) AS name,id"))->orderBy('AccountNo', 'asc')->pluck('name', 'id');
        $groups           = ProductGroup::where('status', '=', '0');

        if ($type == 1) {
            $data['product_list'] = Product::select(DB::Raw("CONCAT(product_number, ' - ', IFNULL(description, '')) AS name, id"))->orderBy("product_number", "asc")->where('is_package', '=', 0)->pluck("name", "id");
        } else {
            $language                         = Session::get('language') ? Session::get('language') : 'no';
            $data['units']                    = DropdownHelper::where("groupcode", "=", "010")->where('language', '=', $language)->orderby("keycode", "asc")->pluck("label", "keycode");
            $data['currency_list']            = DropdownHelper::where('language', $language)->where('groupcode', '015')->orderBy('keycode', 'asc')->pluck('label', 'keycode')->toArray();
            $data['btn']                      = trans('main.create');
            $data['product']['curr_iso_name'] = 'NOK';
            $data['suppliers']                = Customer::where('is_supplier', '=', 1)->pluck('name', 'id');
        }
        $data['stockable'] = 1;
        if ($id) {
            $data['product']          = Product::getProductDetail($id);
            $groups                   = $groups->orWhere('id', @$data['product']->product_group);
            $data['stockable']        = $data['product']->stockable;
            $data['productSuppliers'] = ProductSupplier::whereProduct_id($id)->orderBy('created_at', 'desc')->get();
            $data['productLocations'] = ProductLocation::whereProduct_id($id)->orderBy('created_at', 'desc')->get();
        }
        $data['groups']     = $groups->pluck('name', 'id');
        $data['warehouses'] = Warehouse::orderBy('shortname', 'asc')->pluck('shortname', 'id');
        $data['locations']  = Location::orderBy('name', 'asc')->pluck('name', 'id');
        return $data;
    }

    /**
     * [getProductDetailsFromOrderType description]
     * @param  boolean $order_type   [description]
     * @param  boolean $supplier_id  [description]
     * @param  boolean $warehouse_id [description]
     * @return [type]                [description]
     */
    public static function getProductDetailsFromOrderType($order_type = false, $supplier_id = false, $warehouse_id = false)
    {
        try {
            if ($order_type == "1" || $order_type == "2") {
                return Product::productDetails();
            } else if ($order_type == "3") {
                $productIds      = ProductSupplier::where('supplier', $supplier_id)->pluck('product_id');
                $product_details = Product::select(DB::Raw("CONCAT(product_number, ' - ', IFNULL(description, '')) AS product_number, id"));
                $product_details->whereIn('id', $productIds);
                $product_details = $product_details->where('is_package', '!=', 1)->orderBy("product_number")->pluck("product_number", "id");
                return $product_details;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    // get products
    public static function productDetails($field_name = false, $field_value = false)
    {
        try {
            $product_details = Product::select(DB::Raw("CONCAT(product_number, ' - ', IFNULL(description, '')) AS product_number, id"));
            if ($field_name && $field_value) {
                $product_details->where($field_name, "=", $field_value);
            }
            $product_details = $product_details->where('is_package', '!=', 1)->orderBy("product_number")->pluck("product_number", "id");
            return $product_details;
        } catch (Exception $e) {
            return false;
        }
    }

    // retrieve product from inventory table
    public static function retrieveProductsFromInventory($warehouse_id = false)
    {
        try {
            if ($warehouse_id) {
                $product_details = WarehouseInventory::select(DB::Raw("CONCAT(product_number, ' - ', IFNULL(description, '')) AS product_number, whs_inventory.product_id as id"))->leftjoin('product', 'product_id', '=', 'product.id')->where('warehouse_id', '=', $warehouse_id)->groupBy('whs_inventory.product_id')->pluck("product_number", "id");
                return $product_details;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    // retrieve product from inventory table
    public static function getQuantityFromLocationDetail($location_id = false, $product_id = false)
    {
        try {
            if ($location_id) {
                $product_details = WarehouseInventory::where('location_id', '=', $location_id)->where('product_id', '=', $product_id)->first();
                if ($product_details) {
                    $product_details->qty = number_format($product_details->qty, "0");
                }

                return $product_details;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getActivities($id)
    {
        if ($id) {
            $activity_detail = Product::where('id', '=', $id)->with('acc_plan')->first();
            return $activity_detail;
        }
    }

    public static function getActivitiesFromOrderMaterial($material_ids)
    {
        if ($material_ids) {
            $material_id     = explode(',', $material_ids);
            $activity_detail = Product::leftjoin('order_material', 'order_material.product_number', '=', 'product.id')->whereIN('order_material.id', $material_id)->with('acc_plan')->first();
            return $activity_detail;
        }
    }

    /**
     * [retrieveAllProductsFromInventory description]
     * @param  boolean $warehouse_id [description]
     * @return [type]                [description]
     */
    public static function retrieveAllProductsFromInventory($type = false)
    {
        try {
            $product = Product::select(DB::Raw("product.*, CONCAT(product_number, ' - ', IFNULL(description, '')) AS product_text"))->orderBy('product_number', 'asc');
            if ($type == 1) {
                $product->where('is_package', '=', 0)->orWhere(function ($query) {
                    $query->where('is_package', '=', 1);
                });
            } else {
                $product->where('is_package', '=', 0);
            }
            $products = $product->with('supplier')->get();
            foreach ($products as $key => $value) {
                if ($value->supplier) {
                    $value->product_text = $value->product_text . ' - ' . @$value->supplier->articlenumber;
                }
            }
            return $products->pluck('product_text', 'id')->toArray();
        } catch (Exception $e) {
            echo $e;die;
            return false;
        }
    }

    /**
     * [allProducts description]
     * @return [type] [description]
     */
    public static function allProducts()
    {
        try {
            return Product::select(DB::Raw("product.*, CONCAT(product_number, ' - ', IFNULL(description, '')) AS product_text"))->orderBy('product_number', 'asc')->pluck('product_text', 'product.id')->toArray();
        } catch (Exception $e) {
            echo $e;die;
            return false;
        }
    }

    /**
     * [retrieveAllProductPackagesFromInventory description]
     * @return [type] [description]
     */
    public static function retrieveAllProductPackagesFromInventory()
    {
        try {
            $product_details = Product::select(DB::Raw("CONCAT(product_number, ' - ', IFNULL(description, '')) AS name, id"))->orderBy("product_number", "asc")->where('is_package', '=', 1)->pluck("name", "id");
            return $product_details;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * checkProductNumberUnique
     * @param  string $product_number [description]
     * @return JSON
     */
    public static function checkProductNumberUnique($product_number = false)
    {
        if ($product_number) {
            $product_result = Product::where('product_number', '=', $product_number)->first();
            if (count($product_result)) {
                return json_encode(array("status" => "success", "message" => trans('main.product.product_already_exist')));
            } else {
                return json_encode(array("status" => "error", "message" => ""));
            }

        } else {
            return json_encode(array("status" => "error", "message" => "Enter Product Number"));
        }
    }

    /**
     * [getProductsId description]
     * @return [type] [description]
     */
    public static function getProductsId()
    {
        $product_id_array = array();
        $product_result   = Product::select('id')->where('is_package', '!=', 1)->get();
        foreach ($product_result as $key => $value) {
            $product_id_array[] = $value->id;
        }
        return $product_id_array;
    }

    public static function getProductNumber()
    {
        $product_number = 0;
        $result         = Product::select(DB::raw("MAX(product_number)  as product_number"))->where('is_package', '=', 0)->first();
        if (isset($result->product_number)) {
            $product_number = (int) $result->product_number;
            $product_number = sprintf('%03d', $product_number + 1);
        } else {
            $product_number = "200000";
        }
        return $product_number;
    }

    /**
     * [exportProductXL description]
     * @return [type] [description]
     */
    public static function exportProductXL()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $ids = Product::select('id')->where('is_package', '!=', 1)->pluck('id');
        try {
            $product     = Product::productData($ids);
            $objPHPExcel = new Spreadsheet();
            $objPHPExcel->getProperties()
                ->setCreator("Gantic")
                ->setLastModifiedBy("Gantic")
                ->setTitle("Product Report")
                ->setSubject("Product Report")
                ->setDescription("Product Report")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Product Report");

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', "Varenr")
                ->setCellValue('B1', "Gruppe")
                ->setCellValue('C1', "Kontonr")
                ->setCellValue('D1', "Lev.varenr")
                ->setCellValue('E1', "Leverandør pris nok")
                ->setCellValue('F1', "Kost")
                ->setCellValue('G1', "Kostprisfaktor")
                ->setCellValue('H1', "Kostpris")
                ->setCellValue('I1', "Avanse%")
                ->setCellValue('J1', "Avanse")
                ->setCellValue('K1', "Salgspris")
                ->setCellValue('L1', "MVA%")
                ->setCellValue('M1', "Salgspris m/MVA")
                ->setCellValue('N1', "DG")
                ->setCellValue('O1', "Lagerført")
                ->setCellValue('P1', "Beskrivelse")
                ->setCellValue('Q1', "Enhet")
                ->setCellValue('R1', "Leverandør")
                ->setCellValue('S1', "Lev.varenr")
                ->setCellValue('T1', "Varenavn")
                ->setCellValue('U1', "Lev.pris")
                ->setCellValue('V1', "Lev.rabatt")
                ->setCellValue('W1', "Rabattert")
                ->setCellValue('X1', "Frakttillegg%")
                ->setCellValue('Y1', "Annet%")
                ->setCellValue('Z1', "Lev.valuta");
            $i = 2;
            foreach ($product as $key => $value) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, @$value['Varenr'])
                    ->setCellValue('B' . $i, @$value['Gruppe'])
                    ->setCellValue('C' . $i, @$value['Kontonr'])
                    ->setCellValue('D' . $i, @$value['Levvarenr'])
                    ->setCellValue('E' . $i, number_format($value['Leverandørprisnok'], 2, ',', ''))
                    ->setCellValue('F' . $i, number_format($value['costs'], 2, ',', ''))
                    ->setCellValue('G' . $i, number_format($value['Kostprisfaktor'], 2, ',', ''))
                    ->setCellValue('H' . $i, number_format($value['Kostpris'], 2, ',', ''))
                    ->setCellValue('I' . $i, number_format($value['Avanse%'], 2, ',', ''))
                    ->setCellValue('J' . $i, number_format($value['Avanse'], 2, ',', ''))
                    ->setCellValue('K' . $i, number_format($value['Salgspris'], 2, ',', ''))
                    ->setCellValue('L' . $i, number_format($value['MVA%'], 2, ',', ''))
                    ->setCellValue('M' . $i, number_format($value['SalgsprisMVA'], 2, ',', ''))
                    ->setCellValue('N' . $i, number_format($value['DG'], 2, ',', ''))
                    ->setCellValue('O' . $i, $value['Lagerført'])
                    ->setCellValue('P' . $i, @$value['Beskrivelse'])
                    ->setCellValue('Q' . $i, @$value['Enhet'])
                    ->setCellValue('R' . $i, @$value['Leverandør'])
                    ->setCellValue('S' . $i, @$value['Lev.varenr'])
                    ->setCellValue('T' . $i, @$value['Varenavn'])
                    ->setCellValue('U' . $i, number_format((float) @$value['Lev.pris'], 2, ',', ''))
                    ->setCellValue('V' . $i, number_format((float) @$value['Lev.rabatt'], 2, ',', ''))
                    ->setCellValue('W' . $i, number_format((float) @$value['Rabattert'], 2, ',', ''))
                    ->setCellValue('X' . $i, number_format((float) @$value['Frakttillegg%'], 2, ',', ''))
                    ->setCellValue('Y' . $i, number_format((float) @$value['Annet%'], 2, ',', ''))
                    ->setCellValue('Z' . $i, @$value['Lev.valuta']);
                $objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . $i . ':' . 'E' . $i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $objPHPExcel->setActiveSheetIndex(0)->getStyle('F' . $i . ':' . 'O' . $i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $objPHPExcel->setActiveSheetIndex(0)->getStyle('R' . $i . ':' . 'V' . $i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $objPHPExcel->setActiveSheetIndex(0)->getStyle('W' . $i . ':' . 'Z' . $i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $i++;
            }
            $objPHPExcel->getActiveSheet(0)->getStyle('A1:Z1')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setTitle('Report');
            $objPHPExcel->setActiveSheetIndex(0);
            $writer = new Xlsx($objPHPExcel);
            $file   = GanticHelper::createTempFile("xlsx");
            $writer->save($file);
            ob_end_clean();
            $headers  = config::get("constants.HEADER_FOR_XLSX");
            $fileName = "Product.xlsx";
            return array('filePath' => $file, 'headers' => $headers, 'fileName' => $fileName);
        } catch (\Exception $e) {
            echo $e;die;
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            GanticHelper::errorLog($error_message);
            return null;
        }
    }

    public static function importProductFromExcel($data)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        if ($data) {
            $i = 0;
            foreach ($data as $key => $value) {
                if ($i != 0) {
                    try {
                        $product_array                     = array();
                        $product_number                    = $value['0'];
                        $group_details                     = ProductGroup::where('name', $value['1'])->first();
                        $accound_details                   = AccPlan::where('AccountNo', $value['2'])->first();
                        $unit_details                      = DropdownHelper::where("groupcode", "=", "010")->where('label', '=', $value['16'])->first();
                        $product_array['product_group']    = @$group_details ? $group_details->id : '';
                        $product_array['unit']             = @$unit_details ? $unit_details->keycode : '';
                        $product_array['acc_plan_id']      = @$accound_details ? $accound_details->id : '';
                        $product_array['nobb']             = $value['3'];
                        $product_array['description']      = $value['15'];
                        $product_array['stockable']        = $value['14'] && $value['14'] != "" ? $value['14'] : 0;
                        $product_array['vendor_price_nok'] = $product_array['vendor_price'] = isset($value['4']) && $value['4'] != "" ? str_replace(",", ".", $value['4']) : 0;
                        $product_array['costs']            = isset($value['5']) && $value['5'] != "" ? str_replace(",", ".", $value['5']) : 0;
                        $product_array['cost_factor']      = isset($value['6']) && $value['6'] != "" ? str_replace(",", ".", $value['6']) : 0;

                        if ($value['7'] == "" || str_replace(",", ".", $value['7']) == 0) {
                            $product_array['cost_price'] = ($product_array['vendor_price_nok'] + $product_array['costs']) * (100 + $product_array['cost_factor']) / 100;
                        } else {
                            $product_array['cost_price'] = isset($value['7']) ? str_replace(",", ".", $value['7']) : 0;
                        }
                        $product_array['profit_percentage'] = isset($value['8']) ? str_replace(",", ".", $value['8']) : 0;
                        if ($value['9'] == "" || str_replace(",", ".", $value['9']) == 0) {
                            $product_array['profit'] = $product_array['profit_percentage'] * $product_array['cost_price'] / 100;
                        } else {
                            $product_array['profit'] = isset($value['9']) ? str_replace(",", ".", $value['9']) : 0;
                        }

                        if ($value['10'] == "" || str_replace(",", ".", $value['10']) == 0) {
                            $product_array['sale_price'] = $product_array['profit'] + $product_array['cost_price'];
                        } else {
                            $product_array['sale_price'] = isset($value['10']) ? str_replace(",", ".", $value['10']) : 0;
                        }
                        $product_array['tax'] = isset($value['11']) ? str_replace(",", ".", $value['11']) : 0;

                        if ($value['12'] == "" || str_replace(",", ".", $value['12']) == 0) {
                            $product_array['sale_price_with_vat'] = $product_array['sale_price'] * (100 + $product_array['tax']) / 100;
                        } else {
                            $product_array['sale_price_with_vat'] = isset($value['12']) ? str_replace(",", ".", $value['12']) : 0;
                        }

                        if ($value['13'] == "" || str_replace(",", ".", $value['13']) == 0) {
                            $product_array['dg'] = $product_array['sale_price'] > 0 ? ($product_array['sale_price'] - $product_array['cost_price']) / $product_array['sale_price'] * 100 : 0.00;
                        } else {
                            $product_array['dg'] = isset($value['13']) ? str_replace(",", ".", $value['13']) : 0;
                        }
                        $product_number = sprintf("%03d", $product_number);
                        $product        = Product::where('product_number', $product_number)->first();
                        if ($product) {
                            $product->fill($product_array);
                            $product->save();
                        } else {
                            $product_array['id']             = GanticHelper::gen_uuid();
                            $product_array['product_number'] = @$product_number && @$product_number != 0 ? $product_number : Product::getProductNumber();
                            $product                         = Product::create($product_array);
                        }
                        // update Supplier
                        $supplier_data                      = array();
                        $supplier_data['product_id']        = $product['id'];
                        $supplier_details                   = Customer::where('name', $value['17'])->first();
                        $supplier_data['supplier']          = @$supplier_details->id;
                        $supplier_data['articlenumber']     = $value['18'];
                        $supplier_data['articlename']       = $value['19'];
                        $supplier_data['curr_iso_name']     = isset($value['25']) && $value['25'] != '' ? $value['25'] : "NOK";
                        $supplier_data['supplier_price']    = isset($value['20']) && $value['20'] != '' ? str_replace(",", ".", $value['20']) : 0;
                        $supplier_data['supplier_discount'] = isset($value['21']) && $value['21'] != '' ? str_replace(",", ".", $value['21']) : 0;
                        $supplier_data['discount']          = isset($value['22']) && $value['22'] != '' ? str_replace(",", ".", $value['22']) : 0;
                        $supplier_data['addon']             = isset($value['23']) && $value['23'] != '' ? str_replace(",", ".", $value['23']) : 0;
                        $supplier_data['other']             = isset($value['24']) && $value['24'] != '' ? str_replace(",", ".", $value['24']) : 0;
                        $supplier_data['realcost']          = $supplier_data['discount'] * (100 + $supplier_data['addon'] + $supplier_data['other']) / 100;
                        $supplier_data['realcost_nok']      = $supplier_data['realcost'];
                        if ($supplier_data['curr_iso_name'] && $supplier_data['curr_iso_name'] != 'NOK') {
                            $currencyDetails = Currency::whereRaw('valid_from IN (SELECT max(valid_from) FROM currency GROUP BY curr_iso_name)')->where('curr_iso_name', '=', $supplier_data['curr_iso_name'])->first();
                            if ($currencyDetails) {
                                $supplier_data['realcost_nok'] = @$currencyDetails->exch_rate * $supplier_data['realcost'];
                            }
                        }
                        if (@$supplier_data['supplier']) {
                            $supplier_data['is_main'] = 1;
                            ProductSupplier::where('product_id', $product['id'])->update(['is_main' => 0]);
                            $existData = ProductSupplier::where('product_id', $product['id'])->where('supplier', $supplier_data['supplier'])->first();
                            if ($existData) {
                                $existData->fill($supplier_data);
                                $existData->save();
                            } else {
                                ProductSupplier::create($supplier_data);
                            }
                        }
                    } catch (\Exception $e) {
                        $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
                        GanticHelper::errorLog($error_message);
                        return false;
                    }
                }
                $i++;
            }
        }
        return true;
    }
    /**
     *   get product data
     *   @param ids array
     *   @return array
     **/
    public static function productData($ids)
    {
        $product_data = Product::select('customer.name AS supplier_name', 'acc_plan.AccountNo AS account_number', 'product_group.name as group_name', 'product.*')->leftjoin('customer', 'customer.id', '=', 'product.supplier_id')->leftjoin('acc_plan', 'acc_plan.id', '=', 'product.acc_plan_id')->leftjoin('product_group', 'product_group.id', '=', 'product.product_group');
        if ($ids) {
            $product_data->whereIn('product.id', $ids);
        }
        $product_data       = $product_data->get();
        $language           = Session::get('language') ? Session::get('language') : 'no';
        $units              = DropdownHelper::where('groupcode', '=', '010')->where('language', $language)->pluck('label', 'keycode')->toArray();
        $product_data_array = array();
        foreach ($product_data as $key => $value) {
            $product_array                       = array();
            $product_array["Varenr"]             = $value->product_number;
            $product_array["Gruppe"]             = $value->group_name;
            $product_array["Kontonr"]            = $value->account_number ? $value->account_number : '';
            $product_array["Levvarenr"]          = $value->nobb;
            $product_array["Valuta"]             = $value->curr_iso_name;
            $product_array["Leverandørpris"]    = $value->vendor_price;
            $product_array["Leverandørprisnok"] = $value->vendor_price_nok;
            $product_array["costs"]              = $value->costs;
            $product_array["Kostprisfaktor"]     = $value->cost_factor;
            $product_array["Kostpris"]           = $value->cost_price;
            $product_array["Avanse%"]            = $value->profit_percentage;
            $product_array["Avanse"]             = $value->profit;
            $product_array["Salgspris"]          = $value->sale_price;
            $product_array["MVA%"]               = $value->tax;
            $product_array["SalgsprisMVA"]       = $value->sale_price_with_vat;
            $product_array["DG"]                 = $value->dg;
            $product_array["Lagerført"]         = $value->stockable;
            $product_array["Beskrivelse"]        = $value->description;
            $product_array["Enhet"]              = @$units[@$value->unit];
            $supplier_data                       = ProductSupplier::select('customer.name as supplier_name', 'product_supplier.*')->where('product_id', $value->id)->leftjoin('customer', 'customer.id', '=', 'product_supplier.supplier')->where('is_main', 1)->first();
            $product_array["Leverandør"]        = @$supplier_data->supplier ? $supplier_data->supplier_name : '';
            $product_array["Lev.varenr"]         = @$supplier_data->articlenumber ? $supplier_data->articlenumber : '';
            $product_array["Varenavn"]           = @$supplier_data->articlename ? $supplier_data->articlename : '';
            $product_array["Lev.pris"]           = @$supplier_data->supplier_price ? $supplier_data->supplier_price : '';
            $product_array["Lev.rabatt"]         = @$supplier_data->supplier_discount ? $supplier_data->supplier_discount : '';
            $product_array["Rabattert"]          = @$supplier_data->discount ? $supplier_data->discount : '';
            $product_array["Frakttillegg%"]      = @$supplier_data->addon ? $supplier_data->addon : '';
            $product_array["Annet%"]             = @$supplier_data->other ? $supplier_data->other : '';
            $product_array["Lev.valuta"]         = @$supplier_data->curr_iso_name ? $supplier_data->curr_iso_name : '';
            $product_data_array[]                = $product_array;
        }
        return $product_data_array;
    }

    /**
     * [updateProductPrice description]
     * @return [type] [description]
     */
    public static function updateProductPrice()
    {

        $product_suppliers = ProductSupplier::get();
        foreach ($product_suppliers as $key => $value) {
            $supplier_array = [];
            $exch_rate      = 1;
            if ($value->curr_iso_name != 'NOK') {
                $currencyDetails = Currency::whereRaw('valid_from IN (SELECT max(valid_from) FROM currency GROUP BY curr_iso_name)')->where('curr_iso_name', '=', $value->curr_iso_name)->first();
                if ($currencyDetails) {
                    $exch_rate = @$currencyDetails->exch_rate;
                }
            }
            $real_cost_nok = $exch_rate * $value->realcost;
            ProductSupplier::whereId($value->id)->update(['realcost_nok' => $real_cost_nok]);
            if ($value->is_main == 1) {
                $product_detail                       = Product::whereId($value->product_id)->first();
                $product_array                        = array();
                $product_array['vendor_price']        = $real_cost_nok;
                $product_array['costs']               = Number_format($product_detail->costs, "2", ".", "");
                $product_array['cost_factor']         = Number_format($product_detail->cost_factor, "2", ".", "");
                $product_array['profit_percentage']   = Number_format($product_detail->profit_percentage, "2", ".", "");
                $product_array['tax']                 = Number_format($product_detail->tax, "2", ".", "");
                $product_array['vendor_price_nok']    = $real_cost_nok;
                $product_array['cost_price']          = ((float) $product_array['vendor_price_nok'] + (float) $product_array['costs']) * (100 + (float) $product_array['cost_factor']) / 100;
                $product_array['cost_price']          = Number_format($product_array['cost_price'], "2", ".", "");
                $product_array['profit']              = (float) $product_array['profit_percentage'] * (float) $product_array['cost_price'] / 100;
                $product_array['profit']              = Number_format($product_array['profit'], "2", ".", "");
                $product_array['sale_price']          = (float) $product_array['profit'] + (float) $product_array['cost_price'];
                $product_array['sale_price']          = Number_format($product_array['sale_price'], "2", ".", "");
                $product_array['sale_price']          = round($product_array['sale_price']);
                $product_array['sale_price_with_vat'] = (float) $product_array['sale_price'] * (100 + (float) $product_array['tax']) / 100;
                $product_array['sale_price_with_vat'] = Number_format($product_array['sale_price_with_vat'], "2", ".", "");
                $product_array['dg']                  = $product_array['sale_price'] > 0 ? ((float) $product_array['sale_price'] - (float) $product_array['cost_price']) / (float) $product_array['sale_price'] * 100 : 0;
                $product_array['dg']                  = Number_format($product_array['dg'], "2", ".", "");
                $product_detail->fill($product_array);
                $product_detail->save();
            }

        }
        return true;
    }

    /**
     * [updateProductPricesBySupplier description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public static function updateProductPricesBySupplier($data)
    {
        if ($data) {
            $productDetails = Product::where('id', '=', $data['product_id'])->first();
            $grossMargin    = GrossMargin::where('product_group', '=', $productDetails->product_group)->where('supplier', $data['supplier'])->first();
            $grossMarginAmt = @$grossMargin ? $grossMargin->gross_margin : $productDetails->profit_percentage;
            $real_cost      = isset($data['realcost_nok']) ? str_replace(",", ".", $data['realcost_nok']) : 0.00;
            if ($grossMargin || $real_cost) {
                $product_array                        = array();
                $product_array['vendor_price']        = Number_format($real_cost, "2", ".", "");
                $product_array['costs']               = Number_format($productDetails->costs, "2", ".", "");
                $product_array['cost_factor']         = Number_format($productDetails->cost_factor, "2", ".", "");
                $product_array['profit_percentage']   = Number_format($grossMarginAmt, "2", ".", "");
                $product_array['tax']                 = Number_format($productDetails->tax, "2", ".", "");
                $product_array['vendor_price_nok']    = $product_array['vendor_price'];
                $product_array['cost_price']          = ((float) $product_array['vendor_price_nok'] + (float) $product_array['costs']) * (100 + (float) $product_array['cost_factor']) / 100;
                $product_array['cost_price']          = Number_format($product_array['cost_price'], "2", ".", "");
                $product_array['profit']              = (float) $product_array['profit_percentage'] * (float) $product_array['cost_price'] / 100;
                $product_array['profit']              = Number_format($product_array['profit'], "2", ".", "");
                $product_array['sale_price']          = (float) $product_array['profit'] + (float) $product_array['cost_price'];
                $product_array['sale_price']          = Number_format($product_array['sale_price'], "2", ".", "");
                $product_array['sale_price']          = round($product_array['sale_price']);
                $product_array['sale_price_with_vat'] = (float) $product_array['sale_price'] * (100 + (float) $product_array['tax']) / 100;
                $product_array['sale_price_with_vat'] = Number_format($product_array['sale_price_with_vat'], "2", ".", "");
                $product_array['dg']                  = $product_array['sale_price'] > 0 ? ((float) $product_array['sale_price'] - (float) $product_array['cost_price']) / (float) $product_array['sale_price'] * 100 : 0;
                $product_array['dg']                  = Number_format($product_array['dg'], "2", ".", "");
                $productRec                           = Product::find($productDetails->id);
                $productRec->fill($product_array);
                $productRec->save();
            }
        }
        return true;
    }

    /**
     * [getProductForSelect2 description]
     * @param  [type] $search_val [description]
     * @return [type]             [description]
     */
    public static function getProductForSelect2($search_val, $type)
    {

        if (@$search_val) {
            if ($type == 3) {
                $product = Product::select(\DB::Raw("product.*, CONCAT(product_number, ' - ', IFNULL(description, ''), IF(nobb is null, '', ' - '), IFNULL(nobb, '')) AS product_text"))->orderBy('product_number', 'asc');
                if (isset($search_val) && $search_val != '') {
                    $product->where(function ($query) use ($search_val) {
                        $query->orwhere('product_number', 'LIKE', '%' . $search_val . '%');
                        $query->orwhere('description', 'LIKE', '%' . $search_val . '%');
                        $query->orwhere('nobb', 'LIKE', '%' . $search_val . '%');
                    });
                }
                return $product->limit('15')->get();
            } else {
                $sql = "SELECT
                            *
                        FROM
                            (SELECT
                                CONCAT(p.p_name, IF(ps.article IS NOT NULL, ps.article, '')) AS product_text,
                                    p.id
                            FROM
                                (SELECT
                                CONCAT(product_number, '-', description) AS p_name, id
                            FROM
                                product
                            WHERE
                                product.is_package = 0
                                    AND product.deleted_at IS NULL
                                    AND (product_number LIKE '%$search_val%'
                                    OR description LIKE '%$search_val%')) AS p
                            LEFT JOIN (SELECT
                                product_id,
                                    IF(articlenumber IS NOT NULL, CONCAT(' - ', articlenumber), '') AS article
                            FROM
                                product_supplier
                            WHERE
                                product_supplier.is_main = 1
                                    AND product_supplier.deleted_at IS NULL) AS ps ON p.id = ps.product_id UNION ALL SELECT
                                CONCAT(p.p_name, IF(ps.article IS NOT NULL, ps.article, '')) AS product_text,
                                    p.id
                            FROM
                                (SELECT
                                CONCAT(product_number, '-', description) AS p_name, id
                            FROM
                                product
                            WHERE
                                product.is_package = 0
                                    AND product.deleted_at IS NULL) AS p
                            RIGHT JOIN (SELECT
                                product_id,
                                    IF(articlenumber IS NOT NULL, CONCAT(' - ', articlenumber), '') AS article
                            FROM
                                product_supplier
                            WHERE
                                product_supplier.is_main = 1
                                    AND product_supplier.deleted_at IS NULL
                                    AND articlenumber LIKE '%$search_val%') AS ps ON p.id = ps.product_id) AS products
                        GROUP BY product_text , id limit 15";
                $products = \DB::select($sql);
                $products = collect($products);
                if ($type == 2) {
                    $packages = Product::select(\DB::Raw('product.*, concat(if(product.product_number is null, "", concat(product.product_number, " - ")) ,if(product.product_number is null, "", product.product_number)) as product_text'))->where('is_package', '=', 1)->orderBy('product_number', 'asc')->get();
                    $merged   = $products->merge($packages);
                    return $merged;
                }
                return $products;
            }
        }
        return [];
    }
}

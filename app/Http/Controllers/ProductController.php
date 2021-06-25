<?php
namespace App\Http\Controllers;

use App\Helpers\GanticHelper;
use App\Http\Requests\ProductRequest;
use App\Models\Customer;
use App\Models\DropdownHelper;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductLocation;
use App\Models\ProductSupplier;
use App\Models\Warehouse;
use App\Models\WarehouseInventory;
use App\Models\WarehouseOrderDetails;
use Redirect;
use Request;
use Response;
use Session;

class ProductController extends Controller
{

    protected $folder    = 'product';
    protected $route     = 'main.product.index';
    protected $title     = 'main.product';
    protected $success   = 'success';
    protected $error     = 'error';
    protected $notfound  = 'main.notfound';
    protected $createmsg = 'main.product_createsuccess';
    protected $updatemsg = 'main.product_updatesuccess';
    protected $deletemsg = 'main.product_deletesuccess';
    protected $product   = 'main.product';
    private $productObj;
    private $productLocationObj;

    /**
     * [__construct description]
     * @param Product $productObj [description]
     */
    public function __construct(Product $productObj, ProductLocation $productLocationObj)
    {
        $this->productObj         = $productObj;
        $this->productLocationObj = $productLocationObj;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data = @Session::get('warehousedetails_product_search') ? @Session::get('warehousedetails_product_search') : [];
        @\Request::input() ? Session::put('warehousedetails_product_search', array_merge($data, @\Request::input())) : '';
        $data['suppliers'] = Customer::where('is_supplier', '=', 1)->pluck('name', 'id');
        $data['products']  = Product::getProducts(@Session::get('warehousedetails_product_search'), false, false, @Session::get('warehousedetails_product_search')['search_by_supplier']);
        return view('warehousedetails/products/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('warehousedetails/products/partials/_form', Product::createOrEditProductData());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(ProductRequest $request)
    {
        $input                       = $request->all();
        $input['id']                 = GanticHelper::gen_uuid();
        $input['sale_price']         = isset($input['sale_price']) ? str_replace(",", ".", $input['sale_price']) : "";
        $input['vendor_price']       = isset($input['vendor_price']) ? str_replace(",", ".", $input['vendor_price']) : "";
        $input['costs']              = isset($input['costs']) ? str_replace(",", ".", $input['costs']) : "";
        $input['tax']                = isset($input['tax']) ? str_replace(",", ".", $input['tax']) : "";
        $input['cost_factor']        = isset($input['cost_factor']) ? str_replace(",", ".", $input['cost_factor']) : "";
        $input['cost_price']         = isset($input['cost_price']) ? str_replace(",", ".", $input['cost_price']) : "";
        $input['profit']             = isset($input['profit']) ? str_replace(",", ".", $input['profit']) : "";
        $input['profit_percent']     = isset($input['profit_percent']) ? str_replace(",", ".", $input['profit_percent']) : "";
        $input['sale_price_inc_vat'] = isset($input['sale_price_inc_vat']) ? str_replace(",", ".", $input['sale_price_inc_vat']) : "";
        $input['dg']                 = isset($input['dg']) ? str_replace(",", ".", $input['dg']) : "";
        $input['vendor_price_nok']   = isset($input['vendor_price_nok']) ? str_replace(",", ".", $input['vendor_price_nok']) : "";
        $input['stockable']          = $request->get('stockable') ? 1 : 0;
        $input['product_number']     = Product::getProductNumber();
        $product                     = Product::create($input);
        return Redirect::action('ProductController@edit', [$product->id])->with($this->success, trans($this->createmsg));
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
        $data                = Product::createOrEditProductData($id);
        $data['btn']         = trans('main.update');
        $data['hide_delete'] = WarehouseOrderDetails::where('product_id', '=', $id)->get();
        if (!count($data['hide_delete'])) {
            $data['hide_delete'] = WarehouseInventory::where('product_id', '=', $id)->get();
        }
        return view('warehousedetails/products/partials/_form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(ProductRequest $request, $id)
    {
        $input                       = $request->all();
        $product                     = Product::find($id);
        $input['sale_price']         = isset($input['sale_price']) ? str_replace(",", ".", $input['sale_price']) : "";
        $input['vendor_price']       = isset($input['vendor_price']) ? str_replace(",", ".", $input['vendor_price']) : "";
        $input['costs']              = isset($input['costs']) ? str_replace(",", ".", $input['costs']) : "";
        $input['tax']                = isset($input['tax']) ? str_replace(",", ".", $input['tax']) : "";
        $input['cost_factor']        = isset($input['cost_factor']) ? str_replace(",", ".", $input['cost_factor']) : "";
        $input['cost_price']         = isset($input['cost_price']) ? str_replace(",", ".", $input['cost_price']) : "";
        $input['profit']             = isset($input['profit']) ? str_replace(",", ".", $input['profit']) : "";
        $input['profit_percent']     = isset($input['profit_percent']) ? str_replace(",", ".", $input['profit_percent']) : "";
        $input['sale_price_inc_vat'] = isset($input['sale_price_inc_vat']) ? str_replace(",", ".", $input['sale_price_inc_vat']) : "";
        $input['dg']                 = isset($input['dg']) ? str_replace(",", ".", $input['dg']) : "";
        $input['vendor_price_nok']   = isset($input['vendor_price_nok']) ? str_replace(",", ".", $input['vendor_price_nok']) : "";
        $input['stockable']          = $request->get('stockable') ? 1 : 0;
        $product->fill($input);
        $product->save();
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
        $product = Product::find($id);
        if (!$product) {
            return Redirect::back()->with($this->error, trans($this->notfound));
        }
        $product->delete();
        return Redirect::route($this->route)->with($this->success, trans($this->deletemsg));
    }

    // update approved product
    public function approveProducts()
    {
        $product_ids              = Request::get('approved_product_ids');
        $product_invoice_quantity = Request::get('approved_product_invoice_quantity');
        if ($product_ids) {
            Product::updateApproveProdcut($product_ids, $product_invoice_quantity);
        }
        return Redirect::back();
    }

    // get product detail from selected order type
    public function getProductDetailFromOrderType()
    {
        try {
            $order_type               = Request::get('order_type');
            $supplier_id              = Request::get("supplier_id");
            $warehouse_id             = Request::get("warehouse_id");
            $destination_warehouse_id = Request::get("destination_warehouse");
            if ($order_type) {
                $product_details   = Product::getProductDetailsFromOrderType($order_type, $supplier_id, $warehouse_id);
                $data['products']  = $product_details;
                $location_details  = Location::getLocationsForWarehourOrders($order_type, $warehouse_id);
                $data['locations'] = $location_details;
                if ($data) {
                    echo json_encode(array("status" => "success", "data" => $data));
                } else {
                    echo json_encode(array("status" => "error", "data" => "No data found"));
                }
            } else {
                echo json_encode(array("status" => "error", "data" => "No data found"));
            }
        } catch (Exception $e) {
            echo json_encode(array("status" => "error", "data" => "Something went wrong"));
        }
    }

    // get single product details
    public function getProductDetailFromId($id = false)
    {
        try {
            $product_id   = $id ? $id : Request::get('product_id');
            $order_type   = Request::get('order_type');
            $warehouse_id = Request::get('warehouse_id');
            if ($product_id) {
                $product_details = Product::getProductDetail($product_id, $order_type, $warehouse_id);
                if ($product_details) {
                    echo json_encode(array("status" => "success", "data" => $product_details));
                } else {
                    echo json_encode(array("status" => "error", "data" => "No data found"));
                }
            }
        } catch (Exception $e) {
            echo json_encode(array("status" => "error", "data" => "Something went wrong"));
        }

    }

    // get quantity from inventory
    public function getQuantityFromProduct($location_id = false)
    {
        try {
            $location_id = Request::get('location_id');
            $product_id  = Request::get('product_id');
            if ($location_id) {
                $product_details = Product::getQuantityFromLocationDetail($location_id, $product_id);
                if ($product_details) {
                    echo json_encode(array("status" => "success", "data" => $product_details));
                } else {
                    echo json_encode(array("status" => "error", "data" => "No data found"));
                }
            }
        } catch (Exception $e) {
            echo json_encode(array("status" => "error", "data" => "Something went wrong"));
        }

    }

    // get product from inventory table
    public function getInventoryProductDetails()
    {
        try {
            $warehouse_id = Request::get('warehouse_id');
            if ($warehouse_id) {
                $product_details = Product::retrieveProductsFromInventory($warehouse_id);
                if ($product_details) {
                    echo json_encode(array("status" => "success", "data" => $product_details));
                } else {
                    echo json_encode(array("status" => "error", "data" => "No data found"));
                }
            } else {
                echo json_encode(array("status" => "error", "data" => "No data found"));
            }
        } catch (Exception $e) {
            echo json_encode(array("status" => "error", "data" => "Something went wrong"));
        }
    }
    /**
     * [getProductDetailForOffer description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getProductDetailForOffer($product_id)
    {
        try {
            if ($product_id) {
                $product_details = Product::where('id', '=', $product_id)->first();
            }
            if (@$product_details) {
                echo json_encode(array("status" => "success", "data" => $product_details));
            } else {
                echo json_encode(array("status" => "error", "data" => "No data found"));
            }
        } catch (Exception $e) {
            echo json_encode(array("status" => "error", "data" => "Something went wrong"));
        }

    }

    /**
     * [getProductDetailForCCsheet description]
     * @param  [type] $product_id [description]
     * @return [type]             [description]
     */
    public function getProductDetailForCCsheet($product_id = false, $warehouse = false)
    {
        try {
            if ($product_id) {
                $product_details = Product::where('id', '=', $product_id)->first();
                $language        = Session::get('language') ? Session::get('language') : 'no';
                $locations       = Location::orderBy('name', 'asc')->where('warehouse_id', '=', $warehouse)->pluck('name', 'id');
                $unit            = DropdownHelper::select("label")->where("groupcode", "=", "010")->where('language', '=', $language)->where('keycode', '=',
                    $product_details->unit)->first();
                $product_details->unit_text = @$unit->label;
                if ($product_details) {
                    echo json_encode(array("status" => "success", "data" => $product_details, "location" => $locations));
                } else {
                    echo json_encode(array("status" => "error", "data" => "No data found"));
                }
            }
        } catch (Exception $e) {
            echo json_encode(array("status" => "error", "data" => "Something went wrong"));
        }

    }

    /**
     * [checkProductNumberUnique description]
     * @return [type] [description]
     */
    public function checkProductNumberUnique()
    {
        try {
            $product_number = Request::get('product_number');
            $result         = Product::checkProductNumberUnique($product_number);
            echo $result;
        } catch (Exception $e) {
            echo json_encode(array("status" => "error", "data" => "Something went wrong"));
        }
    }

    /**
     * [getProductDetailByNumber description]
     * @param  [type] $product_number [description]
     * @return [type]                 [description]
     */
    public function getProductDetailByNumber($product_number)
    {
        try {
            $product_detail = Product::where('product_number', '=', $product_number)->first();
            if ($product_detail) {
                return json_encode(array('product_detail' => $product_detail, "status" => "success"));
            } else {
                return json_encode(array('product_detail' => null, "status" => "success"));
            }
        } catch (\Exception $e) {
            return json_encode(array('product_detail' => null, "status" => "error"));
        }
    }

    /**
     * [exportProduct description]
     * @return [type] [description]
     */
    public function exportProduct()
    {
        $fileData = Product::exportProductXL();
        if ($fileData && file_exists($fileData['filePath'])) {
            return Response::download($fileData['filePath'], $fileData['fileName'], $fileData['headers']);
        } else {
            return Redirect::back()->with('error', trans('main.something_went_wrong'));
        }
    }

    /**
     * [importProduct description]
     * @return [type] [description]
     */
    public function importProduct()
    {
        try {
            $input           = Request::all();
            $file            = $input['import_excel'];
            $filepath        = "/uploads/productXL/";
            $destinationPath = storage_path() . $filepath;
            if ($file) {
                $input['id'] = GanticHelper::gen_uuid();
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                \File::cleanDirectory($destinationPath);
                $fileName = 'product.xlsx';
                $file->move($destinationPath, $fileName, 0777);
            }
            $excelData = [];
            if ($xlsx = \SimpleXLSX::parse($destinationPath . $fileName)) {
                foreach ($xlsx->rows() as $r) {
                    $excelData[] = $r;
                }
            }
            $headersArray = [
                0  => "Varenr",
                1  => "Gruppe",
                2  => "Kontonr",
                3  => "Lev.varenr",
                4  => "Leverandør pris nok",
                5  => "Kost",
                6  => "Kostprisfaktor",
                7  => "Kostpris",
                8 => "Avanse%",
                9 => "Avanse",
                10 => "Salgspris",
                11 => "MVA%",
                12 => "Salgspris m/MVA",
                13 => "DG",
                14 => "Lagerført",
                15 => "Beskrivelse",
                16 => "Enhet",
                17 => "Leverandør",
                18 => "Lev.varenr",
                19 => "Varenavn",
                20 => "Lev.pris",
                21 => "Lev.rabatt",
                22 => "Rabattert",
                23 => "Frakttillegg%",
                24 => "Annet%",
                25 => "Lev.valuta",
            ];
            if (@$excelData[0] && ($headersArray === $excelData[0])) {
                $product = Product::importProductFromExcel($excelData);
                if ($product) {
                    return Redirect::back()->with('success', trans('main.imported_successfully'));
                }
                return Redirect::back()->with('error', trans('main.something_went_wrong'));
            }
            return Redirect::back()->with('error', trans('main.invalid_file'));
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            GanticHelper::errorLog($error_message);
            return Redirect::back()->with('error', trans('main.something_went_wrong'));
        }

    }

    /**
     * [recalculatePrices description]
     * @return [type] [description]
     */
    public function recalculatePrices()
    {
        Product::updateProductPrice();
        return Redirect::back()->with('success', trans('main.recalculated_done'));
    }

    /**
     * [prodcutSupplierView description]
     * @return [type] [description]
     */
    public function productSupplierView()
    {
        $data                  = @\Request::input();
        $language              = Session::get('language') ? Session::get('language') : 'no';
        $data['suppliers']     = Customer::where('is_supplier', '=', 1)->where('status', '=', '0')->pluck('name', 'id');
        $data['units']         = DropdownHelper::where("groupcode", "=", "010")->where('language', '=', $language)->orderby("keycode", "asc")->pluck("label", "keycode");
        $data['currency_list'] = DropdownHelper::where('language', $language)->where('groupcode', '015')->orderBy('keycode', 'asc')->pluck('label', 'keycode')->toArray();
        if (@$data['id']) {
            $data['productSupplier'] = ProductSupplier::find($data['id']);
        }
        return view('warehousedetails/products/partials/supplierCreateEditView', $data);
    }

    /**
     * [createOrUpdate description]
     * @return [type] [description]
     */
    public function createOrUpdate()
    {
        $input                      = Request::except('_token', );
        $input['supplier_price']    = isset($input['supplier_price']) ? str_replace(",", ".", $input['supplier_price']) : "";
        $input['supplier_discount'] = isset($input['supplier_discount']) ? str_replace(",", ".", $input['supplier_discount']) : "";
        $input['discount']          = isset($input['discount']) ? str_replace(",", ".", $input['discount']) : "";
        $input['other']             = isset($input['other']) ? str_replace(",", ".", $input['other']) : "";
        $input['addon']             = isset($input['addon']) ? str_replace(",", ".", $input['addon']) : "";
        $input['realcost']          = isset($input['realcost']) ? str_replace(",", ".", $input['realcost']) : "";
        $input['realcost_nok']      = isset($input['realcost_nok']) ? str_replace(",", ".", $input['realcost_nok']) : "";
        if (@$input['is_main'] == 1) {
            ProductSupplier::where('product_id', $input['product_id'])->update(['is_main' => 0]);
            Product::updateProductPricesBySupplier($input);
        } else {
            $input['is_main'] = 0;
        }
        if (@$input['id']) {
            $productSupplier = ProductSupplier::find($input['id']);
            $productSupplier->fill($input);
            $result = $productSupplier->save();
        } else {
            $result = ProductSupplier::create($input);
        }
        return Redirect::back()->with('success', trans('main.product_updatesuccess'));
    }

    /**
     * [deleteProductSupplier description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function deleteProductSupplier($id)
    {
        $product = ProductSupplier::find($id);
        if (!$product) {
            return Redirect::back()->with($this->error, trans($this->notfound));
        }
        $product->delete();
        return Redirect::back()->with('success', trans('main.product_updatesuccess'));
    }

    /**
     * [loadAddLocationForm description]
     * @return [type] [description]
     */
    public function loadAddLocationForm()
    {
        try {
            $data               = @\Request::input();
            $data['warehouses'] = Warehouse::orderBy('shortname', 'asc')->pluck('shortname', 'id');
            return view('warehousedetails/products/addLocation', $data);
        } catch (Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            register_error_log($error_message, 'product.log');
            return Redirect::back()->with('error', trans('main.something_went_wrong'));
        }

    }

    /**
     * [storeProductLocation description]
     * @return [type] [description]
     */
    public function storeProductLocation()
    {
        try {
            $product_location_data = Request::except('_token');

            if (ProductLocation::where('warehouse_id', $product_location_data['warehouse_id'])->where('location_id', $product_location_data['location_id'])->where('product_id', $product_location_data['product_id'])->first()) {
                return Redirect::back()->with('error', trans('main.location_exists'));
            }
            $product_location = $this->productLocationObj->creatProductLocation($product_location_data);
            return $product_location ? Redirect::back()->with('success', trans('main.product_location_add_sucess')) : Redirect::back()->with('error', trans('main.something_went_wrong'));
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            register_error_log($error_message, 'product.log');
            return Redirect::back()->with('error', trans('main.something_went_wrong'));
        }
    }

    /**
     * [deleteLocation description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function deleteLocation($id)
    {
        try {
            $product_location = ProductLocation::find($id);
            if (!$product_location) {
                return Redirect::back()->with($this->error, trans($this->notfound));
            }
            $product_location->delete();
            return Redirect::back()->with($this->success, trans('main.product_location_delete_sucess'));
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            register_error_log($error_message, 'product.log');
            return Redirect::back()->with('error', trans('main.something_went_wrong'));
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Helpers\GanticHelper;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\ProductPackage;
use DB;
use Illuminate\Http\Request;
use Input;
use Redirect;
use Response;
use Session;

class ProductPackageController extends Controller
{

    protected $folder    = 'productpackage';
    protected $route     = 'main.productpackage.index';
    protected $success   = 'success';
    protected $error     = 'error';
    protected $notfound  = 'main.notfound';
    protected $createmsg = 'main.productpackage_createsuccess';
    protected $updatemsg = 'main.productpackage_updatesuccess';
    protected $deletemsg = 'main.productpackage_deletesuccess';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = @Session::get('productpackage_search') ? @Session::get('productpackage_search') : [];
        @\Request::input() ? Session::put('productpackage_search', array_merge($data, @\Request::input())) : '';
        $data['products'] = ProductPackage::getProductPackages(@Session::get('productpackage_search'));
        return view('warehousedetails/productpackage/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data                          = Product::createOrEditProductData(null, 1);
        $data['product_package_count'] = 0;
        return view('warehousedetails/productpackage/form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $input               = $request->all();
        $product_count       = $input['hidden_product_package_table_row_count'];
        $input['id']         = GanticHelper::gen_uuid();
        $input['sale_price'] = isset($input['sale_price']) ? str_replace(",", ".", $input['sale_price']) : "";
        $input['tax']        = isset($input['tax']) ? str_replace(",", ".", $input['tax']) : "";
        $input['is_package'] = 1;
        Product::create($input);
        ProductPackage::createProductList($input, $input['id'], $product_count);
        return Redirect::route($this->route)->with($this->success, trans($this->createmsg));
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
        $data                          = Product::createOrEditProductData($id);
        $data['btn']                   = trans('main.update');
        $data['pacakage_product_list'] = ProductPackage::where("package_id", '=', $id)->orderBy("sort_number", "asc")->get();
        foreach ($data['pacakage_product_list'] as $key => $value) {
            $product_details = Product::withTrashed()->where('id', '=', $value->content)->first();
            if (@$product_details && $product_details->sn_required == 1) {
                $value->qty = number_format($value->qty, 0);
            } else {
                $value->qty = number_format($value->qty, 2, ",", " ");
            }
        }
        $data['product_package_count'] = count($data['pacakage_product_list']);
        $data['product_list']          = Product::select(DB::Raw("CONCAT(product_number, ' - ', IFNULL(description, '')) AS name, id"))->orderBy("product_number", "asc")->where('is_package', '=', 0)->pluck("name", "id");
        return view('warehousedetails/productpackage/edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $id)
    {
        $input               = $request->all();
        $product_count       = $input['hidden_product_package_table_row_count'];
        $product             = Product::find($id);
        $input['sale_price'] = isset($input['sale_price']) ? str_replace(",", ".", $input['sale_price']) : "";
        $input['tax']        = isset($input['tax']) ? str_replace(",", ".", $input['tax']) : "";
        $input['is_package'] = 1;
        $product->fill($input);
        $product->save();
        ProductPackage::where('package_id', '=', $id)->delete();
        ProductPackage::createProductList($input, $id, $product_count);
        return Redirect::route($this->route)->with($this->success, trans($this->updatemsg));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        $product->delete();
        return Redirect::route($this->route)->with($this->success, trans($this->deletemsg));
    }

    /**
     * [getPacakgeProducts description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getPacakgeProducts(Request $request)
    {
        try {
            $package_details = ProductPackage::generatePackageProductContent($request->all());
            if ($package_details) {
                echo json_encode(array("status" => "success", "data" => $package_details));
            } else {
                echo json_encode(array("status" => "error", "data" => "No data found"));
            }
        } catch (Exception $e) {
            echo json_encode(array("status" => "error", "data" => "Something went wrong"));
        }
    }
}

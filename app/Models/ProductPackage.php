<?php

namespace App\Models;

use App\Models\Product;
use App\Models\ProductSupplier;
use Illuminate\Database\Eloquent\Model;
use session;

class ProductPackage extends Model
{

    protected $table   = 'product_package';
    public $timestamps = true;

    protected $fillable = array('id', 'package_id', 'content', 'qty', 'sort_number');
    /**
     * [getProductPackages description]
     * @return [type] [description]
     */
    public static function getProductPackages($conditions = false)
    {
        $product = Product::where('is_package', '=', 1);
        $product->leftjoin('acc_plan', 'acc_plan_id', '=', "acc_plan.id");
        $product->addSelect('product.*', 'acc_plan.Name as Name', 'acc_plan.AccountNo as AccountNo');
        if (isset($conditions['search']) && $conditions['search'] != '') {
            $search = $conditions['search'];
            $product->where(function ($query) use ($search) {
                $query->orwhere('product_number', 'LIKE', '%' . $search . '%');
                $query->orwhere('sale_price', 'LIKE', '%' . str_replace(",", ".", $search) . '%');
                $query->orwhere('description', 'LIKE', '%' . $search . '%');
                $query->orwhere('Name', 'LIKE', '%' . $search . '%');
                $query->orwhere('AccountNo', 'LIKE', '%' . $search . '%');
            });
        }

        $paginate_size = Session::get('paginate_size') ? Session::get('paginate_size') : 10;
        $product       = $product->sortable(['product_number' => 'asc'])->paginate($paginate_size);
        for ($i = 0; $i < count($product); $i++) {
            $product[$i]->sale_price = number_format($product[$i]->sale_price, "2", ",", " ");
        }
        return $product;
    }

    /**
     * [createProductList description]
     * @param  [type]  $data          [description]
     * @param  [type]  $group_id      [description]
     * @param  boolean $product_count [description]
     * @return [type]                 [description]
     */
    public static function createProductList($data, $pacakage_id, $product_count = false)
    {
        try {
            if ($product_count) {
                for ($i = 0; $i < $product_count; $i++) {
                    $record['package_id']  = $pacakage_id;
                    $record['content']     = @$data['product_' . $i];
                    $record['qty']         = @$data['qty_' . $i] ? str_replace(",", ".", @$data['qty_' . $i]) : 0;
                    $record['sort_number'] = $i;
                    if (@$record['content']) {
                        ProductPackage::create($record);
                    }

                }
            }
        } catch (Exception $e) {}
    }

    /**
     * [generatePackageProductContent description]
     * @return [type] [description]
     */
    public static function generatePackageProductContent($input)
    {
        try {

            $package_details  = Product::where('id', '=', $input['package_id'])->first()->toArray();
            $package_contents = $package_products = ProductPackage::selectRaw('product.*, product_package.*, product.id as product_id')
                ->where("product_package.package_id", '=', $input['package_id'])
                ->leftjoin('product', 'content', '=', 'product.id')
                ->orderBy("product_package.sort_number", "desc")
                ->get()
                ->toArray();
            if (@$input['type'] == 1) {
                $package_products = [];
                foreach ($package_contents as $key => $value) {
                    $check_supplier_result = ProductSupplier::where('product_id', $value['product_id'])->where('supplier', $input['supplier_id'])->count();
                    if ($check_supplier_result > 0) {
                        $package_products[] = $value;
                    }
                }
            }
            foreach ($package_products as $key => $value) {
                $package_products[$key]['qty'] = $value['qty'] ? number_format($value['qty'], 2, ",", " ") : 0;
            }
            $package_details['package_products'] = $package_products;
            return $package_details;
        } catch (\Exception $e) {
            echo $e;exit;
            return [];
        }
    }

}

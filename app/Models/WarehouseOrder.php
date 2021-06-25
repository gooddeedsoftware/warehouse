<?php

namespace App\Models;

use App\Helpers\GanticHelper;
use App\Models\DropdownHelper;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductSupplier;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Auth;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Mail;
use PDF;
use Request;
use Session;
use View;

class WarehouseOrder extends Model
{

    protected $table     = 'whs_transfer_order';
    public $timestamps   = true;
    public $incrementing = false;
    use SoftDeletes;
    use Sortable;

    protected $fillable = array('id', 'order_number', 'order_type', 'supplier_id', 'source_warehouse', 'destination_warehouse', 'warehouse', 'priority', 'order_date', 'order_comment', 'order_status', 'deleted_at', 'product_details', 'is_notified', 'added_by', 'updated_by', 'customer_order_id', 'customer_order_number', 'company', 'post_address', 'zip', 'city', 'country', 'supplier_ref', 'delivery_method', 'our_reference');

    protected $sortable = array('order_number', 'order_date', 'added_by');

    public function sourceWarehouse()
    {
        return $this->hasOne('App\Models\Warehouse', 'id', 'source_warehouse');
    }

    public function warehouses()
    {
        return $this->hasOne('App\Models\Warehouse', 'id', 'warehouse');
    }

    public function destinationWarehouse()
    {
        return $this->hasOne('App\Models\Warehouse', 'id', 'destination_warehouse');
    }

    public function supplier()
    {
        return $this->hasOne('App\Models\Customer', 'id', 'supplier_id');
    }

    public function customerOrderRelation()
    {
        return $this->hasOne('App\Models\Order', 'id', 'customer_order_id');
    }

    public static function getWarehouseOrderDetails($conditions = false, $orderby = 'order_number', $order = 'desc', $order_type = false, $order_status = false)
    {
        $warehouse_order_details = WarehouseOrder::select('*');
        $warehouse_order_details->select(DB::Raw("whs_transfer_order.id, whs_transfer_order.order_number,  whs_transfer_order.order_comment,whs_transfer_order.order_type,concat_ws('',s1.name,w1.shortname,w3.shortname) from_whs_concat, concat_ws('',w2.shortname,w3.shortname) to_whs_concat, whs_transfer_order.priority, whs_transfer_order.order_date, whs_transfer_order.order_status, user.initials as added, first_name as first_name, last_name as last_name, customer_order_number as customer_order_number, customer_order_id as customer_order_id"));
        $warehouse_order_details->leftjoin('customer as s1', 's1.id', '=', 'whs_transfer_order.supplier_id');
        $warehouse_order_details->leftjoin('warehouse as w1', 'w1.id', '=', 'whs_transfer_order.source_warehouse');
        $warehouse_order_details->leftjoin('warehouse as w2', 'w2.id', '=', 'whs_transfer_order.destination_warehouse');
        $warehouse_order_details->leftjoin('warehouse as w3', 'w3.id', '=', 'whs_transfer_order.warehouse');
        $warehouse_order_details->leftjoin('user', 'user.id', '=', 'whs_transfer_order.added_by');

        $warehouse_order_details->leftJoin('dropdown_helper as A', function ($join) {
            $join->on('whs_transfer_order.order_type', '=', 'A.key_code')->where('A.group_code', '=', '014');
        });
        $warehouse_order_details->leftJoin('dropdown_helper as B', function ($join) {
            $join->on('whs_transfer_order.priority', '=', 'B.key_code')->where('B.group_code', '=', '006');
        });
        $warehouse_order_details->leftJoin('dropdown_helper as C', function ($join) {
            $join->on('whs_transfer_order.order_status', '=', 'C.key_code')->where('C.group_code', '=', '013');
        });
        if (isset($conditions['search']) && $conditions['search'] != '') {
            $search = $conditions['search'];
            $warehouse_order_details->where(function ($query) use ($search) {
                $query->orwhere('order_number', 'LIKE', '%' . $search . '%');
                $query->orwhere('priority', 'LIKE', '%' . $search . '%');
                $query->orwhere('w1.shortname', 'LIKE', '%' . $search . '%');
                $query->orwhere('w2.shortname', 'LIKE', '%' . $search . '%');
                $query->orwhere('w3.shortname', 'LIKE', '%' . $search . '%');
                $query->orwhere('order_date', 'LIKE', '%' . formatSearchDate($search) . '%');
                $query->orwhere('s1.name', 'LIKE', '%' . $search . '%');
                $query->orwhere('user.first_name', 'LIKE', '%' . $search . '%');
                $query->orwhere('order_type', 'LIKE', '%' . $search . '%');
            });
        }
        if ($order_type) {
            $warehouse_order_details->where('order_type', '=', $order_type);
        }
        if ($order_status) {
            $warehouse_order_details->where('order_status', '=', $order_status);
        }

        if ($orderby == 'order_number' && Request::get('sort') == 'product_number') {
            $warehouse_order_details->orderby('order_number', $order);
        } else {
            $warehouse_order_details->sortable(['order_number' => 'desc']);
        }
        $paginate_size           = Session::get('paginate_size') ? Session::get('paginate_size') : 10;
        $warehouse_order_details = $warehouse_order_details->paginate($paginate_size);
        for ($i = 0; $i < count($warehouse_order_details); $i++) {
            $warehouse_order_details[$i]->order_date = (($warehouse_order_details[$i]->order_date) ? GanticHelper::formatDate($warehouse_order_details[$i]->order_date) : "");
            $warehouse_order_details[$i]->priority   = (($warehouse_order_details[$i]->priority == 1) ? "01" : (($warehouse_order_details[$i]->priority == 2) ? "02" : (($warehouse_order_details[$i]->priority == 3) ? "03" : "")));
        }
        return $warehouse_order_details;
    }

    /**
     * [departmentSortable description]
     * @param  [type] $query     [description]
     * @param  [type] $direction [description]
     * @return [type]            [description]
     */
    public function ordertypeSortable($query, $direction)
    {
        $language = Session::get('language') ? Session::get('language') : 'no';
        if ($language == 'no') {
            $query->orderby('A.value_no', $direction);
        } else {
            $query->orderby('A.value_en', $direction);
        }
    }

    /**
     * [prioritySortable description]
     * @param  [type] $query     [description]
     * @param  [type] $direction [description]
     * @return [type]            [description]
     */
    public function prioritySortable($query, $direction)
    {
        $language = Session::get('language') ? Session::get('language') : 'no';
        if ($language == 'no') {
            $query->orderby('B.value_no', $direction);
        } else {
            $query->orderby('B.value_en', $direction);
        }
    }

    /**
     * [orderstatusSortable description]
     * @param  [type] $query     [description]
     * @param  [type] $direction [description]
     * @return [type]            [description]
     */
    public function orderstatusSortable($query, $direction)
    {
        $language = Session::get('language') ? Session::get('language') : 'no';

        if ($language == 'no') {
            $query->orderby('C.value_no', $direction);
        } else {
            $query->orderby('C.value_en', $direction);
        }
    }

    /**
     * [orderstatusSortable description]
     * @param  [type] $query     [description]
     * @param  [type] $direction [description]
     * @return [type]            [description]
     */
    public function addedSortable($query, $direction)
    {
        $query->orderby('user.first_name', $direction);
    }

    /**
     * [fromwarehouseSortable description]
     * @param  [type] $query     [description]
     * @param  [type] $direction [description]
     * @return [type]            [description]
     */
    public function fromwarehouseSortable($query, $direction)
    {
        $query->orderby('w1.shortname', $direction);
    }

    /**
     * [towarehouseSortable description]
     * @param  [type] $query     [description]
     * @param  [type] $direction [description]
     * @return [type]            [description]
     */
    public function towarehouseSortable($query, $direction)
    {
        $query->orderby('w2.shortname', $direction);
    }

    /**
     * [getDatasForSupplierOrder description]
     * @param  boolean $id   [description]
     * @param  integer $type [description]
     * @return [type]        [description]
     */
    public static function getDatasForSupplierOrder($id = false, $type = 3)
    {
        $data                     = array();
        $data['product_packages'] = Product::select(DB::Raw("CONCAT(product_number, ' - ', IFNULL(description, '')) AS name, id"))->orderBy("product_number", "asc")->where('is_package', '=', 1)->pluck("name", "id");
        $data['users']            = User::select(DB::Raw("concat(first_name ,' ',  IFNULL(last_name, '')) AS name, id"))->where('activated', '=', 0)->orderBy('first_name', 'asc')->pluck('name', 'id');
        $data['btn']              = trans('main.create');
        $language                 = Session::get('language') ? Session::get('language') : 'no';
        $data['warehouses']       = Warehouse::orderby('shortname', 'asc')->pluck('shortname', 'id');
        $data['all_locations']    = Location::orderby('name', 'asc')->pluck('name', 'id');
        $data['priorities']       = DropdownHelper::where('groupcode', '=', '006')->where('language', $language)->orderby('keycode', 'asc')->pluck('label', 'keycode');
        $data['supplier']         = Customer::where('is_supplier', 1)->orderby("name", "asc")->pluck("name", "id");
        $data['company']          = Company::first();
        $data['countries']        = Country::pluck('name', 'id');
        if ($id) {
            $data['warehouseorder']    = WarehouseOrder::find($id);
            $data['create_order_type'] = $data['warehouseorder']->order_type;
            $warehouse_id              = $data['warehouseorder']->warehouse;
            $data['status']            = DropdownHelper::where('groupcode', '=', '013')->where('language', $language);
            if ($data['warehouseorder']->order_status == 2) {
                $data['status']->where(function ($query) {
                    $query->where('keycode', '=', '3')->orwhere('keycode', '=', '2');
                });
            } else if ($data['warehouseorder']->order_status == 1) {
                $data['status']->where(function ($query) {
                    $query->where('keycode', '=', '3')->orwhere('keycode', '=', '1')->orwhere('keycode', '=', '2');
                });
            } else if ($data['warehouseorder']->order_status == 5) {
                $data['status']->where(function ($query) {
                    $query->where('keycode', '=', '5')->orwhere('keycode', '=', '6');
                });
            } else {
                $data['status']->where('keycode', '!=', 7)->where('keycode', '!=', 8)->where('keycode', '!=', 9);
            }
            $warehouse_id                       = $data['warehouseorder']->destination_warehouse;
            $data['status']                     = $data['status']->pluck('label', 'keycode');
            $data['products']                   = Product::getProductDetailsFromOrderType($data['warehouseorder']->order_type, $data['warehouseorder']->supplier_id);
            $data['warehouseorder']->order_date = (($data['warehouseorder']->order_date) ? GanticHelper::formatDate($data['warehouseorder']->order_date) : "");
            $data['warehouseorder']->priority   = (($data['warehouseorder']->priority == 1) ? "01" : (($data['warehouseorder']->priority == 2) ? "02" : (($data['warehouseorder']->priority == 3) ? "03" : "")));
        } else {
            $data['status'] = DropdownHelper::where('groupcode', '=', '013')->where('language', $language);
            $data['status']->where(function ($query) use ($type) {
                $query->where('keycode', '=', 1);
                $query->orwhere('keycode', '=', 2);
            });
            $data['status'] = $data['status']->orderby('keycode', 'asc')->pluck('label', 'keycode');
        }
        return $data;
    }

    /**
     * [getDatasForAdjustmentOrder description]
     * @param  boolean $id  [description]
     * @param  integer $tye [description]
     * @return [type]       [description]
     */
    public static function getDatasForAdjustmentOrder($id = false, $type = 1)
    {
        $data                   = array();
        $data['warehouseorder'] = new WarehouseOrder();
        $data['btn']            = trans('main.create');
        $language               = Session::get('language') ? Session::get('language') : 'no';
        $data['warehouses']     = Warehouse::orderby('shortname', 'asc')->pluck('shortname', 'id');
        $data['priorities']     = DropdownHelper::where('groupcode', '=', '006')->where('language', $language)->orderby('keycode', 'asc')->pluck('label', 'keycode');
        if ($id) {
            $data['warehouseorder']                 = WarehouseOrder::findOrFail($id);
            $data['warehouseorder_product_details'] = json_decode($data['warehouseorder']->product_details);
            $warehouse_id                           = $data['warehouseorder']->warehouse;
            $data['status']                         = DropdownHelper::where('groupcode', '=', '013')->where('language', $language);
            if ($data['warehouseorder']->order_status == 1 || $data['warehouseorder']->order_status == 2) {
                $data['status']->where(function ($query) {
                    $query->where('keycode', '=', '1')->orwhere('keycode', '=', '3');
                });
            } else if ($data['warehouseorder']->order_status == 5) {
                $data['status']->where(function ($query) {
                    $query->where('keycode', '=', '5')->orwhere('keycode', '=', '6');
                });
            } else {
                $data['status']->where('keycode', '!=', 2)->where('keycode', '!=', 7)->where('keycode', '!=', 9)->where('keycode', '!=', 8)->where('keycode', '!=', 8);
            }
            $data['status']                     = $data['status']->pluck('label', 'keycode');
            $data['destination_locations']      = Location::orderby('name', 'asc')->where('warehouse_id', '=', $warehouse_id)->pluck('name', 'id');
            $data['warehouseorder']->order_date = (($data['warehouseorder']->order_date) ? GanticHelper::formatDate($data['warehouseorder']->order_date) : "");
            $data['warehouseorder']->priority   = (($data['warehouseorder']->priority == 1) ? "01" : (($data['warehouseorder']->priority == 2) ? "02" : (($data['warehouseorder']->priority == 3) ? "03" : "")));
        } else {
            $data['status'] = DropdownHelper::where('groupcode', '=', '013')->where('language', $language);
            $data['status']->where(function ($query) use ($type) {
                $query->where('keycode', '=', 1);
            });
            $data['status'] = $data['status']->orderby('keycode', 'asc')->pluck('label', 'keycode');
        }
        return $data;
    }

    /**
     *  There are totally three type of orders are there
     *  1 - Transfer Order
     *  2 - Adjustment Order
     *  3 - Supplier Order
     */
    /**
     * get data's to create orders
     * @param  string $id
     * @param  Integer $type
     * @param  String $warehouse
     * @param  string $product_details
     * @return object
     */
    public static function getDatasToCreateOrders($id = false, $type = 1)
    {
        $data               = array();
        $data['btn']        = trans('main.create');
        $usertype           = Session::get('usertype');
        $language           = Session::get('language') ? Session::get('language') : 'no';
        $data['warehouses'] = Warehouse::orderby('shortname', 'asc')->pluck('shortname', 'id');
        if ($usertype == "Admin" || $usertype == "Administrative") {
            $data['transfer_order_warehouses'] = Warehouse::getWarehouseforTransferOrders();
        } else {
            $user_id                           = Session::get('currentUserID');
            $data['transfer_order_warehouses'] = Warehouse::getWarehouseforTransferOrders($user_id);
        }
        $data['priorities'] = DropdownHelper::where('groupcode', '=', '006')->where('language', $language)->orderby('keycode', 'asc')->pluck('label', 'keycode');
        $data['order_type'] = DropdownHelper::where('groupcode', '=', '014')->where('language', $language)->orderby('keycode', 'asc')->pluck('label', 'keycode');
        if ($id) {
            $data['warehouseorder'] = WarehouseOrder::findOrFail($id);
            $warehouse_id           = $data['warehouseorder']->warehouse;

            $data['status'] = DropdownHelper::where('groupcode', '=', '013')->where('language', $language);
            $data['status']->orderby(DB::Raw(' FIELD(keycode, "1", "7", "3","8", "9", "5", "6")'), 'asc');
            if ($data['warehouseorder']->order_status > 3 && $data['warehouseorder']->order_status != 7 && $data['warehouseorder']->order_status != 5) {
                $data['status']->where('keycode', '!=', '1')->where('keycode', '!=', '3')->where('keycode', '!=', '7')->where('keycode', '!=', '2');
            } else if ($data['warehouseorder']->order_status == 1 || $data['warehouseorder']->order_status == 0) {
                $data['status']->where(function ($query) {
                    $query->where('keycode', '=', '1')->orwhere('keycode', '=', '7');
                });
            } else if ($data['warehouseorder']->order_status == 7) {
                $data['status']->where(function ($query) {
                    $query->where('keycode', '=', '1')->orwhere('keycode', '=', '7')->orwhere('keycode', '=', '3');
                });
            } else if ($data['warehouseorder']->order_status == 5) {
                $data['status']->where(function ($query) {
                    $query->where('keycode', '=', '5')->orwhere('keycode', '=', '6');
                });
            } else {
                $data['status']->where('keycode', '!=', '2')->where('keycode', '!=', '4');
            }
            $data['status']                     = $data['status']->pluck('label', 'keycode');
            $data['products']                   = Product::getProductDetailsFromOrderType($data['warehouseorder']->order_type, false);
            $data['locations']                  = Location::getLocationsForWarehourOrders($data['warehouseorder']->order_type, $data['warehouseorder']->source_warehouse);
            $data['destination_locations']      = Location::getLocationsForWarehourOrders(false, $data['warehouseorder']->destination_warehouse);
            $data['warehouseorder']->order_date = (($data['warehouseorder']->order_date) ? GanticHelper::formatDate($data['warehouseorder']->order_date) : "");
            $data['warehouseorder']->priority   = (($data['warehouseorder']->priority == 1) ? "01" : (($data['warehouseorder']->priority == 2) ? "02" : (($data['warehouseorder']->priority == 3) ? "03" : "")));
        } else {
            $data['status'] = DropdownHelper::where('groupcode', '=', '013')->where('language', $language);
            $data['status']->where(function ($query) use ($type) {
                $query->where('keycode', '=', 1);
                $query->orwhere('keycode', '=', 7);
            });
            $data['status'] = $data['status']->orderby('keycode', 'asc')->pluck('label', 'keycode');
        }
        $data['all_locations'] = Location::orderby('name', 'asc')->pluck('name', 'id');
        return $data;
    }

    // order number
    public static function getWarehouseOrderNumber()
    {
        $act_no = "10000";
        $result = WarehouseOrder::select(DB::raw("MAX(CAST(order_number as UNSIGNED))  as order_number"))->first();
        if (isset($result->order_number) && $result->order_number >= 10000) {
            $order_number = $result->order_number;
            $order_number = sprintf('%05d', $order_number + 1);
        } else {
            $order_number = '10000';
        }
        return $order_number;
    }

    // update warehouse order status
    public static function updateWarehouseStatus($id, $status)
    {
        if ($id) {
            try {
                DB::update('update `whs_transfer_order` set order_status="' . $status . '" where  id="' . $id . '"');
            } catch (Exception $e) {
            }
        }
    }

    /**
     *   Get warehouse order details and construct data
     *   @param id string
     *   @return file
     **/
    public function createWarehouseReport($id = false, $return_content = false)
    {
        if ($id) {
            $warehouse_order = new WarehouseOrder();
            $data            = $warehouse_order->getWarehouseOrderAndProductDetails($id, 1);
            if ($data['warehouse_details']->order_type_id == 4) {
                return ['pdf' => WarehouseOrder::createReturnOrderReport($id, $data), 'fileName' => @$data['warehouse_details']->order_number];
            } else {
                if ($data['warehouse_details']->order_type_id == 3) {
                    return ['pdf' => WarehouseOrder::constructWarehouseOrderPDF($id), 'fileName' => @$data['warehouse_details']->order_number];
                } elseif ($data['warehouse_details']->order_type_id == 2) {
                    $view = 'warehousedetails.order.adjustment_report';
                } elseif ($data['warehouse_details']->order_type_id == 1) {
                    $view = 'warehousedetails.order.transfer_report';
                } else {
                    $view = 'warehousedetails.order.warehouseorder_report';
                }
                $warehouse_order_report_view = View::make($view, $data);
                $order_report_contents       = $warehouse_order_report_view->render();
                if ($return_content) {
                    return $order_report_contents;
                }
                $pdf_file_name = GanticHelper::createTempFile("pdf");
                PDF::loadHTML($order_report_contents)->save($pdf_file_name);
                return ['pdf' => $pdf_file_name, 'fileName' => @$data['warehouse_details']->order_number];
            }
        } else {
            return false;
        }
    }

    /**
     * [createReturnOrderReport description]
     * @return [type] [description]
     */
    public static function createReturnOrderReport($id, $data)
    {
        try {
            if ($id && $data) {
                $input                                = $data;
                $order_id                             = $data['warehouse_details']->customer_order_id;
                $data                                 = Order::editOrder($data['warehouse_details']->customer_order_id);
                $data['return_order_product_details'] = json_decode($input['warehouse_details']->product_details);
                $data['product_drop_downs']           = Product::productDetails();
                $data['warehouses']                   = Warehouse::orderby('shortname', 'asc')->pluck('shortname', 'id');
                $data['all_locations']                = Location::orderby('name', 'asc')->pluck('name', 'id');
                $data['contacts_details']             = array();
                $data['department_details']           = array();
                $data['warehouse_order_details']      = $input;
                if (@$data['orders']->customer_id) {
                    $data['customer_details'] = Customer::where('id', '=', $data['orders']->customer_id)->with('shippingCustomerAddress', 'departmentCustomerAddress')->first();
                }
                if (@$data['selected_contact_persons']) {
                    foreach ($data['selected_contact_persons'] as $key => $value) {
                        $data['contacts_details'][] = $data['contacts_with_mobile'][$value];
                    }
                }
                if (@$data['contacts_details']) {
                    $data['contact_persons'] = implode(',', $data['contacts_details']);
                }
                if (@$data['orders']->department_id) {
                    foreach ($data['orders']->department_id as $key => $value) {
                        $data['department_details'][] = $data['departments'][$value];
                    }
                }
                if (@$data['department_details']) {
                    $data['selected_department'] = implode(',', $data['department_details']);
                }
                $data['responsible_data'] = [];
                $responsible              = OrderUser::where('order_id', '=', $order_id)->first();
                if ($responsible) {
                    $data['responsible_data'] = User::where('id', '=', $responsible->user_id)->first();
                }
                $company['company_information'] = Company::first();
                $footer                         = View::make('common.footer_report', $company);
                $warehouse_order_report_view    = View::make('warehousedetails.order.returnOrder_report', $data);
                $temp_pdf_name                  = GanticHelper::createTempFile("pdf");
                $pdfReport                      = PDF::loadHTML($warehouse_order_report_view)->setOption('footer-html', $footer)->save($temp_pdf_name);
                return $temp_pdf_name;
            } else {
                return false;
            }

        } catch (Exception $e) {
            echo $e;
            exit();
        }
    }

    /**
     * getWarehouseOrderAndProductDetails
     * @param  string $id
     * @return object
     */
    public function getWarehouseOrderAndProductDetails($id = false, $type = false)
    {
        if ($id) {
            $data['warehouse_details'] = WarehouseOrder::where('id', '=', $id)->with('warehouses', 'destinationWarehouse', 'sourceWarehouse', 'supplier')->first();
            $language                  = Session::get('language') ? Session::get('language') : 'no';
            $order_type                = DropdownHelper::where('groupcode', '=', '014')->where('language', $language)->orderby('keycode', 'asc')->pluck('label', 'keycode');
            $product_details           = array();
            if ($data['warehouse_details'] && $data['warehouse_details']->product_details) {
                $warehouseOrder = new WarehouseOrder();
                if ($type == 1) {
                    $product_details = json_decode($data['warehouse_details']->product_details);
                } else {
                    $product_details = $warehouseOrder->splitOrderJson($data);
                }
                $data['locations']                        = Location::orderby('name', 'asc')->pluck('name', 'id');
                $data['warehouses']                       = Warehouse::orderby('shortname', 'asc')->pluck('shortname', 'id');
                $data['warehouse_details']->order_type_id = $data['warehouse_details']->order_type;
                $data['warehouse_details']->order_type    = $order_type[$data['warehouse_details']->order_type];
            }
            $data['product_details'] = $product_details;
            return $data;
        } else {
            return false;
        }
    }

    /**
     * [splitSupplierOrderJsonData description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function splitSupplierOrderJsonData($data)
    {
        $locations         = Location::orderby('name', 'asc')->pluck('name', 'id');
        $product_details   = array();
        $json_decoded_data = json_decode($data['warehouse_details']->product_details);
        foreach ($json_decoded_data as $json_key => $json_value) {
            $product_id               = $json_value->product_id;
            $product                  = $json_value->product_text;
            $picked_quantity          = 0;
            $ordered_quantity         = $json_value->qty;
            $product_location_details = array();
            foreach ($json_value->order_details as $key => $order_value) {
                foreach ($order_value->serial_number_products as $key => $value) {
                    $array_index = $value->rec_warehouse_text . '_' . $value->rec_location_id . '_' . $order_value->received_date;
                    if (!isset($product_location_details[$array_index])) {
                        $product_location_details[$array_index]['receive_location'] = $value->rec_location_text;
                        $product_location_details[$array_index]['receive_quantity'] = (@$value->serial_number ? 1 : $order_value->received_quantity);
                        $product_location_details[$array_index]['received_date']    = $order_value->received_date;
                        $product_location_details[$array_index]['rec_whs']          = $value->rec_warehouse_text;
                        $product_location_details[$array_index]['product']          = $product;
                        $product_location_details[$array_index]['ordered_quantity'] = $ordered_quantity;

                    } else {
                        $product_location_details[$array_index]['receive_quantity'] = $product_location_details[$array_index]['receive_quantity'] + (@$value->serial_number ? 1 : $order_value->received_quantity);
                    }
                }
            }
            $product_details[] = array('product' => $product, 'source_location' => @$locations[@$json_value->location_id], 'ordered_quantity' => @$json_value->qty, 'location_details' => array($product_location_details), 'picked_quantity' => $picked_quantity);
        }
        return $product_details;
    }

    /**
     *   Split order JSON
     *   @param data object
     *   @return array
     **/
    public function splitOrderJson($data)
    {
        $locations         = Location::orderby('name', 'asc')->pluck('name', 'id');
        $product_details   = array();
        $json_decoded_data = json_decode($data['warehouse_details']->product_details);
        foreach ($json_decoded_data as $json_key => $json_value) {
            $product_id               = $json_value->product_id;
            $product                  = $json_value->product_text;
            $picked_quantity          = 0;
            $product_location_details = array();
            foreach ($json_value->order_details as $key => $order_value) {
                if ($data['warehouse_details']->order_type == 1) {
                    $picked_quantity = $picked_quantity+@$order_value->picked_quantity;
                } else {
                    $picked_quantity = '';
                }
                if ($data['warehouse_details']->order_type == 2) {
                    $array_index                                                = $locations[@$json_value->location_id];
                    $product_location_details[$array_index]['receive_location'] = $locations[@$json_value->location_id];
                    $product_location_details[$array_index]['receive_quantity'] = $order_value->received_quantity;
                    $product_location_details[$array_index]['received_date']    = $order_value->received_date;
                } else {
                    foreach ($order_value->serial_number_products as $key => $value) {
                        $array_index = $value->rec_location_id;
                        if (!isset($product_location_details[$array_index])) {
                            $product_location_details[$array_index]['receive_location'] = $value->rec_location_text;
                            $product_location_details[$array_index]['receive_quantity'] = (@$value->serial_number ? 1 : $order_value->received_quantity);
                            $product_location_details[$array_index]['received_date']    = $order_value->received_date;

                        } else {
                            $product_location_details[$array_index]['receive_quantity'] = $product_location_details[$array_index]['receive_quantity'] + (@$value->serial_number ? 1 : $order_value->received_quantity);

                        }
                    }
                }
            }

            $product_details[] = array('product' => $product, 'source_location' => @$locations[@$json_value->location_id], 'ordered_quantity' => @$json_value->qty, 'location_details' => array($product_location_details), 'picked_quantity' => $picked_quantity);
        }
        return $product_details;
    }

    /**
     * [notifyWarehouseOrderStatus description]
     * @param  [type]  $order_type [description]
     * @param  [type]  $id         [description]
     * @param  string  $language   [description]
     * @param  boolean $user_id    [description]
     * @return [type]              [description]
     */
    public static function notifyWarehouseOrderStatus($order_type, $id, $language = 'no', $user_id = false)
    {
        try {
            $email         = array();
            $order_details = WarehouseOrder::where('id', '=', $id)->where('is_notified', '=', '0')->with('supplier', 'destinationWarehouse')->first();
            if ($order_details) {
                $template = '';
                $data     = array();
                if ($order_type == 3) {
                    $user_email = User::select('email')->where('id', '=', $user_id)->where('activated', '=', 0)->first();
                    if ($user_email) {
                        $email[] = $user_email->email;
                    }
                    $template                = 'warehousedetails/order/supplier_order_notification';
                    $data['order']           = $order_details;
                    $data['priorities']      = DropdownHelper::where('groupcode', '=', '006')->where('language', $language)->orderby('keycode', 'asc')->pluck('label', 'keycode');
                    $data['order']->priority = (($data['order']->priority == 1) ? "01" : (($data['order']->priority == 2) ? "02" : (($data['order']->priority == 3) ? "03" : "")));
                    $subject                 = trans('main.supplierOrder') . ': ' . $order_details->order_number . ' - ' . $order_details->supplier->name;
                } elseif ($order_type == 1) {
                    if ($order_details->order_status == '7') {
                        $subject           = trans('main.product_request_sent_to_your_warehouse');
                        $warehouse_details = Warehouse::where('id', '=', $order_details->source_warehouse)->first();
                    } else if ($order_details->order_status == '9') {
                        $subject           = trans('main.your_requested_order_has_picked');
                        $warehouse_details = Warehouse::where('id', '=', $order_details->destination_warehouse)->first();
                    }
                    if (@$warehouse_details->notification_email) {
                        $email[] = @$warehouse_details->notification_email;
                    }
                }
                try {
                    if (@$email && count($email) > 0) {
                        if ($order_type == 3) {
                            $order_pdf = WarehouseOrder::constructWarehouseOrderPDF($id);
                            Mail::send($template, $data, function ($message) use (&$order_details, &$order_pdf, &$email, &$subject, &$order_type, &$from_mail, &$from_company_name) {
                                $message->subject($subject);
                                $message->attach($order_pdf, ['as' => $order_details->order_number . '.pdf']);
                                $message->to($email);
                                if (config("app.env") != "production") {
                                    $bcc_email[] = "david@processdrive.com";
                                    $bcc_email[] = "vitali@avalia.no";
                                    $message->bcc($bcc_email);
                                }

                            });
                        } else {
                            Mail::send([], [], function ($message) use (&$email, &$subject, &$order_type, &$from_mail, &$from_company_name) {
                                $message->subject('Hi ' . $subject);
                                if ($order_type != 3) {
                                    $message->setBody('Hi ' . $subject);
                                }
                                if (config("app.env") != "production") {
                                    $bcc_email[] = "david@processdrive.com";
                                    $bcc_email[] = "vitali@avalia.no";
                                    $message->bcc($bcc_email);
                                }
                                $message->to($email);
                            });
                        }
                    }
                } catch (Exception $e) {
                    return false;
                }
                WarehouseOrder::where('id', '=', $id)->update(['is_notified' => '1']);
            }
        } catch (Exception $e) {}
    }

    /**
     * [constructWarehouseOrderPDF description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public static function constructWarehouseOrderPDF($id)
    {

        $data                     = WarehouseOrder::getDatasForSupplierOrder($id);
        $data['supplier_details'] = Customer::whereId($data['warehouseorder']->supplier_id)->first();
        $data['users']            = User::select(DB::Raw("concat(first_name ,' ', last_name) AS name, id"))->where('activated', '=', 0)->orderBy('first_name', 'asc')->pluck('name', 'id');
        $language_old             = Session::get('language') ? Session::get('language') : 'no';
        \App::setLocale('no');
        if ($data['supplier_details']->language == 1) {
            \App::setLocale('en');
        }
        $data['supplier_address'] = CustomerAddress::with('customer')->where('customer_id', $data['warehouseorder']->supplier_id)->whereType(1)->orderBy('created_at', 'desc')->first();
        if (!$data['supplier_address']) {
            $data['supplier_address'] = CustomerAddress::with('customer')->where('customer_id', $data['warehouseorder']->supplier_id)->whereType(3)->orderBy('created_at', 'desc')->first();
        }
        if (!$data['supplier_address']) {
            $data['supplier_address'] = CustomerAddress::with('customer')->where('customer_id', $data['warehouseorder']->supplier_id)->whereType(2)->orderBy('created_at', 'desc')->first();
        }
        $data['product_details']     = count(json_decode($data['warehouseorder']->product_details)) > 0 ? warehouseorder::constructProductForSupplierReport(json_decode($data['warehouseorder']->product_details), @$data['warehouseorder']->supplier_id) : [];
        $data['company_information'] = Company::first();
        $data['countries']           = Country::pluck('name', 'id');
        $supplier_report_view        = View::make('warehousedetails/order/suppplier_report', $data);
        $supplier_report_footer_view = View::make('warehousedetails/order/supplier_report_footer', $data);
        $temp_pdf_name               = GanticHelper::createTempFile("pdf");
        $pdfReport                   = PDF::loadHTML($supplier_report_view)->setOption('footer-html', $supplier_report_footer_view)->save($temp_pdf_name);
        \App::setLocale($language_old);
        return $temp_pdf_name;
    }

    /**
     * [constructProductForSupplierReport description]
     * @param  [type] $product_details [description]
     * @return [type]                  [description]
     */
    public static function constructProductForSupplierReport($product_details, $supplier_id)
    {
        $total = 0;
        foreach ($product_details as $key => $value) {
            $product_data = Product::where('id', '=', $value->product_id)->first();
            if ($product_data->is_package == 1) {
                $value->vendor_price = $product_data->sale_price;
            } else {
                $prductSupplier      = ProductSupplier::where('product_id', $product_data->id)->where('supplier', $supplier_id)->first();
                $value->vendor_price = @$prductSupplier ? $prductSupplier->supplier_price : 0;
            }
            $value->productDetails   = $product_data;
            $value->cal_vendor_price = (float) $value->vendor_price * (float) $value->qty;
            $total                   = $total + $value->cal_vendor_price;
        }
        return ['product_details' => $product_details, 'total' => $total];
    }

    //Sending the supplier order mail to the logged in user
    public function sendSupplierOrderMailToUser($id)
    {
        try {
            $language      = Session::get('language') ? Session::get('language') : 'no';
            $order_details = WarehouseOrder::where('id', '=', $id)->with('supplier', 'destinationWarehouse')->first();
            if ($order_details) {
                $data                    = array();
                $email[]                 = Auth::user()->email;
                $template                = 'warehousedetails/order/supplier_order_notification';
                $data['order']           = $order_details;
                $data['priorities']      = DropdownHelper::where('groupcode', '=', '006')->where('language', $language)->orderby('keycode', 'asc')->pluck('label', 'keycode');
                $data['order']->priority = (($data['order']->priority == 1) ? "01" : (($data['order']->priority == 2) ? "02" : (($data['order']->priority == 3) ? "03" : "")));
                $subject                 = trans('main.supplierOrder') . ': ' . $order_details->order_number . ' - ' . $order_details->supplier->name;
                $order_pdf               = WarehouseOrder::constructWarehouseOrderPDF($id);
                Mail::send($template, $data, function ($message) use (&$order_details, &$order_pdf, &$email, &$subject, &$order_type, &$from_mail, &$from_company_name) {
                    $message->subject($subject);
                    $message->attach($order_pdf, ['as' => $order_details->order_number . '.pdf']);
                    $message->to($email);
                    if (config("app.env") != "production") {
                        $bcc_email[] = "david@processdrive.com";
                        $bcc_email[] = "vitali@avalia.no";
                        $message->bcc($bcc_email);
                    }

                });
                return true;
            }
        } catch (Exception $e) {
            echo $e;exit;
        }
        return false;
    }
}

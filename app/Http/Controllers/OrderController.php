<?php
namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Customer;
use App\Models\OfferSettings;
use App\Models\Order;
use App\Models\OrderMaterial;
use App\Models\PacklistHistory;
use App\Models\Shipping;
use App\Traits\PickPackList;
use Redirect;
use Request;
use Response;
use Session;

class OrderController extends Controller
{
    use PickPackList;
    protected $folder      = 'department';
    protected $route       = 'main.order.index';
    protected $offer_route = 'main.offer.index';
    protected $success     = 'success';
    protected $error       = 'error';
    protected $warning     = 'warning';
    protected $createmsg   = 'main.order_createsuccess';
    protected $updatemsg   = 'main.order_updatesuccess';
    protected $deletemsg   = 'main.order_deletesuccess';
    protected $email       = 'main.email';
    protected $no_result   = 'main.notfound';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($order_status = false)
    {
        $filter_data = @Session::get('order_search') ? @Session::get('order_search') : [];
        @Request::input() ? @Session::put('order_search', array_merge($filter_data, @Request::input())) : '';
        $order_status = @Request::get('order_status_hidden') ? Request::get('order_status_hidden') : $order_status;
        $customer_id  = @Request::get('customer_id') ? Request::get('customer_id') : null;
        $data         = Order::getOrderPaginated(Session::get('order_search'), @Session::get('order_search')['search_status'], $order_status, $customer_id);
        if ($order_status == 2) {
            return view('order.archived_order', $data);
        } else {
            return view('order.index', $data);
        }
    }

    /**
     * [offerIndex description]
     * @return [type] [description]
     */
    public function offerIndex($order_status = false)
    {
        $filter_data = @Session::get('order_search') ? @Session::get('order_search') : [];
        @Request::input() ? @Session::put('order_search', array_merge($filter_data, @Request::input())) : '';
        $order_status = @Request::get('order_status_hidden') ? Request::get('order_status_hidden') : $order_status;
        $customer_id  = @Request::get('customer_id') ? Request::get('customer_id') : null;
        $data         = Order::getOrderPaginated(Session::get('order_search'), @Session::get('order_search')['search_status'], $order_status, $customer_id);
        return view('offer.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $data                     = Order::getDataToCreteOrders();
        $data['invoice_comments'] = OfferSettings::whereType(2)->select('data')->first();
        $data['is_offer']         = 0;
        if (@$data) {
            return view('order.form', $data);
        }
        return Redirect::route($this->route)->with($this->warning, __('main.something_went_wrong'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(OrderRequest $request)
    {
        $request_data  = $request->all();
        $order_details = Order::createCustomerOrder($request_data);
        if ($request_data['order_submit_btn'] == 'close') {
            return Redirect::route($this->route)->with($this->success, __($this->createmsg));
        } else {
            return Redirect::route('main.order.edit', $order_details['id'])->with($this->success, __($this->createmsg));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data             = Order::editOrder($id);
        $data['is_offer'] = 0;
        if ($data == 0) {
            return Redirect::route($this->route)->with($this->warning, __($this->no_result));
        } else {
            return view('order.edit', $data);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(OrderRequest $request, $id)
    {
        $request_data         = $request->all();
        $order_status_from_DB = Order::updateCustomerOrder($request_data, $id);
        if ($request_data['update_mail_btn_val'] == 1) {
            $data                 = array();
            $data['order_status'] = $request_data['status'];
            $data['order_id']     = $id;
            $data['is_offer']     = 0;
            $mail_send_details    = Order::sendOrderMail($data, false);
            if (@$mail_send_details) {
                return Redirect::back()->with('success', __('main.emailsent'));
            } else {
                return Redirect::back()->with('warning', __('main.emailnotsent'));
            }
        }
        if (@$request_data['update']) {
            return Redirect::back()->with($this->success, __($this->updatemsg));
        } else {
            $index_page = 1;
            if ($order_status_from_DB == 5) {
                return Redirect::action('OrderController@index', 2)->with($this->success, __($this->updatemsg));
            }
            return Redirect::action('OrderController@index', $index_page)->with($this->success, __($this->updatemsg));
        }
    }

/**
 * Remove the specified resource from storage.
 *
 * @param  int  $id
 * @return Response
 */
    public function destroy($id)
    {
        $orders = Order::findOrFail($id);
        $orders->delete();
        return Redirect::route($this->route)->with($this->success, __($this->deletemsg));
    }

    // create order from equipment
    public function createOrderFromEquipment($equipment_id, $customer_id)
    {
        $data             = Order::getDataToCreateOrderFromEquipment($equipment_id, $customer_id);
        $data['is_offer'] = 0;
        return view('order.form', $data);
    }

    /**
     * [sendOrDownloadOrder description]
     * @return [type] [description]
     */
    public function sendOrDownloadOrder()
    {
        try {
            $input = Request::all();
            if ($input['type'] == 1) {
                $fileData = Order::downloadOrderReportByStatus($input);
                if ($fileData && file_exists($fileData['filePath'])) {
                    $headers = array('Content-Type: application/pdf');
                    return Response::download($fileData['filePath'], $fileData['fileName'], $headers);
                } else {
                    return Redirect::back()->with('error', __('main.something_went_wrong'));
                }
            } else {
                $order_details = Order::sendOrderMailFromIndex($input['order_id']);
                if ($order_details) {
                    return Redirect::back()->with('success', __('main.emailsent'));
                } else {
                    return Redirect::back()->with('warning', __('main.emailnotsent'));
                }
            }
        } catch (\Exception $e) {
            return Redirect::route($this->route)->with($this->warning, __('main.something_went_wrong'));
        }
    }

    /**
     * [sendOfferOrderMail description]
     * @return [type] [description]
     */
    public function sendOfferOrderMail()
    {
        try {
            $input             = Request::all();
            $input['is_offer'] = 0;
            $order_details     = Order::sendOrderMail($input, false);
            if ($order_details) {
                return Redirect::back()->with('success', __('main.emailsent'));
            } else {
                return Redirect::back()->with('warning', __('main.emailnotsent'));
            }
        } catch (\Exception $e) {
            echo $e;
            exit;
        }
    }

    /**
     * [getCustomerOrderByProduct description]
     * @param  boolean $product_id [description]
     * @return [type]              [description]
     */
    public function getCustomerOrderByProduct($product_id = false)
    {
        $data['customer_orders'] = Order::getCustomerOrderByProduct($product_id);
        return view('warehousedetails/customer_order', $data);
    }

    /**
     * [getCustomerOrderByWarehouse description]
     * @param  boolean $product_id   [description]
     * @param  boolean $location_id  [description]
     * @param  boolean $warehouse_id [description]
     * @return [type]                [description]
     */
    public function getCustomerOrderByWarehouse($product_id = false, $location_id = false, $warehouse_id = false)
    {
        $data['customer_orders'] = Order::getCustomerOrderByProductAndWarehosue($product_id, $location_id, $warehouse_id);
        return view('warehousedetails/customer_order', $data);
    }

    /**
     * [picklist description]
     * @return [type] [description]
     */
    public function picklist()
    {
        $input    = Request::all();
        $fileData = $this->createPicklistPDF($input);
        if ($fileData && file_exists($fileData['filePath'])) {
            $headers = array('Content-Type: application/pdf');
            return Response::download($fileData['filePath'], $fileData['fileName'], $headers);
        } else {
            return Redirect::back()->with('error', __('main.something_went_wrong'));
        }
    }

    /**
     * [packlist description]
     * @return [type] [description]
     */
    public function packlist()
    {
        $input    = Request::all();
        $fileData = $this->createPackListPDF($input);
        if ($fileData && file_exists($fileData['filePath'])) {
            Session::put('filetodownload', true);
            Session::put('filetodownloadData', $fileData);
            return Redirect::back()->with($fileData['rest_order'] == 0 ? 'success' : 'info', $fileData['rest_order'] == 0 ? __('main.packlist_sucess_msg') : __('main.packlist_sucess_msg_with_rest_order') . ' - ' . $fileData['rest_order_number']);
        } else {
            return Redirect::back()->with('error', __('main.something_went_wrong'));
        }
    }

    /**
     * [downloadFile description]
     * @return [type] [description]
     */
    public function downloadFile()
    {
        $fileData = Session::get('filetodownloadData');
        $headers  = array('Content-Type: application/pdf');
        Session::put('filetodownload', false);
        Session::put('filetodownloadData', '');
        return Response::download($fileData['filePath'], $fileData['fileName'], $headers);
    }

    /**
     * [createOrderinUNI description]
     * @return [type] [description]
     */
    public function createOrderinUNI()
    {
        try {
            $input_data       = Request::all();
            $order_details    = Order::whereId($input_data['order_id'])->first();
            $customer_details = Customer::where('id', $order_details->customer_id)->first();
            if ($customer_details && $customer_details->uni_id) {
                $order_details = Order::createCustomerUNIOrder($input_data);
                if ($order_details) {
                    return Redirect::back()->with('success', __('main.order_createsuccess_in_uni'));
                } else {
                    return Redirect::back()->with('error', __('main.something_went_wrong'));
                }
            } else {
                return Redirect::back()->with('error', __('main.customer_not_in_uni'));
            }
        } catch (Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            register_error_log($error_message, 'uni.log');
            return Redirect::back()->with('error', __('main.something_went_wrong'));
        }
    }

    /**
     * [getPacklistBtnStatus description]
     * @return [type] [description]
     */
    public function getPacklistBtnStatus($order_id)
    {
        try {
            $data['shipping_data']         = Shipping::whereNull('shipment_status')->where('order_id', $order_id)->count();
            $data['picked_product']        = OrderMaterial::where('order_id', $order_id)->whereNull('reference_id')->whereNull('is_logistra')->whereNull('shippment_id')->where('quantity', '>', 0)->count();
            $data['pick_product']          = OrderMaterial::where('order_id', $order_id)->whereNull('is_logistra')->where('quantity', '=', 0)->where('is_package', 0)->count();
            $data['pack_list_history']     = PacklistHistory::where('order_id', $order_id)->count();
            $data['non_stockable_product'] = OrderMaterial::where('order_id', $order_id)->where('stockable', 0)->where('quantity', '=', 0)->count();
            return json_encode(array("status" => "success", "data" => $data));
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            register_error_log($error_message, 'order.log');
            return json_encode(array("status" => "error", "message" => __('main.something_went_wrong')));
        }
    }

    /**
     * [downloadLastPackList description]
     * @param  [type] $order_id [description]
     * @return [type]           [description]
     */
    public function downloadLastPackList($order_id)
    {
        if ($order_id) {
            $file = PickPackList::generateLatestPacklistPDF($order_id);
            if ($file['filePath']) {
                $headers = array('Content-Type: application/pdf');
                return Response::download($file['filePath'], $file['fileName'], $headers);
            }
        }
        return Redirect::back()->with($this->error, __('main.something_went_wrong'));
    }
}

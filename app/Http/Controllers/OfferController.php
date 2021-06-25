<?php
namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\OfferSettings;
use App\Models\Order;
use App\Models\OrderMaterial;
use App\Models\Product;
use Illuminate\Support\Facades\Redirect;
use Request;
use Response;
use Session;

class OfferController extends Controller
{

    private $orderObject;
    protected $route                      = 'main.offer.index';
    protected $offer_route                = 'main.offer.index';
    protected $success                    = 'success';
    protected $error                      = 'error';
    protected $warning                    = 'warning';
    protected $deletemsg                  = 'main.offer_deletesuccess';
    protected $no_result                  = 'main.notfound';
    protected $offer_update_message       = 'main.offer_updatesuccess';
    protected $offer_order_created        = 'main.offer_order_created';
    protected $offer_create_message       = 'main.offer_createsuccess';
    protected $offer_order_create_message = 'main.offer_order_create_message';

    public function __construct(Order $orderObject)
    {
        $this->orderObject = $orderObject;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($order_status = false)
    {
        try {
            $filter_data = @Session::get('offer_search') ? @Session::get('offer_search') : [];
            @Request::input() ? @Session::put('offer_search', array_merge($filter_data, @Request::input())) : '';
            $data = Order::getOrderPaginated(Session::get('offer_search'), @Session::get('offer_search')['search_status'], false, false, 1);
            return view('offer.index', $data);
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            register_error_log($error_message, 'offer.log');
            return Redirect::back()->with('error', trans('main.something_went_wrong'));
        }
    }

    /**
     * [show description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function show($id)
    {
    }

    /**
     * [create description]
     * @return [type] [description]
     */
    public function create()
    {
        try {
            $data                     = Order::getDataToCreteOrders();
            $data['is_offer']         = 1;
            $data['invoice_comments'] = OfferSettings::whereType(1)->select('data')->first();
            if (@$data) {
                return view('order.form', $data);
            }
            return Redirect::route($this->route)->with($this->warning, __('main.something_went_wrong'));
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            register_error_log($error_message, 'offer.log');
            return Redirect::back()->with('error', trans('main.something_went_wrong'));
        }
    }

    /**
     * [edit description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function edit($id)
    {
        try {
            $data                  = Order::editOrder($id);
            $data['is_offer']      = 1;
            $data['disable_value'] = 0;
            if ($data['orders']->offer_order_id) {
                $data['disable_value'] = 1;
            }
            if ($data == 0) {
                return Redirect::route($this->route)->with($this->warning, __($this->no_result));
            }
            return view('order.edit', $data);
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            register_error_log($error_message, 'offer.log');
            return Redirect::back()->with('error', trans('main.something_went_wrong'));
        }
    }

    /**
     * [listOfferMaterials description]
     * @param  [type] $order_id [description]
     * @return [type]           [description]
     */
    public function listOfferMaterials($order_id)
    {
        try {
            $data = @Session::get('offer_product_search_form') ? @Session::get('offer_product_search_form') : [];
            @\Request::input() ? Session::put('offer_product_search_form', array_merge($data, @\Request::input())) : '';
            $data                  = OrderMaterial::getOrderMaterials(Session::get('offer_product_search_form'), $order_id, false, false, 1);
            $data['disable_value'] = 0;
            if ($data['orders']->offer_order_id) {
                $data['disable_value'] = 1;
            }
            return view('ordermaterial.offerMaterial', $data);
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            register_error_log($error_message, 'offer.log');
            return Redirect::back()->with('error', trans('main.something_went_wrong'));
        }
    }

    /**
     * [customStore description]
     * @return [type] [description]
     */
    public function customStore()
    {
        try {
            $input                  = Request::all();
            $order_material_details = OrderMaterial::storeOfferMaterial($input);
            if ($order_material_details) {
                return json_encode(array("status" => "success", "data" => $order_material_details));
            } else {
                return json_encode(array("status" => "error", "message" => __('main.something_went_wrong')));
            }
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            register_error_log($error_message, 'offer.log');
            return json_encode(array("status" => "error", "message" => __('main.something_went_wrong')));
        }
    }

    /**
     * [destroy description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function destroy($id)
    {
        $orders = Order::findOrFail($id);
        $orders->delete();
        return Redirect::route($this->route)->with($this->success, __($this->deletemsg));
    }

    /**
     * [update description]
     * @param  OrderRequest $request [description]
     * @param  [type]       $id      [description]
     * @return [type]                [description]
     */
    public function update(OrderRequest $request, $id)
    {
        $request_data = $request->all();
        $order_number = Order::updateCustomerOrder($request_data, $id, 1);
        $message      = __($this->offer_update_message);
        if ($request_data['status'] == 3) {
            if ($order_number) {
                return Redirect::action('OfferController@index')->with('info', $order_number . ' - ' . __($this->offer_order_create_message));
            } else {
                return Redirect::action('OfferController@index')->with('error', __('main.something_went_wrong'));
            }
        }
        if (@$request_data['update']) {
            return Redirect::back()->with($this->success, $message);
        } else {
            return Redirect::action('OfferController@index')->with($this->success, $message);
        }
    }

    /**
     * [store description]
     * @param  OrderRequest $request [description]
     * @return [type]                [description]
     */
    public function store(OrderRequest $request)
    {
        $request_data  = $request->all();
        $order_details = Order::createCustomerOrder($request_data);
        if ($request_data['order_submit_btn'] == 'close') {
            return Redirect::route($this->route)->with($this->success, __($this->offer_create_message));
        } else {
            return Redirect::route('main.offer.edit', $order_details['id'])->with($this->success, __($this->offer_create_message));
        }
    }
}

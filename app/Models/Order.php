<?php
namespace App\Models;

use App\Helpers\GanticHelper;
use App\Jobs\OrderOfferMail;
use App\Models\Customer;
use App\Models\Department;
use App\Models\DropdownHelper;
use App\Models\Equipment;
use App\Models\OfferOrderProduct;
use App\Models\Order;
use App\Models\OrderContactPerson;
use App\Models\OrderDepartment;
use App\Models\OrderMaterial;
use App\Models\Product;
use App\Models\Shipping;
use App\Models\UniIntegration;
use App\Models\User;
use App\Models\UserOrder;
use App\Traits\Offer;
use Auth;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use PDF;
use Session;
use View;

class Order extends Model
{
    use Offer, SoftDeletes, Sortable;
    protected $table     = 'order';
    public $incrementing = false;
    public $timestamps   = true;

    protected $fillable = array(
        'id',
        'customer_id',
        'ordered_by',
        'order_number',
        'order_date',
        'project_number',
        'visiting_address',
        'controll_type',
        'comments',
        'invoice_customer',
        'order_invoice_comments',
        'email_invoice',
        'status',
        'all_approved',
        'customer_sign',
        'deliveraddress',
        'pmt_term',
        'contact',
        'phone',
        'email',
        'date_completed',
        'invoice_type',
        'priority',
        'order_category',
        'equipment_id',
        'order_invoice_status',
        'is_delete',
        "sum",
        "mva",
        "round_down",
        "total",
        'deliveraddress1',
        'deliveraddress2',
        'deliveraddress_zip',
        'deliveraddress_city',
        'is_category_enable',
        'order_user',
        'added_by',
        'updated_by',
        'offer_id',
        'visitingAddress',
        'visitingAddress1',
        'visitingAddress2',
        'visitingAddressZip',
        'visitingAddressCity',
        'offer_due_date',
        'uni_status',
        'is_res_order',
        'res_order_id',
        'is_offer',
        'offer_order_id',
        'offer_number',
        'offer_order_number',
        'invoice_number'
    );
    protected $sortable = array('order_number', 'project', 'start_date', 'order_date', 'date_completed', 'is_res_order', 'offer_number', 'offer_order_number');
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id', 'id');
    }

    public function invoiceCustomer()
    {
        return $this->belongsTo('App\Models\Customer', 'invoice_customer', 'id');
    }

    public function responsibleUser()
    {
        return $this->belongsTo('App\Models\User', 'order_user', 'id');
    }

    public function users()
    {
        return $this->belongsTo('App\Models\User', 'project_owner', 'id');
    }

    public function order_contact_person()
    {
        return $this->hasMany('App\Models\OrderContactPerson', 'order_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo('App\Models\Department', 'department_id', 'id');
    }

    public function orderedBy()
    {
        return $this->belongsTo('App\Models\Contact', 'ordered_by', 'id');
    }

    public function equipment()
    {
        return $this->belongsTo('App\Models\Equipment');
    }

    public function order_department()
    {
        return $this->hasMany('App\Models\OrderDepartment', 'order_id', 'id');
    }

    public function orderMaterial()
    {
        return $this->hasMany('App\Models\OrderMaterial', 'order_id', 'id');
    }

    public function whsReturnOrder()
    {
        return $this->hasMany('App\Models\WarehouseOrder', 'customer_order_id', 'id');
    }

    /**
     * [offerOrderProducts description]
     * @return [type] [description]
     */
    public function offerOrderProducts()
    {
        return $this->hasMany('App\Models\OfferOrderProduct', 'order_id', 'id');
    }

    public static function getOrderPaginated($conditions = false, $status = false, $order_status = false, $customer_id = false, $is_offer = 0)
    {
        $usertype      = Session::get('usertype');
        $user_details  = User::findOrFail(Auth::getUser()->id)->toArray();
        $department_id = "";
        if ($usertype != "Admin" && $usertype != "Administrative") {
            $department_id = $user_details['department_id'];
        }
        $orders = Order::selectRAW('customer.name as customer_name, customer.email as customer_email, concat(COALESCE(internalnbr,""), IF(LENGTH(internalnbr), " - ", ""),  COALESCE(sn, ""), IF(LENGTH(sn), " - ", ""), COALESCE(description, "") ) as equipment_name, d2.value_en as order_category_en, d2.value_no as order_category_no, d1.value_en as status_en, d1.value_no as status_no');
        $orders->leftjoin('customer', 'customer_id', '=', 'customer.id');
        $orders->leftjoin('order_department', 'order.id', '=', 'order_id');
        $orders->leftjoin('user_order', 'order.id', '=', 'user_order.order_id');
        $orders->leftjoin('equipment', 'order.equipment_id', '=', 'equipment.id');
        $orders->leftjoin('dropdown_helper as d2', 'order.order_category', '=', 'd2.key_code')->where('d2.group_code', '=', '007');
        $orders->leftjoin('dropdown_helper as d1', 'order.status', '=', 'd1.key_code')->where('d1.group_code', '=', $is_offer == 0 ? '005' : '020');
        $orders->addSelect('order.*')->distinct();
        $orders->GroupBy('order.id')->with('whsReturnOrder');
        $orders->where('is_offer', $is_offer);

        if (isset($conditions['search_by_department']) && $conditions['search_by_department'] != '' && $conditions['search_by_department'] != 'all') {
            $department = $conditions['search_by_department'];
            $orders->where('order_department.department_id', '=', $department);
        }
        if (isset($conditions['search_by_order_users']) && $conditions['search_by_order_users'] != "" && $conditions['search_by_order_users'] != "all") {
            $search_str   = $conditions['search_by_order_users'];
            $search_field = Order::orderSearchByUser($search_str);
            $orders->where(function ($query) use ($search_field) {
                $query->where($search_field['field'], '=', $search_field['value']);
            });
        }

        if ($is_offer == 0) {
            if ($order_status == 2) {
                $orders->whereIn('order.status', ['5']);
            } else {
                $orders->whereNotIn('order.status', ['5']);
                if (isset($conditions['search_status']) && $conditions['search_status'] != '') {
                    $search = $conditions['search_status'];
                    $orders->where(function ($query) use ($search) {
                        $query->orwhere('order.status', '=', $search);
                    });
                }
            }
        } else {
            if (isset($conditions['search_status']) && $conditions['search_status'] != '') {
                $search = $conditions['search_status'];
                $orders->where(function ($query) use ($search) {
                    $query->orwhere('order.status', '=', $search);
                });
            }
        }

        if (isset($conditions['search']) && $conditions['search'] != '' || isset($conditions['archived_search']) && $conditions['archived_search'] != '') {
            $search = @$conditions['search'] ? $conditions['search'] : '';
            $orders->where(function ($query) use ($search) {
                $query->orwhere('order.order_number', 'LIKE', '%' . $search . '%');
                $query->orwhere('order.offer_number', 'LIKE', '%' . $search . '%');
                $query->orwhere('order.offer_order_number', 'LIKE', '%' . $search . '%');
                $query->orwhere('order.project_number', 'LIKE', '%' . $search . '%');
                $query->orwhere('order_date', 'LIKE', '%' . formatSearchDate($search) . '%');
                $query->orwhere('date_completed', 'LIKE', '%' . formatSearchDate($search) . '%');
                $query->orwhereHas('customer', function ($query) use ($search) {
                    $query->where('customer.name', 'LIKE', '%' . $search . '%');
                });
                $query->orwhereHas('equipment', function ($query) use ($search) {
                    $query->where('equipment.internalnbr', 'LIKE', '%' . $search . '%');
                    $query->where('equipment.description', 'LIKE', '%' . $search . '%');
                });
                $language = Session::get('language') ? Session::get('language') : 'no';
                if ($language == 'no') {
                    $query->orwhere('d1.value_no', 'LIKE', '%' . $search . '%');
                    $query->orwhere('d2.value_no', 'LIKE', '%' . $search . '%');
                } else {
                    $query->orwhere('d1.value_en', 'LIKE', '%' . $search . '%');
                    $query->orwhere('d2.value_en', 'LIKE', '%' . $search . '%');
                }
            });
        }
        if ($department_id) {
            $orders->whereIN('order_department.department_id', array($department_id));
        }
        if (@$customer_id) {
            $orders->where('order.customer_id', '=', $customer_id);
        }

        // Show the orders to users, assigned to has some value then we need to show the order only for assigned users. If assigned to is empty then we need to show orders to all users in the department.
        $user_id       = Session::get('currentUserID');
        $department_id = Auth::User()->department_id;
        if ($usertype == "User") {
            $orders->where(function ($query) use ($user_id, $department_id) {
                $query->whereExists(function ($query) use ($user_id) {
                    $query->select(DB::raw(1))
                        ->from('user_order')
                        ->whereRaw("user_order.order_id = order.id")
                        ->whereRaw("user_order.user_id = '" . $user_id . "'");
                });
                $query->orwhereNotExists(function ($query) use ($user_id, $department_id) {
                    $query->select(DB::raw(1))
                        ->from('user_order')
                        ->leftjoin('user', 'user_order.user_id', '=', 'user.id')
                        ->leftjoin('department', 'user.department_id', '=', 'department.id')
                        ->whereRaw("department.id = '" . $department_id . "'")
                        ->whereRaw("user_order.user_id = user.id")
                        ->whereRaw("user_order.order_id = order.id");
                });
            });
        }
        $paginate_size = Session::get('paginate_size') ? Session::get('paginate_size') : 10;
        $data          = Order::getDropDownValuesForFilter($usertype, $customer_id);
        if ($is_offer == 1) {
            $data['orders'] = $orders->sortable(['offer_number' => 'desc'])->paginate($paginate_size);
        } else {
            $data['orders'] = $orders->sortable(['order_number' => 'desc'])->paginate($paginate_size);
        }
        return $data;
    }

    /**
     * [getDropDownValuesForFilter description]
     * @param  [type] $usertype    [description]
     * @param  [type] $customer_id [description]
     * @return [type]              [description]
     */
    public static function getDropDownValuesForFilter($usertype, $customer_id)
    {
        $data                           = array();
        $language                       = Session::get('language') ? Session::get('language') : 'no';
        $data['orders_search_categoty'] = array('my_order' => __('main.my_order'), 'my_department' => __('main.my_department'), 'assigned_to_me' => __('main.assigned_to_me'));
        $data['departments']            = Department::orderby('Name', 'asc')->where('status', '=', '0')->pluck('name', 'id');
        $data['order_categories']       = DropdownHelper::where('language', $language)->where('groupcode', '007')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        if ($usertype == "User") {
            $data['order_status'] = DropdownHelper::where('language', $language)->where('groupcode', '005')->where('keycode', '!=', '4')->where('keycode', '!=', '5')->where('keycode', '!=', '6')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        } else {
            $data['order_status'] = DropdownHelper::where('language', $language)->where('groupcode', '005')->where('keycode', '!=', '6')->where('keycode', '!=', '5')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        }
        $data['offer_status'] = DropdownHelper::where('language', $language)->where('groupcode', '020')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        if (@$customer_id) {
            $data['customer_id'] = 'customer_id=' . $customer_id;
        }
        return $data;
    }

    public static function orderSearchByUser($str)
    {
        if ($str) {
            $result = [];
            switch ($str) {
                case 'my_order':
                    $result = array('field' => 'order.order_user', 'value' => Session::get('currentUserID'));
                    break;
                case 'my_department':
                    $result = array('field' => 'order_department.department_id', 'value' => Auth::user()->department_id);
                    break;
                case 'assigned_to_me':
                    $result = array('field' => 'user_order.user_id', 'value' => Session::get('currentUserID'));
                    break;
                case 'need_approval':
                    $result = array('field' => 'order.all_approved', 'value' => '0');
                    break;
            }
            return $result;
        }
    }
    /**
     * [titleSortable description]
     * @param  [type] $query     [description]
     * @param  [type] $direction [description]
     * @return [type]            [description]
     */
    public function titleSortable($query, $direction)
    {
        $query->orderby('equipment.sn', $direction);
    }
    /**
     * [customerSortable description]
     * @param  [type] $query     [description]
     * @param  [type] $direction [description]
     * @return [type]            [description]
     */
    public function customerSortable($query, $direction)
    {
        $query->orderby('customer.name', $direction);
    }

    /**
     * [order_project_numberSortable description]
     * @param  [type] $query     [description]
     * @param  [type] $direction [description]
     * @return [type]            [description]
     */
    public function order_project_numberSortable($query, $direction)
    {
        $query->orderby('order.project_number', $direction);
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
            $query->orderby('status_no', $direction);
        } else {
            $query->orderby('status_en', $direction);
        }
    }

    /**
     * [ordercategorysortSortable description]
     * @param  [type] $query     [description]
     * @param  [type] $direction [description]
     * @return [type]            [description]
     */
    public function ordercategorysortSortable($query, $direction)
    {
        $language = Session::get('language') ? Session::get('language') : 'no';
        if ($language == 'no') {
            $query->orderby('order_category_no', $direction);
        } else {
            $query->orderby('order_category_en', $direction);
        }
    }

    // order edit
    public static function editOrder($id)
    {
        $edit_order = Order::select('order_number')->where('id', '=', $id)->first();
        if ($edit_order == null) {
            return 0;
        }
        $usertype                         = Session::get('usertype');
        $language                         = Session::get('language') ? Session::get('language') : 'no';
        $data['selected_contact_persons'] = OrderContactPerson::getContactPersonIdByOrderId($id);
        $data['selected_users']           = UserOrder::getUserIdByOrderId($id);
        $data['orders']                   = Order::with('orderedBy', 'customer')->find($id);
        $data['customer_address']         = Order::getCustomerAddress($data['orders']->customer_id);
        $data['pmt_terms']                = Order::getPmtTerms();
        Order::formatOrderDates($data);
        $data['customers']             = Customer::where('status', '=', '0')->orderby('name', 'asc')->pluck('name', 'id');
        $data['departments']           = Department::orderby('Name', 'asc')->where('status', '=', '0')->pluck('name', 'id');
        $data['contacts_with_mobile']  = Contact::getContactDetailByCustomerId($data['orders']->customer_id);
        $data['contacts']              = Contact::where('customer_id', '=', $data['orders']->invoice_customer)->pluck('name', 'id');
        $department                    = OrderDepartment::getOrderDepartmentIDByOrderId($id);
        $data['orders']->department_id = $department;
        $data['users']                 = User::selectRaw('concat(if(first_name is null, "", concat(first_name, " ")) ,if(last_name is null, "", last_name)) as name, id')->whereIN('department_id', $department)->where('activated', '=', 0)->orderBy('first_name', 'asc')->pluck('name', 'id');
        $data['allUsers']              = $data['department_chiefs']              = User::selectRaw('concat(if(first_name is null, "", concat(first_name, " ")) ,if(last_name is null, "", last_name)) as name, id')->where('activated', '=', 0)->orderBy('first_name', 'asc')->pluck('name', 'id');
        $data['allstatus']             = DropdownHelper::where('language', $language)->where('groupcode', '005')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        $data['allofferstatus']        = DropdownHelper::where('language', $language)->where('groupcode', '020')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        if (@$data['orders']->status == 1) {
            $data['order_status'] = DropdownHelper::where('language', $language)->where('groupcode', '005')->whereIn('keycode', [1, 2])->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        } else {
            $data['order_status'] = DropdownHelper::where('language', $language)->where('groupcode', '005')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        }
        $data['priority']       = DropdownHelper::where('language', $language)->where('groupcode', '006')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        $data['order_category'] = DropdownHelper::where('language', $language)->whereIn('keycode', ["01", "03", "06", "08"])->where('groupcode', '007')->orderBy('keycode', 'asc')->pluck('label', 'keycode');

        $data['equipments']                 = Equipment::select(DB::Raw("concat(COALESCE(internalnbr,''), IF(LENGTH(internalnbr), ' - ', ''),  COALESCE(sn,''), IF(LENGTH(sn), ' - ', ''), COALESCE(description,'') ) AS name,id"))->where('customer_id', '=', $data['orders']->customer_id)->orderby('sn', 'asc')->pluck('name', 'id');
        $sent_mail_notification             = DB::table('offer_order_mail_history')->where('id', DB::raw("(select max(`id`) from offer_order_mail_history)"))->where('order_id', '=', $id)->first();
        $data['sent_mail_notification_msg'] = '';
        if ($sent_mail_notification != null) {
            if ($sent_mail_notification->order_status == 1) {
                $data['sent_mail_notification_msg'] = trans('main.offer') . ' ' . __('main.send') . ' ' . $sent_mail_notification->user_name . ' ' . __('main.on') . ' ' . date('d.m.Y', strtotime($sent_mail_notification->send_date));
            } else {
                $data['sent_mail_notification_msg'] = __('main.order') . ' ' . __('main.send') . ' ' . $sent_mail_notification->user_name . ' ' . __('main.on') . ' ' . date('d.m.Y', strtotime($sent_mail_notification->send_date));
            }
        }
        $data['customerName']   = Customer::find($data['orders']->customer_id)->name;
        $data['mailHistory']    = OrderEmailHistory::where('order_id', $id)->orderBy('created_at', 'desc')->get();
        $data['offer_status']   = Self::getOfferStatusForEdit($data['orders']->status, @$data['orders']->order_id);
        $data['material_count'] = OrderMaterial::where('order_id', $id)->where('quantity', '>', 0)->count();
        return $data;
    }

    /**
     * [formatOrderDates description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public static function formatOrderDates($data)
    {
        $data['orders']->order_date     = $data['orders']->order_date ? date('d.m.Y', strtotime($data['orders']->order_date)) : '';
        $data['orders']->date_completed = $data['orders']->date_completed ? date('d.m.Y', strtotime($data['orders']->date_completed)) : '';
        $data['orders']->offer_due_date = @$data['orders']->offer_due_date ? date('d.m.Y', strtotime($data['orders']->offer_due_date)) : '';
        return $data;
    }

    /**
     * [getDataToCreteOrders description]
     * @return [type] [description]
     */
    public static function getDataToCreteOrders()
    {
        try {
            $data['orders']                  = new Order();
            $data['orders']->order_date      = date('d.m.Y');
            $data['pmt_terms']               = Order::getPmtTerms();
            $data['customers']               = Customer::where('status', '=', '0')->orderby('name', 'asc')->pluck('name', 'id');
            $usertype                        = Session::get('usertype');
            $data['departments']             = Department::orderby('Name', 'asc')->where('status', '=', '0')->pluck('name', 'id');
            $data['orders']['department_id'] = Auth::User()->department_id;
            $data['users']                   = User::selectRaw('concat(if(first_name is null, "", concat(first_name, " ")) ,if(last_name is null, "", last_name)) as name, id')->where('department_id', Auth::User()->department_id)->where('activated', '=', 0)->pluck('name', 'id');
            $data['department_chiefs']       = User::selectRaw('concat(if(first_name is null, "", concat(first_name, " ")) ,if(last_name is null, "", last_name)) as name, id')->where('activated', '=', 0)->orderBy('first_name', 'asc')->pluck('name', 'id');
            $language                        = Session::get('language') ? Session::get('language') : 'no';
            $data['order_status']            = DropdownHelper::where('language', $language)->where('groupcode', '005')->whereIn('keycode', [1, 2])->orderBy('keycode', 'asc')->pluck('label', 'keycode');
            $data['priority']                = DropdownHelper::where('language', $language)->where('groupcode', '006')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
            $data['order_category']          = DropdownHelper::where('language', $language)->whereIn('keycode', ["01", "03", "06", "08"])->where('groupcode', '007')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
            $data['offer_status']            = Self::getOfferStatus(6);
            return $data;
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            GanticHelper::errorLog($error_message);
            return null;
        }
    }

    /**
     * [createCustomerOrder description]
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public static function createCustomerOrder($input)
    {
        $input['id']             = GanticHelper::gen_uuid();
        $input['order_date']     = date('Y-m-d', strtotime($input['order_date']));
        $input['date_completed'] = $input['date_completed'] ? date('Y-m-d', strtotime($input['date_completed'])) : null;
        $input['offer_due_date'] = @$input['offer_due_date'] ? date('Y-m-d', strtotime($input['offer_due_date'])) : null;
        if ($input['is_offer'] == 0) {
            $input['order_number'] = Order::getOrderNumber();
        } else {
            $input['offer_number'] = Self::getOfferNumber();
        }
        $input['caseworker']         = $input['added_by']         = Session::get('currentUserID');
        $input['total']              = @$input['invoice_total'] ? @$input['invoice_total'] : 0.00;
        $input['email']              = @$input['email2'];
        $input['is_category_enable'] = 0;
        $input['order_user']         = @$input['ordered_created_by'] ? $input['ordered_created_by'] : Session::get('currentUserID');
        $order_details               = Order::create($input);
        Order::storeOrderRelationData($input, $order_details);
        return $order_details;
    }

    /**
     * [getOrderNumber description]
     * @return [type] [description]
     */
    public static function getOrderNumber()
    {
        $order_number = "10000";
        $result       = Order::select(DB::raw("MAX(order_number )  as order_number"))->first();
        if (isset($result->order_number)) {
            $order_number = $result->order_number;
            $order_number = sprintf('%04d', $order_number + 1);
        } else {
            $order_number = '10000';
        }
        return $order_number;
    }

    /**
     * [updateCustomerOrder description]
     * @param  [type] $input [description]
     * @param  [type] $id    [description]
     * @return [type]        [description]
     */
    public static function updateCustomerOrder($input, $id)
    {
        $order                   = Order::find($id);
        $order_status_from_DB    = $order->status;
        $input['order_date']     = date('Y-m-d', strtotime($input['order_date']));
        $input['date_completed'] = $input['date_completed'] ? date('Y-m-d', strtotime($input['date_completed'])) : null;
        $input['offer_due_date'] = @$input['offer_due_date'] ? date('Y-m-d', strtotime($input['offer_due_date'])) : null;
        $input['updated_by']     = $input['caseworker']     = Session::get('currentUserID');
        $input['total']          = @$input['invoice_total'];
        $input['email']          = @$input['email2'];
        $input['order_user']     = @$input['ordered_created_by'] ? $input['ordered_created_by'] : Session::get('currentUserID');
        if ($input['status'] == 5) {
            $input['header'] = Customer::where('id', '=', $input['customer_id'])->get();
        }
        $order->fill($input);
        $order->save();
        Order::updateCustomerRelationData($input, $id);
        if ($order_status_from_DB != $input['status']) {
            $order_history_input['id']            = GanticHelper::gen_uuid();
            $order_history_input['order_id']      = $id;
            $order_details['order_details']       = Order::find($id);
            $json_format_input                    = json_encode($order_details);
            $order_history_input['order_data']    = $json_format_input;
            $order_history_input['modified_date'] = date('Y-m-d H:i:s');
            $order_history_input['modified_by']   = Session::get('currentUserID');
            OrderHistory::create($order_history_input);
        }
        if ($input['is_offer'] == 1 && @$input['status'] == 2 && $order_status_from_DB != 2) {
            Order::sendOrderMailFromIndex($id);
        }
        if ($input['is_offer'] == 1 && @$input['status'] == 3 && !$order->offer_order_id) {
            $new_order = self::createOrderFromOffer($id);
            return $new_order;
        }
        return $order_status_from_DB;
    }

    /**
     * [storeOrderRelationData description]
     * @param  [type] $input         [description]
     * @param  [type] $order_details [description]
     * @return [type]                [description]
     */
    public static function storeOrderRelationData($input, $order_details = false)
    {
        // order department
        if (@$input['department_id']) {
            $order_department = $input['department_id'];
            if (!empty($order_department)) {
                $order_department_input['department_id'] = $order_department;
                $order_department_input['order_id']      = $input['id'];
                OrderDepartment::create($order_department_input);
            }
        }

        // order contact persons
        if (@$input['contact_person_id']) {
            $order_contact = $input['contact_person_id'];
            foreach ($order_contact as $contactas) {
                $order_contact_input['contact_person_id'] = $contactas;
                $order_contact_input['order_id']          = $input['id'];
                OrderContactPerson::create($order_contact_input);
            }
        }

        // user orders
        if (@$input['order_user_id']) {
            $order_user = $input['order_user_id'];
            foreach ($order_user as $orderuser) {
                if ($orderuser != "" && $orderuser != null) {
                    $order_user_input['user_id']  = $orderuser;
                    $order_user_input['order_id'] = $input['id'];
                    $user_details                 = UserOrder::create($order_user_input);
                }
            }
        }
        // update equipment last_repair date
        if (@$input['order_category'] == "01" && isset($input['equipment_id'])) {
            Equipment::updateLastRepairDate($input['equipment_id'], $input['order_date']);
        }

    }

    /**
     * [updateCustomerRelationData description]
     * @param  [type] $input [description]
     * @param  [type] $id    [description]
     * @return [type]        [description]
     */
    public static function updateCustomerRelationData($input, $id)
    {
        OrderDepartment::where('order_id', $id)->delete();
        OrderContactPerson::where('order_id', $id)->delete();
        UserOrder::where('order_id', $id)->delete();
        $input['id'] = $id;
        Order::storeOrderRelationData($input);
    }

    /**
     * [getPmtTerms description]
     * @return [type] [description]
     */
    public static function getPmtTerms()
    {
        try {
            $language = Session::get('language') ? Session::get('language') : 'no';
            return DropdownHelper::where('language', $language)->where('groupcode', '009')->orderby('keycode', 'asc')->pluck('label', 'keycode');
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            GanticHelper::errorLog($error_message);
        }
    }

    /**
     * [getUnits description]
     * @return [type] [description]
     */
    public static function getUnits()
    {
        $language      = Session::get('language') ? Session::get('language') : 'no';
        $data['units'] = DropdownHelper::where("groupcode", "=", "010")->where('language', '=', $language)->orderby("keycode", "asc")->pluck("label", "keycode");
        return $data['units'];
    }

    public static function getCustomerAddress($customer_id)
    {
        try {
            if (@$customer_id) {
                return CustomerAddress::where('customer_id', '=', $customer_id)->orderBy('address1', 'asc')->pluck('address1', 'id');
            } else {
                return array();
            }
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            GanticHelper::errorLog($error_message);
        }
    }

    public static function upload_file($request, $file, $files = false)
    {
        if ($request->hasFile($file) && $request->file($file)->getSize() > 0) {
            $destinationPath = storage_path() . "/uploads/order/";
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $filename                   = $request->file($file)->getClientOriginalName();
            $extension                  = $request->file($file)->getClientOriginalExtension();
            $fileName                   = GanticHelper::gen_uuid() . '.' . $extension;
            $return['fileName']         = $fileName;
            $return['filePath']         = '/uploads/order/' . $fileName;
            $return['fileSize']         = $request->file($file)->getSize();
            $return['fileType']         = $request->file($file)->getMimeType();
            $return['fileExtension']    = $extension;
            $return['fileOriginalName'] = $filename;
            Image::make($request->file($file)->getRealPath())->save($destinationPath . $fileName);
            $files = json_encode($return);
        }
        return $files;
    }

    public static function getDataToCreateOrderFromEquipment($equipment_id, $customer_id)
    {
        $language          = Session::get('language') ? Session::get('language') : 'no';
        $usertype          = Session::get('usertype');
        $data['pmt_terms'] = Order::getPmtTerms();
        // get equipments
        $data['equipments'] = Equipment::select(DB::Raw("concat(COALESCE(internalnbr,''), IF(LENGTH(internalnbr), ' - ', ''),  COALESCE(sn,''), IF(LENGTH(sn), ' - ', ''), COALESCE(description,'') ) AS name,id"))->where('id', '=', $equipment_id)->orderby('sn', 'asc')->pluck('name', 'id');

        $data['products'] = array();
        // get the contacts for selected customer
        $data['contacts_with_mobile'] = Contact::getContactDetailByCustomerId($customer_id);
        $data['customer_address']     = Order::getCustomerAddress($customer_id, 3);
        $data['contacts']             = Contact::where('customer_id', '=', $customer_id)->pluck('name', 'id');
        // for hourlogg
        $data['orders']                   = array();
        $data['orders']['priority']       = "02";
        $data['orders']['order_date']     = date('d.m.Y');
        $data['orders']['order_category'] = "01";

        // here set the custmer ans equipment
        $data['orders']['customer_id']      = $customer_id;
        $data['orders']['invoice_customer'] = $customer_id;
        $data['orders']['equipment_id']     = $equipment_id;
        $customer                           = Customer::where('id', '=', $customer_id)->orderby('name', 'asc')->first();
        $data['orders']['email_invoice']    = $customer['email'];
        $data['customers']                  = Customer::where('status', '=', '0')->orderby('name', 'asc')->pluck('name', 'id');
        $data['departments']                = Department::orderby('Name', 'asc')->where('status', '=', '0')->pluck('name', 'id');
        if ($usertype != "Admin" && $usertype != "Administrative") {
            $data['orders']['department_id'] = Auth::User()->department_id;
            $data['users']                   = User::where('department_id', Auth::User()->department_id)->where('activated', '=', 0)->pluck('first_name', 'id');
        }
        $data['department_chiefs'] = User::selectRaw('concat(if(first_name is null, "", concat(first_name, " ")) ,if(last_name is null, "", last_name)) as name, id')->where('activated', '=', 0)->orderBy('first_name', 'asc')->pluck('name', 'id');
        $data['order_status']      = DropdownHelper::where('language', $language)->where('groupcode', '005')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        $data['priority']          = DropdownHelper::where('language', $language)->where('groupcode', '006')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        $data['order_category']    = DropdownHelper::where('language', $language)->whereIn('keycode', ["01", "03", "06", "08"])->where('groupcode', '007')->orderBy('keycode', 'asc')->pluck('label', 'keycode');
        return $data;
    }

    /**
     * [downloadOrderReportByStatus description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public static function downloadOrderReportByStatus($data)
    {
        if ($data['order_id']) {
            $language      = Session::get('language') ? Session::get('language') : 'no';
            $fileData      = array();
            $order_details = Order::select('status', 'order_number', 'is_offer', 'offer_number')->where('id', '=', $data['order_id'])->first();
            if ($order_details->is_offer == 1) {
                $order_details->order_number = $order_details->offer_number;
                $order_pdf                   = Order::constructOfferOrderPDF($data['order_id'], 1);
                $file_name                   = __('main.offer');
            } else {
                $file_name = __('main.order');
                $order_pdf = Order::constructOfferOrderPDF($data['order_id'], 2);
            }
            $fileData['fileName'] = $file_name . '_' . $order_details->order_number . '.pdf';
            $fileData['filePath'] = $order_pdf;
            return $fileData;
        }
    }
    public static function constructOfferOrderPDF($order_id = false, $type = false)
    {
        try {
            if (@$order_id) {
                $data             = Order::editOrder($order_id);
                $shipping_details = Shipping::where('order_id', $order_id)->orderBy('created_at', 'desc')->get();
                if (count($shipping_details->toArray()) == 1) {
                    $data['shipping_details'] = $shipping_details[0];
                }
                $data['type']                = $type;
                $file_array                  = $data['contacts_details']                  = $data['department_details']                  = $data['offer_products']                  = array();
                $data['company_information'] = Company::first();
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
                $data['offerProducts'] = Order::constructOfferProductsData($order_id, $type);
                $sum                   = 0;
                $mva                   = 0;
                foreach ($data['offerProducts'] as $key => $value) {
                    $sum = $value['sum_ex_vat'] + $sum;
                    $mva = $mva + ($value['sum_ex_vat'] * $value['vat'] / 100);
                }
                $total                       = $sum + $mva;
                $data['orders']->sum         = $sum;
                $data['orders']->mva         = $mva;
                $data['orders']->total       = round($total);
                $data['company_information'] = Company::first();
                // $data['standard_offer_text'] = OfferSettings::whereType(1)->select('data')->first();
                $footer            = View::make('order.footer_report', $data);
                $offer_detail_view = View::make('order.offer_report', $data);
                $temp_pdf_name     = GanticHelper::createTempFile("pdf");
                $pdfReport         = PDF::loadHTML($offer_detail_view)->setOption('footer-html', $footer)->save($temp_pdf_name);
                return $temp_pdf_name;
            }
        } catch (\Exception $e) {
            echo $e;
            exit;
        }
    }

    /**
     * [constructOfferProductsData description]
     * @param  boolean $order_id [description]
     * @return [type]            [description]
     */
    public static function constructOfferProductsData($order_id = false, $type = false)
    {
        try {
            if (@$order_id) {
                if ($type == 1) {
                    $offerProducts = OrderMaterial::where('order_id', '=', $order_id)->orderBy('sortorderval', 'asc')->get();
                    if (@$offerProducts) {
                        foreach ($offerProducts as $key => $value) {
                            if ($value->is_text != 1) {
                                $package                 = Product::where('id', '=', $value['product_number'])->first();
                                $value['package']        = $package->is_package;
                                $value['productDetails'] = $package;
                                if ($package->is_package == 1) {
                                    $package_products          = ProductPackage::selectRaw('product.*, product_package.*, product.id as product_id')->where("product_package.package_id", '=', $value['product_number']);
                                    $package_products          = $package_products->leftjoin('product', 'content', '=', 'product.id')->orderBy("product_package.sort_number", "asc")->get();
                                    $value['package_products'] = $package_products;
                                }
                                $value->price = $value->offer_sale_price;
                                $value->qty   = $value->order_quantity;
                            }

                        }
                        return $offerProducts;
                    }
                } else {
                    $offerProducts = OrderMaterial::where('order_id', '=', $order_id)->whereNull('reference_id')->whereNull('text_ref_id')->orderBy('sortorderval', 'asc')->get();
                    if (@$offerProducts) {
                        foreach ($offerProducts as $key => $value) {
                            $value->qty          = $value->order_quantity;
                            $package             = Product::where('id', '=', $value['product_number'])->first();
                            $billing_data_detail = BillingData::where('ordermaterial_id', $value->id)->first();
                            if ($value->is_text != 1) {
                                $value['productDetails'] = $package;
                                $value['package']        = $package->is_package;
                                if ($package->is_package == 1) {
                                    $package_contents = OrderMaterial::where('reference_id', '=', $value->id)->get();
                                    foreach ($package_contents as $pacakge_key => $pacakge_value) {
                                        $pacakge_value->qty            = $pacakge_value->order_quantity;
                                        $pacakge_value->productDetails = Product::where('id', '=', $value['product_number'])->first();
                                    }
                                    $value['package_products'] = $package_contents;
                                }
                            }
                        }
                        return $offerProducts;
                    }
                }
            }
        } catch (\Exception $e) {
            echo $e;
            exit;
        }
    }

    /**
     * [sendOrderMailFromIndex description]
     * @param  boolean $order_id [description]
     * @return [type]            [description]
     */
    public static function sendOrderMailFromIndex($order_id = false)
    {
        if ($order_id) {
            $data          = array();
            $order_details = Order::select('order.*', 'customer.email')->where('order.id', '=', $order_id)->leftjoin('customer', 'order.invoice_customer', '=', 'customer.id')->first();
            if ($order_details) {
                $data['order_status'] = @$order_details->status;
                $data['order_id']     = $order_id;
                $data['email']        = @$order_details->email;
                $data['order_number'] = @$order_details->order_number;
                if ($order_details->is_offer == 1) {
                    $data['order_number'] = @$order_details->offer_number;
                }
                $data['is_offer'] = @$order_details->is_offer;
            }
            Order::sendOrderMail($data, 1);
            return true;
        }
    }

    /**
     * [sendOrderMail description]
     * @param  [type]  $data [description]
     * @param  boolean $type [description]
     * @return [type]        [description]
     */
    public static function sendOrderMail($data, $type = false)
    {
        try {
            if ($data['order_id']) {
                $order_details = Order::select('order_number', 'offer_number')->where('order.id', '=', $data['order_id'])->first();
                $to_email      = array();
                if (@$data['is_offer'] == 1) {
                    $order_details->order_number = $order_details->offer_number;
                    $order_pdf                   = Order::constructOfferOrderPDF($data['order_id'], 1);
                } else {
                    $order_pdf = Order::constructOfferOrderPDF($data['order_id'], 2);
                }
                $is_not_production = 1;
                if (config("app.env") == "production") {
                    $is_not_production = 0;
                }
                $to_email[] = Auth::user()->email;
                if (@$to_email) {
                    $language = Session::get('language') ? Session::get('language') : 'no';
                    app('Illuminate\Contracts\Bus\Dispatcher')->dispatch(new OrderOfferMail($order_pdf, $data['is_offer'], $to_email, $language, @$order_details->order_number, $is_not_production));
                    OrderEmailHistory::storeOrderMailHistory($data['order_id'], implode(",", $to_email), $data['order_status'], Session::get('currentUserID'));
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
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
    public static function getCustomerOrderByProduct($product_id = false)
    {
        if ($product_id) {
            $order_material_data = OrderMaterial::select('*')->selectRaw('order_material.id as ordermaterial_id')->leftjoin('order', 'order_material.order_id', '=', 'order.id')->where('order_material.product_number', "=", $product_id)->where('order_material.approved_product', '=', 0)->where('order_material.quantity', '>', 0)->get();
            return $order_material_data;
        } else {
            return null;
        }
    }

    /**
     * [getCustomerOrderByProductAndWarehosue description]
     * @param  boolean $product_id   [description]
     * @param  boolean $location_id  [description]
     * @param  boolean $warehouse_id [description]
     * @return [type]                [description]
     */
    public static function getCustomerOrderByProductAndWarehosue($product_id = false, $location_id = false, $warehouse_id = false)
    {
        if ($product_id && $location_id && $warehouse_id) {
            $order_material_data = OrderMaterial::select('*')->selectRaw('order_material.id as ordermaterial_id')->leftjoin('order', 'order_material.order_id', '=', 'order.id')->where('order_material.product_number', "=", $product_id)->where('order_material.approved_product', '=', 0)->where('order_material.quantity', '>', 0)->where('order_material.warehouse', '=', $warehouse_id)->where('order_material.location', '=', $location_id)->get();
            return $order_material_data;
        } else {
            return null;
        }
    }

    /**
     * [createCustomerUNIOrder description]
     * @param  [type] $input_data [description]
     * @return [type]             [description]
     */
    public static function createCustomerUNIOrder($input_data)
    {
        $order_data       = Order::with('customer', 'responsibleUser', 'order_department', 'order_department.department')->find($input_data['order_id']);
        $uni_order_result = !$order_data->uni_status ? UniIntegration::createOrderInUNi($order_data) : $order_data->uni_status;
        if ($uni_order_result) {
            Order::whereId(@$input_data['order_id'])->update(['status' => '4']);
            $language  = Session::get('language') ? Session::get('language') : 'no';
            $units     = DropdownHelper::where("groupcode", "=", "010")->where('language', '=', $language)->orderby("keycode", "asc")->pluck("label", "keycode");
            $materials = json_decode($input_data['materials']);
            $sent_id   = OrderMaterial::where('order_id', $input_data['order_id'])->orderBy('sent_id', 'desc')->first();
            $sent_id   = @$sent_id ? $sent_id->sent_id + 1 : 1;
            foreach ($materials as $material) {
                $order_line_result = UniIntegration::sendOrderItemsToUNI($material->id, $units, $uni_order_result);
                if ($order_line_result) {
                    OrderMaterial::whereId($material->id)->update(['uni_status' => 1, 'approved_product' => 1, 'invoiced' => 1, 'sent_id' => $sent_id]);
                } else {
                    return false;
                }
            }
            return true;
        } else {
            return false;
        }

    }
}

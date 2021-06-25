<?php
namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Equipment;
use App\Models\HourLogging;
use App\Models\Order;
use App\Models\OrderDepartment;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Input;
use Session;

class AjaxController extends Controller
{

    /*
     * Check whether the user email exists
     */
    public function validateuseremail()
    {
        $check_email  = DB::table('user')->Where('email', '=', \Request::input('email'))->get();
        $result_email = count($check_email) ? 1 : 0;
        return $result_email;
    }

    public function getContactPersonsAndUsers()
    {
        $customer_id     = @$_POST['customer_id'];
        $customer_detail = Customer::where('id', $customer_id)->first();
        // get contacts
        $result                      = Contact::where('customer_id', '=', $customer_id)->orderby('name', 'asc')->get();
        $results                     = array();
        $contact_options             = "";
        $contact_options_with_mobile = "";
        foreach ($result as $key => $value) {
            $contact_options .= '<option value=' . $value->id . '>' . $value->name . '</option>';
            $contact_options_with_mobile .= '<option value=' . $value->id . '>' . $value->name . ' ' . $value->mobile . '</option>';
        }

        // get equipments
        $equipment_result  = Equipment::select(DB::Raw("concat(COALESCE(internalnbr,''), IF(LENGTH(internalnbr), ' - ', ''),  COALESCE(sn,''), IF(LENGTH(sn), ' - ', ''), COALESCE(description,'') ) AS name,id"))->where('customer_id', '=', $customer_id)->orderby('sn', 'asc')->pluck('name', 'id');
        $equipment_options = "";
        $equipment_options .= '<option selected value="">' . trans('main.selected') . '</option>';
        foreach ($equipment_result as $key => $value) {
            $equipment_options .= '<option value=' . $key . '>' . $value . '</option>';
        }

        //Customer Address Details
        $customer_address_result  = CustomerAddress::where('customer_id', '=', $customer_id)->get();
        $customer_address_options = '<option selected value="">' . trans('main.selected') . '</option>';
        $i                        = 0;
        foreach ($customer_address_result as $key => $value) {
            if ($value['type'] == 3) {
                $customer_address_options .= '<option selected="selected" value=' . $value->id . '>' . $value->address1 . '</option>';
            } else {
                $customer_address_options .= '<option value=' . $value->id . '>' . $value->address1 . '</option>';
            }

        }

        return json_encode(array('contact_person' => $contact_options, 'equipments' => $equipment_options, 'contact_options_with_mobile' => $contact_options_with_mobile, 'customer_address_options' => $customer_address_options, 'customer_detail' => $customer_detail));
    }

    /**
     * [getUsersByDepartment description]
     * @return [type] [description]
     */
    public function getUsersByDepartment()
    {
        $department_id    = @$_POST['department_id'];
        $user_result      = User::with('usertype')->where('department_id', $department_id)->where('activated', '=', 0)->orderby('first_name', 'asc')->get();
        $all_user_options = "";
        foreach ($user_result as $key => $value) {
            $all_user_options .= '<option value="' . $value->id . '">' . $value->first_name . ' ' . $value->last_name . '</option>';
        }
        return json_encode(array("all_users" => $all_user_options));
    }

    // get customer orders
    public function getOrders()
    {
        $customer_id   = $_POST['customer_id'];
        $result        = Order::where('customer_id', '=', $customer_id)->orderby('order_number', 'asc')->pluck('order_number', 'id');
        $results       = array();
        $order_options = "";
        $order_id      = $_POST['order_id'];
        foreach ($result as $key => $value) {

            if ($order_id == $key) {
                $order_options .= '<option selected value=' . $key . '>' . $value . '</option>';
            } else {
                $order_options .= '<option value=' . $key . '>' . $value . '</option>';
            }
        }

        return json_encode(array('orders' => $order_options));
    }

    // update user pagination
    public function updateUserPagination($paginate_size, $user_id)
    {

        if ($user_id && $paginate_size) {
            Session::put('paginate_size', $paginate_size);
            echo "success";
        } else {
            echo "error";
        }
    }

    // check product number and supplier duplicate
    public function checkProductDuplicate()
    {
        try {
            $product_number = $_POST['product_number'];
            $supplier_id    = $_POST['supplier_id'];

            $check_product = DB::table('product')->where('product_number', '=', $product_number)->where('supplier_id', '=', $supplier_id)->get();

            if (count($check_product) > 0) {
                echo json_encode(array("status" => "success", "data" => trans("main.product.product_already_exist")));
            } else {
                echo json_encode(array("status" => "fail", "data" => "Ok"));
            }
        } catch (Exception $e) {
            echo json_encode(array("status" => "error"));
        }
    }

    /**
     * [loadUsers description]
     * @return [type] [description]
     */
    public function loadUsers()
    {
        $id            = $_POST['id'];
        $module        = $_POST['module'];
        $users         = User::getUsersDropDownForGroup(1, false, $module);
        $users_options = "";
        foreach ($users as $key => $value) {
            $users_options .= '<option value=' . $key . '>' . $value . '</option>';
        }
        return json_encode(array('users' => $users_options));
    }

    /**
     * [getUsersForHourLogging description]
     * @return [type] [description]
     */
    public function getUsersForHourLogging($id)
    {
        $hourlogg_result = Hourlogging::where('id', "=", $id)->first();
        $department      = OrderDepartment::getOrderDepartmentIDByOrderId(@$hourlogg_result->order_id);
        $users           = User::select(DB::Raw("concat(first_name ,' ', IFNULL(last_name,'')) AS full_name, id"))->where('activated', '=', 0)->orderby('first_name', 'asc')->whereIN('department_id', $department)->orWhere('id', '=', $hourlogg_result->user_id)->pluck('full_name', 'id');
        $users_options   = "";
        foreach ($users as $key => $value) {
            $users_options .= '<option value=' . $key . '>' . $value . '</option>';
        }
        return json_encode(array('users' => $users_options));
    }
}

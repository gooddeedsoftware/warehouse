<?php
namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Session;

class Contact extends Model
{
    protected $table     = 'contact_person';
    public $incrementing = false;
    public $timestamps   = true;
    use SoftDeletes;
    protected $fillable = array('id', 'customer_id', 'name', 'email', 'mobile', 'phone', 'deleted_at', 'added_by', 'updated_by', 'title');

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    public static function getContactDetails($conditions = false, $orderby = 'contact_person.name', $order = 'asc', $id = false)
    {
        if ($orderby == 'customerName') {
            $orderby = 'customer.name';
        }

        $contact = Contact::join('customer', 'customer.id', '=', 'contact_person.customer_id', 'left');
        $contact->select('customer.name as customerName', 'contact_person.*');
        $contact->orderBy($orderby, $order);
        if ($id) {
            $contact->where('contact_person.customer_id', '=', $id);
        }
        if (isset($conditions['search']) && $conditions['search'] != '') {
            $search = $conditions['search'];
            $contact->where(function ($query) use ($search) {
                $query->orwhere('customer_id', 'LIKE', '%' . $search . '%');
                $query->orwhere('contact_person.name', 'LIKE', '%' . $search . '%');
                $query->orwhere('mobile', 'LIKE', '%' . $search . '%');
                $query->orwhere('contact_person.phone', 'LIKE', '%' . $search . '%');
                $query->orwhere('contact_person.email', 'LIKE', '%' . $search . '%');

                $query->orwhereHas('customer', function ($query) use ($search) {
                    $query->where('customer.name', 'LIKE', '%' . $search . '%');
                });
            });
        }
        $paginate_size = Session::get('paginate_size') ? Session::get('paginate_size') : 10;
        return $contact->paginate($paginate_size);
    }
    // get contact person details
    public static function getContactPerson($id)
    {
        $contact_person_details = Contact::where('id', "=", $id)->get();
        return $contact_person_details;
    }
    // get contact id by customer id
    public static function getContactDetailByCustomerId($customer_id)
    {
        $contacts = Contact::select(DB::Raw("concat(name, ' ', IFNULL(mobile,'')) AS name,id"))->where('customer_id', '=', $customer_id)->pluck('name', 'id');
        return $contacts;
    }
    // get contact id by customer id
    public static function getContactIdsByCustomerId($customer_id)
    {
        $contacts         = Contact::select(DB::Raw("concat(name, ' ', IFNULL(mobile,'')) AS name,id"))->where('customer_id', '=', $customer_id)->pluck('name', 'id');
        $contact_id_array = array();
        foreach ($contacts as $contact_id => $contact_name) {
            $contact_id_array[] = $contact_id;
        }
        return $contact_id_array;
    }
    // delete contact using customer id
    public static function deleteContactUsingCustomerID($customer_id)
    {
        if ($customer_id) {
            try {
                DB::update('update `contact_person` set deleted_at="' . date("Y-m-d h:i:s") . '" where customer_id="' . $customer_id . '"');
            } catch (Exception $e) {
            }
        }
    }
}

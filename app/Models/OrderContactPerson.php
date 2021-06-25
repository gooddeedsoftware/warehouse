<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;

class OrderContactPerson extends Model
{
    protected $table    = 'order_contact_person';
    public $timestamps  = false;
    protected $fillable = array('order_id', 'contact_person_id');

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }
    public function contact()
    {
        return $this->belongsTo('App\Models\Contact', 'contact_person_id', 'id');
    }

    public static function getContactPersonDetails($conditions = false, $orderby = 'contact_person_id', $order = 'asc')
    {
        $order_contact_person = OrderContactPerson::orderBy($orderby, $order);
        if (isset($conditions['search']) && $conditions['search'] != '') {
            $search = $conditions['search'];
            $customer->where(function ($query) use ($search) {

            });
        }
        $order_contact_person->with('order', 'contact');
        $paginate_size = Session::get('paginate_size') ? Session::get('paginate_size') : 10;
        return $order_contact_person->paginate($paginate_size);
    }

    // get order contact persons
    public static function contact_person_name($id)
    {
        $ordercontact_persons = OrderContactPerson::with('contact')->where('order_id', $id)->get();
        return $ordercontact_persons;
    }

    // get contact persons id by order id
    public static function getContactPersonIdByOrderId($order_id)
    {
        $contact_persons_ids = array();
        $contact_persons     = OrderContactPerson::where('order_id', $order_id)->get()->toArray();
        foreach ($contact_persons as $person) {
            $contact_persons_ids[] = $person['contact_person_id'];
        }
        return $contact_persons_ids;
    }
}

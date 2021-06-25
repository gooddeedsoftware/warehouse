<?php
namespace App\Http\Controllers;

use App\Helpers\GanticHelper;
use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use Illuminate\Http\Request;
use Redirect;
use Session;

class ContactPersonController extends Controller
{
    protected $route               = 'main.contact.index';
    protected $customer_view_route = 'main.customer.edit';
    protected $success             = 'success';
    protected $error               = 'error';
    protected $notfound            = 'main.notfound';
    protected $createmsg           = 'main.contact_createsuccess';
    protected $updatemsg           = 'main.contact_updatesuccess';
    protected $deletemsg           = 'main.contact_deletesuccess';

    /**
     * Loads a contact view.
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function loadContactView()
    {
        $data = @\Request::input();
        if ($data['id']) {
            $data['contact'] = Contact::where('id', $data['id'])->first();
        }
        return view('customer/contact/quickCreate', $data);
    }

    /**
     * Creates an or update contact.
     *
     * @param      \Illuminate\Http\Request  $request  The request
     *
     * @return     <type>                    ( description_of_the_return_value )
     */
    public function createOrUpdateContact(ContactRequest $request)
    {
        $input             = $request->all();
        $input['added_by'] = Session::get('currentUserID');
        if (@$input['id']) {
            $contact = Contact::find($input['id']);
            $contact->fill($input);
            $contact->save();
            return Redirect::route($this->customer_view_route, $input['customer_id'])->with($this->success, __($this->updatemsg));
        } else {
            $input['id'] = GanticHelper::gen_uuid();
            $contact     = Contact::create($input);
            return Redirect::route($this->customer_view_route, $input['customer_id'])->with($this->success, __($this->createmsg));
        }

    }

    /**
     * Deletes the given identifier.
     *
     * @param      <type>  $id     The identifier
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function delete($id)
    {
        $contact = Contact::find($id);
        if (!$contact) {
            return Redirect::back()->with($this->error, __($this->notfound));
        }
        $contact->delete();
        return Redirect::back()->with($this->success, __($this->deletemsg));
    }

    public function contactInlineStore(ContactRequest $request, $id = null)
    {
        $temp              = 0;
        $input             = $request->all();
        $input['id']       = GanticHelper::gen_uuid();
        $input['added_by'] = Session::get('currentUserID');
        Contact::create($input);
        $contact_result = Contact::where('id', '=', $input['id'])->first();
        $result         = array('result' => 'success', "id" => $input['id']);
        echo json_encode($result);
    }
}

<?php

namespace App\Http\Requests;
use App\Http\Requests\Request;

class ContactRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        $rules = [];
        switch ($this->method()) {
            case 'POST':
                $customer_id = Request::get('customer_id');
                if (!Request::get('id')) {
                    $rules['name'] = 'required|unique:contact_person,name,NULL,id,deleted_at,NULL,customer_id,' . $customer_id;
                }
                break;
            default:break;
        }
        return $rules;
    }
}

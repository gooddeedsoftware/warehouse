<?php

/*
------------------------------------------------------------------------------------------------
Created By   : Aravinth.A
Email:       : aravinth@avalia.no
Created Date : 20.06.2017
Purpose      : Customer Address Requests
------------------------------------------------------------------------------------------------
 */

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CustomerAddressRequest extends Request
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
        $rules['country'] = 'required';

        switch ($this->method()) {
            case 'GET':
            case 'DELETE':
                $rules = [];
                break;

            default:break;
        }
        return $rules;
    }
}

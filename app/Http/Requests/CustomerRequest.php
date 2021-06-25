<?php
namespace App\Http\Requests;

use App\Http\Requests\Request;

class CustomerRequest extends Request
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
                $rules['name'] = 'required|max:255|unique:customer,name,NULL,id,deleted_at,NULL';
                break;
            case 'PUT':
            case 'PATCH':
                $rules['name'] = 'required|unique:customer,name,' . Request::segment(2) . ',id,deleted_at,NULL';
                break;
            default:
                break;
        }
        return $rules;
    }

    public function messages()
    {
        return [];
    }
}

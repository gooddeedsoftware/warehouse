<?php
namespace App\Http\Requests;

use App\Http\Requests\Request;

class UserRequest extends Request
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
        $rules['usertype_id'] = 'required';
        $rules['first_name']  = 'required';

        switch ($this->method()) {
            case 'GET':
            case 'DELETE':
                {
                    return [];
                }
                break;
            case 'POST':
                {
                    $rules['email'] = 'required|email|unique:user,email';

                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules['email'] = 'required|email|unique:user,email,' . Request::segment(2) . ',id';
                }
                break;
            default:break;
        }
        return $rules;
    }
}

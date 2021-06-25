<?php
namespace App\Http\Requests;

use App\Http\Requests\Request;

class UserTypeRequest extends Request
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
        switch ($this->method()) {
            case 'GET':
            case 'DELETE':
                {
                    return [];
                }
                break;
            case 'POST':
                {
                    return [
                        'type' => 'required|unique:user_type,type|max:50',
                    ];
                }
            case 'PUT':
            case 'PATCH':
                {
                    return [
                        'type' => 'required|unique:user_type,type,' . Request::segment(2) . ',id',
                    ];
                }
                break;
            default:break;
        }
    }
}

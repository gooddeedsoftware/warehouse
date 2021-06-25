<?php
namespace App\Http\Requests;

use App\Http\Requests\Request;

class LocKRequest extends Request {
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
                        // 'month' => 'required|unique:offer_permission,group',
                    ];
                }
            case 'PUT':
            case 'PATCH':
                {
                }
                break;
            default:break;
        }
    }
}

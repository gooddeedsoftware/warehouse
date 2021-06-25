<?php
namespace App\Http\Requests;

use App\Http\Requests\Request;

class CompanyRequest extends Request
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
            case 'POST':
                break;
            case 'PUT':
                break;
            case 'PATCH':
            case 'DELETE':
                {
                    return [];
                }
                break;

            default:break;
        }
        return [];
    }
}

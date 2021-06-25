<?php
namespace App\Http\Requests;

use App\Http\Requests\Request;

class DietRequest extends Request
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
        $rules['name'] = 'required';
		 switch ($this->method()) {
            case 'GET':
            case 'DELETE':
                {
                    return [];
                }
                break;
            case 'POST':
                {
                    $rules['name'] = 'required|unique:diet,name|max:25';	
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules['name'] = 'required|unique:diet,name,' . Request::segment(2) . ',id';
                }
                break;
            default:break;
        }
        return $rules;
    }
}

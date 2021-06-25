<?php
namespace App\Http\Requests;

use App\Http\Requests\Request;

class ProductGroupRequest extends Request
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
        $rules['number'] = 'required';
        $rules['name']   = 'required';
        switch ($this->method()) {
            case 'POST':
                {
                    $rules['number'] = 'required|unique:product_group,number';
                    $rules['name']   = 'required|unique:product_group,name';
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules['number'] = 'required|unique:product_group,number,' . Request::segment(2) . ',id';
                    $rules['name']   = 'required|unique:product_group,name,' . Request::segment(2) . ',id';
                }
                break;
            default:
                break;
        }
        return $rules;
    }
}

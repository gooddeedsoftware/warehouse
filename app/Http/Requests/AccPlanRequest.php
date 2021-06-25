<?php
namespace App\Http\Requests;

use App\Http\Requests\Request;

class AccPlanRequest extends Request
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
        $rules['AccountNo'] = 'required';
        switch ($this->method()) {
            case 'POST':
                {
                    $rules['AccountNo'] = 'required|unique:acc_plan,AccountNo|max:25';
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules['AccountNo'] = 'required|unique:acc_plan,AccountNo,' . Request::segment(2) . ',id';
                }
                break;
            default:
                break;
        }
        return $rules;
    }
}

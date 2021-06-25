<?php
namespace App\Http\Requests;

use App\Http\Requests\Request;

class DepartmentRequest extends Request
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
            case 'POST':
                {
                    $rules['Name'] = 'required|unique:department,Name, NULL,id,deleted_at,NULL';
                    $rules['Nbr']  = 'required|unique:department,Nbr,NULL,id,deleted_at,NULL';
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules['Name'] = 'required|unique:department,Name,' . Request::segment(2) . ',id,deleted_at,NULL';
                    $rules['Nbr']  = 'required|unique:department,Nbr,' . Request::segment(2) . ',id,deleted_at,NULL';
                }
                break;
            default:
                break;
        }
        return $rules;
    }
}

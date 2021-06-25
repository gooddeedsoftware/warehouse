<?php
namespace App\Http\Requests;

use App\Http\Requests\Request;

class WorkTypeRequest extends Request
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
                        'name' => 'required|unique:worktype,name|max:250',
                    ];
                }
            case 'PUT':
            case 'PATCH':
                {
                    return [
                        'name' => 'required|unique:worktype,name,' . Request::segment(2) . ',id',
                    ];
                }
                break;
            default:break;
        }
    }
}

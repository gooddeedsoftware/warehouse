<?php
namespace App\Http\Requests;

use App\Http\Requests\Request;

class GroupRequest extends Request
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
                        'group' => 'required|unique:group,group|max:50',
                    ];
                }
            case 'PUT':
            case 'PATCH':
                {
                    return [
                        'group' => 'required|unique:group,group,' . Request::segment(2) . ',id',
                    ];
                }
                break;
            default:break;
        }
    }
}

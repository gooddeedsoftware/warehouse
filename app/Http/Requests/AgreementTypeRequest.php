<?php
namespace App\Http\Requests;

use App\Http\Requests\Request;

class AgreementTypeRequest extends Request
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
                        'agreement_value_en' => 'required|unique:agreement_type,agreement_value_en|max:50',
                        'agreement_value_no' => 'required|unique:agreement_type,agreement_value_no|max:50',
                    ];
                }
            case 'PUT':
            case 'PATCH':
                {
                    return [
                        'agreement_value_en' => 'required|unique:agreement_type,agreement_value_en,' . Request::segment(2) . ',id',
                        'agreement_value_no' => 'required|unique:agreement_type,agreement_value_no,' . Request::segment(2) . ',id',
                    ];
                }
                break;
            default:break;
        }
    }
}

<?php
namespace App\Http\Requests;

use App\Http\Requests\Request;

class OrderRequest extends Request
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
		$rules['project'] = 'required';
		$rules['signature_image'] = 'required_if:update,false|mimes:jpeg,jpg,bmp,png|max:200';
        $rules = [
            'order_start_date'        => 'date_format:d.m.Y',
            'order_end_date'          => 'date_format:d.m.Y',
        ];

        switch ($this->method()) {
            case 'GET':
            case 'DELETE':
                {
                    return [];
                }
                break;
            case 'POST':
                {
                    $rules['customer_id'] = 'required';
                    $rules['invoice_customer'] = 'required';
                }
            case 'PUT':
            case 'PATCH':
                {
                    if (Request::segment(4)) {
                        $id = Request::segment(4);
                    } else {
                        $id = Request::segment(2);
                    }
                }
                break;
            default:break;
        }

        return $rules;

    }
}

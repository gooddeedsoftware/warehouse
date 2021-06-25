<?php
namespace App\Http\Requests;

use App\Http\Requests\Request;

class AgreementRequest extends Request
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

        $rules['customer_id'] = 'required';
        $rules['agreementtype_id'] = 'required';
        $rules['invoice_customer_id'] = 'required';
		$rules['project'] = 'required';
        $rules = [
            'start_date'        => 'date_format:d.m.Y',
            'end_date'          => 'date_format:d.m.Y',
        ];

        return $rules;

    }
}

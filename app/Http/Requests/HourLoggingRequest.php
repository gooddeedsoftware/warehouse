<?php
namespace App\Http\Requests;

use App\Http\Requests\Request;

class HourLoggingRequest extends Request
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
        $rules['user_id'] = 'required';
        $rules['other'] = 'required';
        $rules['hours'] = 'required';
        $rules = [
            'start_time'        => 'date_format:H:i',
            'end_time'          => 'date_format:H:i|after:start_time',
        ];
        $rules = [
            'date'        => 'date_format:d.m.Y'
        ];
        return $rules;
    }
}

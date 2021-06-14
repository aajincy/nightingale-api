<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UnitsRequest extends FormRequest
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
        return [
            "sortby"    => ['nullable','string',Rule::in(['name', 'rooms','nurse_ratio','aide_ratio','patient_nurse_day_ratio','patient_aides_day_ratio','patient_nurse_night_ratio','patient_aides_night_ratio'])],
            "sortorder" => ['nullable','string',Rule::in(['asc','desc'])],
            "count"     => ['nullable','integer']
        ];
    }
}

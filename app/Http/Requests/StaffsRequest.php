<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StaffsRequest extends FormRequest
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
            "sortby"    => ['nullable','string',Rule::in(['first_name', 'email','phone','title','roles'])],
            "sortorder"    => ['nullable','string',Rule::in(['asc','desc'])],
            "count"   => ['nullable','integer']
        ];
    }
}

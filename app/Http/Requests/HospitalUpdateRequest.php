<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HospitalUpdateRequest extends FormRequest
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
            'name'    => 'required|string',
            'address' => 'required|string',
            'city'    => 'required|string',
            'state'   => 'required|string',
            'zipcode' => 'required|string',
            'admins'  => 'required|array',
            'brand_colors'  => 'required|string'
            
        ];
    }
}

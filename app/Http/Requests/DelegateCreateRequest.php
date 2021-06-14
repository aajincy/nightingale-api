<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DelegateCreateRequest extends FormRequest
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
            'room_id'     => "required|string",
            'unit_id'     => "required|string",
            'patient_id'  => "required|string",
            'staff_id'    => "required|string",
            'shift'       => "required|string",
            'assigned_date'  => "required|date",
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatientCreateRequest extends FormRequest
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
            'first_name' => "required|string",
            'middle_name' => "nullable|string",
            'last_name' => "required|string",
            'email' => "required|string",
            'phone' => "required|string",
            'date_of_birth' => "required|date",
            'weight' => "required|string",
            'diagnosis' => "nullable|string",
            'admit_date' => "required|date",
            'unit_id' => "required|string",
            'unit_name' => "nullable|string",
            'staff_id'=>"required|string",
            'room_number' => "required|string",
            'notes' => "nullable|string",
            'tags' => "required|string",
            'discharged_at' => "nullable|date",

        ];
    }
}

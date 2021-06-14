<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StaffCreateRequest extends FormRequest
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
            'email' => "required|string|unique:users",
            'phone' => "required|string|unique:users",
            'password' => "nullable|string",
            'type' => "required|string",
            'roles' => "required|string",
            'profile_picture' => "nullable|string",
            'title' => "required|string",
            'credentials'=>"nullable|string",
            'certifications' => "required|string",
            'sms_notifications' => "required|boolean",
            'active' => "required|boolean",
            'experience' => "nullable|string",
            'start_date' => "nullable|date",            
            'units' => "nullable|array",
        ];
    }
}

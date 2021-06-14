<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
        $user = $this->route()->parameters['admin'];
        return [
            "first_name"        => ['required','string'],
            "middle_name"       => ['nullable','string'],
            "last_name"         => ['required','string'],
            "email"             => ['required','email','unique:users,email,'.$user->id],
            "phone"             => ['required','string','unique:users,phone,'.$user->id],
            "password"          => ['nullable','string'],
            "type"              => ['required','string'], // hospital_staff , administrator, moderator
            "roles"             => ['required_if:type,hospital_staff','string'], // admin, charge_nurse , none 
            "profile_picture"   => ['nullable'],
            "title"             => ['required_if:type,hospital_staff','string'],
            "hospital_id"       => ['required_if:type,hospital_staff','string', 'exists:hospitals,id'],
            "certifications"    => ['required_if:type,hospital_staff','string'],
            "sms_notifications" => ['nullable','string'],
            "experience"        => ['required_if:type,hospital_staff','string'],
            "start_date"        => ['required_if:type,hospital_staff','date'],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class LoginUpdateRequest extends FormRequest
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
        $user = auth()->user();
        return [
            "first_name"       => ['nullable','string'],
            "last_name"        => ['nullable','string'],
            "email"            => ['required','email','unique:users,email,'.$user->id],
            'current_password' => ['required_with:password','string'],
            'password'         => ['nullable','string','min:6','confirmed'],
        ];
    }
    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $user = auth()->user();
        $validator->after(function ($validator) use ($user) {
            if ($this->current_password){
                $hashcheck = Hash::check($this->current_password, $user->password);
                if(!$hashcheck){
                    $validator->errors()->add('current_password', 'Current password is incorrect.');
                }
                if(($this->current_password && $this->password) && ($this->current_password == $this->password)){
                    $validator->errors()->add('password', 'Current password and New password should not be same.');
                }
            }
        });
    }    
}

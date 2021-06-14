<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CsrRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = auth()->user();
        if($user->type == 'administrator'){
            return true;
         }
         else{
            return false;
         }  
       // return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "sortby"    => ['nullable','string',Rule::in(['status', 'created_at'])],
            "sortorder"    => ['nullable','string',Rule::in(['asc','desc'])],
            "count"   => ['nullable','integer']
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CsrCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {

        $user = auth()->user();
        if($user->staff()->exists()){
            $csr =  $user->staff->csrequest;
         }
         else{
             return response()->json([],405);
         }  
        //return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reason_for_contact' => "required|string",
            'message' => "required|string"
        ];
    }
}

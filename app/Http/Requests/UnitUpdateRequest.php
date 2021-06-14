<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnitUpdateRequest extends FormRequest
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
            'name' => 'required|string',
            'rooms' => 'required|int',
            'brand_color' => 'required|string',
            'nurse_ratio_patient_day' => 'required|int',
            'nurse_ratio_nurse_day' => 'required|int',
            'aides_ratio_patient_day' => 'required|int',
            'aides_ratio_aide_day' => 'required|int',
            'nurse_ratio_patient_night' => 'required|int',
            'nurse_ratio_nurse_night' => 'required|int',
            'aides_ratio_patient_night' => 'required|int',
            'aides_ratio_aide_night' => 'required|int',
            'staffs'                => 'nullable|array',
            'aggregated'            => 'required|boolean',
            'designation'           => 'required|string',
            'workload_dimensions'   => 'nullable|array',
            'workload_dimensions.*.shape' => 'required|string',
            'workload_dimensions.*.dimension' => 'required|string',
            'workload_dimensions.*.min' => 'required|int',
            'workload_dimensions.*.max' => 'required|int',
            'workload_dimensions.*.show' => 'required|boolean',
        ];
    }
}

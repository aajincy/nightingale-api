<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NotificationChannelRequest extends FormRequest
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
            'notification_id'   => "required|exists:system_notifications,id",
            'type'              => "required|string|".Rule::in(['sms', 'email','push_notification','in_app_notification']),
            'status'            => "required|boolean"
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffNotification extends Model
{
    protected $fillable = [
        "staff_id",
        "system_notification_id",
        "sms",
        "email",
        "push_notification",
        "in_app_notification",            
    ];
    protected $casts    = [
        'sms' => 'boolean',
        'email' => 'boolean',
        'push_notification' => 'boolean',
        'in_app_notification' => 'boolean',
    ];
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
    public function notification()
    {
        return $this->belongsTo(SystemNotification::class);
    }
}

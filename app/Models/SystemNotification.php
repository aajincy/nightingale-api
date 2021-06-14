<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemNotification extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        "title",
        "notification_triat",
        "sms",
        "email",
        "push_notification",
        "in_app_notification"
    ];
    protected $casts    = [
        'sms'=>'boolean',
        'email'=>'boolean',
        'push_notification'=>'boolean',
        'in_app_notification'=>'boolean',
    ];
}

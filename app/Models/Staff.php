<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Staff extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ["image","type","roles","title","credentials","sms_notifications","certifications","start_date","suspended_at","suspended_till","experience",'user_id','active',"first_name","middle_name","last_name","email","phone"];
    protected $casts    = ['start_date'=>'date','suspended_at'=>'date','suspended_till'=>'date','sms_notifications'=>'boolean','active'=>'boolean'];
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class,'staff_id');
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class,'unit_staff');
    }

    public function cs_requests()
    {
        return $this->hasMany(CustomerSupportRequest::class);
    }
    public function delegates()
    {
        return $this->belongsToMany(Delegate::class);
    }
    public function notifications()
    {
        return $this->hasMany(StaffNotification::class);
    }
    public function isSuspended()
    {
        if($this->suspended_till)
        return $this->suspended_till->gt(now());
        else
        return false;
    }
}

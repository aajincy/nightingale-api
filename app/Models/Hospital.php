<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hospital extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['name','address','city','state','zipcode','brand_colors','active','default_status'];
    protected $casts    = ['active'=>'boolean','default_status'=>'boolean'];
    public function admins()
    {
        return $this->belongsToMany(User::class,'hospital_admin', 'hospital_id', 'user_id');
    }
    public function units()
    {
        return $this->hasMany(Unit::class);
    }
    public function room()
    {
        return $this->hasMany(Room::class);
    }
    public function cs_requests()
    {
        return $this->hasMany(CustomerSupportRequest::class);
    }
    public function staff()
    {
        return $this->hasMany(Staff::class);
    }
    public function patient()
    {
        return $this->hasMany(Patient::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ["first_name","middle_name","last_name","email","phone","date_of_birth","weight","diagnosis","admit_date","unit_id","unit_name","staff_id","room_number","notes","tags","discharged_at"];
    protected $casts    = ['admit_date'=>'datetime','discharged_at'=>'datetime'];
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);

    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function delegates()
    {
        return $this->hasMany(Delegate::class);
    }

    public function staff(){
        return $this->belongsTo(Staff::class,'staff_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;


class Delegate extends Model
{
    use HasFactory;

    protected $fillable = ['room_id','unit_id','patient_id','staff_id','shift','assigned_date'];
    protected $casts    = ['assigned_date'=>'date'];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }



    // code by sethu
    public function staffWorkload():HasOne
    {
        return $this->hasOne(PatientWorkLoadDimension::class,'staff_id','staff_id');
    }

}

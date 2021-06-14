<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'name',
        'rooms',
        'brand_color',
        'nurse_ratio_patient_day',
        'nurse_ratio_nurse_day',
        'aides_ratio_patient_day',
        'aides_ratio_aide_day',
        'nurse_ratio_patient_night',
        'nurse_ratio_nurse_night',
        'aides_ratio_patient_night',
        'aides_ratio_aide_night',
        'designation',
        'aggregated',
        'workload_dimensions'
    ];
    protected $casts = ['workload_dimensions'=>'json',"aggregated"=>"boolean"];
    public function hospital()
    {
        return $this->belongsTo('App\Models\Hospital');
    }
    public function staffs()
    {
        return $this->belongsToMany(Staff::class,'unit_staff');
    }
    public function room()
    {
        return $this->hasMany(Room::class);
    }
    public function patient()
    {
        return $this->hasMany(Patient::class);
    }

    public function floormap()
    {
        return $this->hasOne(FloorMap::class);
    }

}

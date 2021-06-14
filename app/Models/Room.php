<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Room extends Model
{
    use HasFactory;
    protected $fillable = ['room_number', 'hospital_id', 'unit_id','status'];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function delegates()
    {
        return $this->hasMany(Delegate::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class,'room_number','room_number');
    }

}

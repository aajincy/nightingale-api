<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerSupportRequest extends Model
{
    use HasFactory,SoftDeletes;
    
    protected $fillable = ["staff_id","hospital_id","reason_for_contact","message","status","resolved_by"];
 
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
    
}

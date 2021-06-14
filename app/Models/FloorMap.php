<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class FloorMap extends Model
{

    public function floor_rooms(){
        return $this->hasMany(Room::class,'unit_id','unit_id');
    }
}

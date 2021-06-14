<?php


namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Models\FloorMap;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FloorController extends Controller
{

    public function save(Request $request, $hospital_id){
        try {
            $rule = ['unit_id'=>'required','map'=>'required','rooms'=>'required'];
            $validate = $this->validate($request,$rule);
            $floor = FloorMap::where(['hospital_id'=>$hospital_id,'unit_id'=>$validate['unit_id']])->first();
            $new = false;
            if(!$floor) {
                $floor = new FloorMap();
                $new = true;
            }
            DB::beginTransaction();
            $floor_rooms = $validate['rooms'];
            $floor->hospital_id = $hospital_id;
            $floor->unit_id = $validate['unit_id'];
            $floor->map = json_encode($validate['map']);
            $floor->rooms = json_encode($floor_rooms);
            $floor->save();
            $rooms_name = [];
            if($new){
                foreach ($floor_rooms as $item){
                    $room = new Room();
                    $room->room_number = $item;
                    $room->hospital_id = $hospital_id;
                    $room->unit_id = $validate['unit_id'];
                    $room->status = 'available';
                    $room->save();
                }
            }
            else{
                foreach ($floor_rooms as $item){
                    if(empty($item['id']))
                        $room = new Room();
                    else
                        $room = Room::find($item['id']);
                    $room->room_number = $item['room_no'];
                    $room->hospital_id = $hospital_id;
                    $room->unit_id = $validate['unit_id'];
                    $room->status = 'available';
                    $room->save();
                    array_push($rooms_name,$item['room_no']);
                }
                $floor->rooms = json_encode($rooms_name);
                $floor->save();
            }



            DB::commit();
            return response()->json(['status'=>true,'code' => 'SUCCESS','message' => "Floor Map saved",'data'=>[]]);

        }
        catch (ValidationException $e){
            return response()->json(['status'=>false,'code' => 'FAILED','message' => $e->validator->errors()->first(),'data'=>[]],422);
        }
        catch (\Exception $e){
            DB::rollBack();
            return response()->json(['status'=>false,'code' => 'FAILED','message' => $e->getMessage(),'data'=>[]],500);
        }
    }

    public function load(Request $request, $hospital_id){
        try {
            $rule = ['unit_id'=>'required'];
            $validate = $this->validate($request,$rule);
            $floor = FloorMap::where(['hospital_id'=>$hospital_id,'unit_id'=>$validate['unit_id']])->with('floor_rooms:id,room_number,unit_id')->first();
            $map = $floor->map ?? '[]';
            $rooms = $floor->rooms ?? '[]';
            $map = json_decode($map);
            $rooms = json_decode($rooms);
            $floor_rooms = $floor->floor_rooms;
            return response()->json(['status'=>true,'code' => 'SUCCESS','message' => "Floor Map Loaed",'data'=>['floor_map'=>['map'=>$map,'rooms'=>$rooms,'floor_rooms'=>$floor_rooms]]]);

        }
        catch (ValidationException $e){
            return response()->json(['status'=>false,'code' => 'FAILED','message' => $e->validator->errors()->first(),'data'=>[]],422);
        }
        catch (\Exception $e){
            return response()->json(['status'=>false,'code' => 'FAILED','message' => $e->getMessage(),'data'=>[]],500);
        }
    }
}

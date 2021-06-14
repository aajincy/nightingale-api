<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\StaffDeactivated;
use App\Models\FloorMap;
use App\Models\Patient;
use App\Models\PatientTransferLogs;
use App\Models\Room;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class RoomController extends Controller
{

   public function rooms(Request $request,$hospital_id,$unit_id){
       $rooms = Room::where(['hospital_id'=>$hospital_id,'unit_id'=>$unit_id])->get();
       return \response()->json(['status'=>true,'code' => 'SUCCESS','message' => '','data'=>$rooms]);
   }

   public function discharge(Request $request,$hospital_id): \Illuminate\Http\JsonResponse
   {

       try {

           $validate = $request->validate(['patient_id'=>'required']);
           $update = Patient::find($validate['patient_id'])->update(['discharged_at',Carbon::now()->toDateTimeString()]);
           if($update)
               return \response()->json(['status'=>true,'code' => 'SUCCESS','message' => 'Discharged Patient','data'=>[]]);
           else
               return \response()->json(['status'=>false,'code' => 'FAILED','message' => 'Oops something went wrong','data'=>[],500]);
       }
       catch (ValidationException $e){
           return \response()->json(['status' => false, 'code' => 'FAILED', 'message' => $e->validator->errors()->first(), 'data' => []],422);
       }
       catch (\Exception $e){
           DB::rollBack();
           return \response()->json(['status' => false, 'code' => 'FAILED', 'message' => $e->getMessage(), 'data' => []],500);
       }
   }

   public function availableRooms(Request $request,$hospital_id): \Illuminate\Http\JsonResponse
   {
       try {
           $validate = $request->validate(['unit_id' => 'required']);
           $allocationRooms = Patient::where(['discharged_at' => null, 'unit_id'=>$validate['unit_id'],'hospital_id' => $hospital_id])->get('room_number')->toArray();
           //$rooms = Room::where(['hospital_id' => $hospital_id,'unit_id'=>$validate['unit_id']])->whereNotIn('room_number', $allocationRooms)->get(['id', 'room_number']);
           $rooms = Room::where(['hospital_id' => $hospital_id,'unit_id'=>$validate['unit_id']])->whereNotIn('room_number', $allocationRooms)->get('id', 'room_number');
           return \response()->json(['status' => true, 'code' => 'SUCCESS', 'message' => '', 'data' => ['rooms' => $rooms]]);
       }
       catch (ValidationException $e){
           return \response()->json(['status' => false, 'code' => 'FAILED', 'message' => $e->validator->errors()->first(), 'data' => []]);
       }
       catch (\Exception $e){
           DB::rollBack();
           return \response()->json(['status' => false, 'code' => 'FAILED', 'message' => $e->getMessage(), 'data' => []]);
       }


   }

    public function transfer(Request $request,$hospital_id): \Illuminate\Http\JsonResponse
    {
        try {
            $validate = $request->validate(['patient_id'=>'required','room_no'=>'required','unit_id'=>'required']);
            if ($this->checkRoomAvailable($hospital_id,$validate['unit_id'], $validate['room_no'])) {
                DB::beginTransaction();
                $patient = Patient::find($validate['patient_id']);
                $log = new PatientTransferLogs();
                $log->patient_id = $patient->id;
                $log->from_room_number = $patient->room_number;
                $log->to_room_number = $validate['room_no'];
                $log->unit_id = $validate['unit_id'];
                $log->transfer_on = Carbon::now()->toDateTimeString();
                $log->save();
                $patient->room_number = $validate['room_no'];
                $patient->unit_id = $validate['unit_id'];
                $patient->save();
                DB::commit();
                return \response()->json(['status' => true, 'code' => 'SUCCESS', 'message' => '', 'data' => []]);
            }

            return \response()->json(['status' => false, 'code' => 'FAILED', 'message' => 'Room is not available', 'data' => []]);
        }
        catch (ValidationException $e){
            return \response()->json(['status' => false, 'code' => 'FAILED', 'message' => $e->validator->errors()->first(), 'data' => []]);
        }
        catch (\Exception $e){
            DB::rollBack();
            return \response()->json(['status' => false, 'code' => 'FAILED', 'message' => $e->getMessage(), 'data' => []]);
        }
    }

    private function checkRoomAvailable($hospital_id,$unit_id,$room){
        $room = Patient::where(['discharged_at'=>null,'hospital_id'=>$hospital_id,'room_number'=>$room]);
        return $room ? false : true;
    }

}

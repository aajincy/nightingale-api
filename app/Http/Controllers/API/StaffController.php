<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\StaffDeactivated;
use App\Models\Delegate;
use App\Models\FloorMap;
use App\Models\Patient;
use App\Models\Room;
use App\Models\Staff;
use App\Models\StaffNotificationPreference;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class StaffController extends Controller
{
    public function deactivate(Request $request,$hospital_id)
    {
        try {
            $validate = $request->validate(['staff_id'=>'required']);
            $staff = Staff::find($validate['staff_id']);
            if($staff){
                $staff->active = 0;
                //Mail::to($staff->email)->queue(new StaffDeactivated());
                $staff->save();
                \response()->json(['status'=> true,'code' => 'SUCCESS','message' => 'Staff Deactivate','data'=>[]],200);
            }
            else
                \response()->json(['status'=> false,'code' => 'STAFF_DEACTIVATE_FAILED','message' => '','data'=>[]],400);
        }
        catch (ValidationException $e){
            return \response()->json(['status' => false, 'code' => 'FAILED', 'message' => $e->validator->errors()->first(), 'data' => []],422);
        }
        catch (\Exception $e){
            DB::rollBack();
            return \response()->json(['status' => false, 'code' => 'FAILED', 'message' => $e->getMessage(), 'data' => []],500);
        }

    }

    public function activate(Request $request,$hospital_id)
    {
        try {
            $validate = $request->validate(['staff_id'=>'required']);
            $update = $this->changeStaffStatus($hospital_id,$validate['staff_id'],1);
            if($update)
                return \response()->json(['status'=>true,'code' => 'SUCCESS','message' => 'Patient Activated','data'=>[]]);
            else
                return \response()->json(['status'=>false,'code' => 'FAILED','message' => 'Oops something went wrong','data'=>[]],400);
        }
        catch (ValidationException $e){
            return \response()->json(['status' => false, 'code' => 'FAILED', 'message' => $e->validator->errors()->first(), 'data' => []],422);
        }
        catch (\Exception $e){
            DB::rollBack();
            return \response()->json(['status' => false, 'code' => 'FAILED', 'message' => $e->getMessage(), 'data' => []],500);
        }

    }

    public function search(Request $request,$hospital_id){
        try {
            $validate = $request->validate(['keyword'=>'nullable','unit'=>'required']);
            $staff = Staff::whereHas('units',function($query) use($validate){
                    $query->where('unit_id',$validate['unit']);
                });
            $staff->where(['hospital_id'=>$hospital_id,'active'=>1,'suspended_at'=>null]);
                if(!empty($validate['keyword']))
                    $staff->where('first_name','LIKE','%'.$validate['keyword'].'%');
            $staff = $staff->get(['id','first_name','middle_name','last_name','title']);
            return \response()->json(['status'=> true,'code' => 'SUCCESS','message' => '','data'=>$staff]);
        }
        catch (ValidationException $e){
           return \response()->json(['status'=> false,'code' => 'STAFF_SEARCH_FAILED','message' => $e->validator->errors()->first(),'data'=>[]],400);
        }
    }

    public function staffRoles(Request $request,$hospital_id){
        $staff = Staff::where(['hospital_id'=>$hospital_id,'suspended_at'=>null,'active'=>1])
            ->where('title','!=','Aide')->where('roles','!=','none')
            ->get();
        return \response()->json(['status'=>true,'code' => 'SUCCESS','message' => '','data'=>$staff]);
    }

    public function grid(Request $request,$hospital_id){
        try {
            $validate = $request->validate(['shift'=>'required','unit'=>'required','date'=>'required|date|date_format:Y-m-d']);
            $delegates = Delegate::whereHas('patient',function ($query){
                $query->where('discharged_at',null);
            })->whereHas('staff')
                ->with(['patient:id,first_name,middle_name,last_name','staff:id,first_name,middle_name,last_name','room:id,room_number'])
                ->where(['shift'=>$validate['shift'],'unit_id'=>$validate['unit']])
                ->whereDate('assigned_date',$validate['date'])->get(['id','room_id','patient_id','staff_id']);
//
            return \response()->json(['status'=> true,'code' => 'SUCCESS','message' => '','data'=>$delegates]);
        }
        catch (ValidationException $e){
            return \response()->json(['status'=> false,'code' => 'FAILED','message' => $e->validator->errors()->first(),'data'=>[]],400);
        }
    }

    public function assignment(Request $request,$hospital_id){
        try {
            $validate = $request->validate(['shift'=>'required','unit'=>'required','date'=>'required|date:Y-m-d']);
            $delegates = Delegate::with(['staff:id,first_name,middle_name,last_name,phone','staff.patients','room:id,room_number','staffWorkload:id,staff_id,work_load'])
                ->withCount('room')
                ->where(['shift'=>$validate['shift'],'unit_id'=>$validate['unit']])
                ->whereDate('assigned_date',$validate['date'])->get();

            foreach ($delegates as $item){
                $allocationRooms = Patient::where(['discharged_at' => null, 'unit_id'=>$item['unit'],'hospital_id' => $hospital_id])->get('room_number')->toArray();
                $rooms = Room::where(['hospital_id' => $hospital_id,'unit_id'=>$validate['unit']])->whereNotIn('room_number', $allocationRooms)->get(['id', 'room_number']);
                $item->available_rooms = $rooms;
            }

            return \response()->json(['status'=> true,'code' => 'SUCCESS','message' => '','data'=>$delegates]);
        }
        catch (ValidationException $e){
            return \response()->json(['status'=> false,'code' => 'FAILED','message' => $e->validator->errors()->first(),'data'=>[]],400);
        }
    }

    public function census(Request $request,$hospital_id){
        try {
            $validate = $request->validate(['shift'=>'required','unit'=>'required','date'=>'required|date:Y-m-d']);
            $delegates = Delegate::with(['staff:id,phone,email,last_name,middle_name,first_name,title','staff.patients:id,staff_id,first_name,room_number,middle_name,last_name,phone,designation','room'])
                ->where(['shift'=>$validate['shift'],'unit_id'=>$validate['unit']])
                ->whereDate('assigned_date',$validate['date'])->get();


            return \response()->json(['status'=> true,'code' => 'SUCCESS','message' => '','data'=>$delegates]);
        }
        catch (ValidationException $e){
            return \response()->json(['status'=> false,'code' => 'FAILED','message' => $e->validator->errors()->first(),'data'=>[]],400);
        }
    }

    public function map(Request $request,$hospital_id){
        try {
            $validate = $request->validate(['unit'=>'required']);
            $map = FloorMap::with('floor_rooms.patients.staff')->find($validate['unit']);
            return \response()->json(['status'=> true,'code' => 'SUCCESS','message' => '','data'=>$map]);
        }
        catch (ValidationException $e){
            return \response()->json(['status'=> false,'code' => 'FAILED','message' => $e->validator->errors()->first(),'data'=>[]],400);
        }
    }


    public function saveNotificationPreference(Request $request,$hospital_id){
        try {
            $validate = $request->validate(['staff_id'=>'required','inappnotification'=>'required','emailnotification'=>'required','pushnotification'=>'required','scheduleday'=>'required']);
            $preference = StaffNotificationPreference::where('staff_id',$validate['staff_id'])->first();
            if(!$preference)
                $preference = new StaffNotificationPreference();
            $preference->staff_id = $validate['staff_id'];
            $preference->preferences = "";
            $preference->inappnotification = json_encode($validate['inappnotification']);
            $preference->emailnotification = json_encode($validate['emailnotification']);
            $preference->pushnotification = json_encode($validate['pushnotification']);
            $preference->scheduleday = $validate['scheduleday'];
            $preference->save();
            return \response()->json(['status'=> true,'code' => 'SUCCESS','message' => '','data'=>[]]);
        }
        catch (ValidationException $e){
            return \response()->json(['status'=> false,'code' => 'FAILED','message' => $e->validator->errors()->first(),'data'=>[]],400);
        }
        catch (\Exception $e){
            return \response()->json(['status'=> false,'code' => 'FAILED','message' => $e->getMessage(),'data'=>[]],500);
        }
    }

    public function loadNotificationPreference(Request $request,$hospital_id,$staff_id){
        try {
            $preference = StaffNotificationPreference::where('staff_id',$staff_id)->first();
            if($preference) {
                $preference->preferences = "";
                $preference->inappnotification = json_decode($preference->inappnotification);
                $preference->emailnotification = json_decode($preference->emailnotification);
                $preference->pushnotification = json_decode($preference->pushnotification);
                $preference->scheduleday = $preference->scheduleday;
            }
            else {
                $preference = ['preferences'=>'','inappnotification'=>[],'emailnotification'=>[],'pushnotification'=>[],'scheduleday'=>""];
            }
            return \response()->json(['status'=> true,'code' => 'SUCCESS','message' => '','data'=>$preference]);
        }
        catch (ValidationException $e){
            return \response()->json(['status'=> false,'code' => 'FAILED','message' => $e->validator->errors()->first(),'data'=>[]],400);
        }
    }


    private function changeStaffStatus($hospital_id,$id,$status){
        return Staff::where(['hospital_id'=>$hospital_id,'id'=>$id])->update(['active'=>$status]);
    }




}

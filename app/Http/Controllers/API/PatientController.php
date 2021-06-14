<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\StaffDeactivated;
use App\Models\Patient;
use App\Models\PatientWorkLoadDimension;
use App\Models\Room;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class PatientController extends Controller
{
    public function workLoadList(Request $request,$hospital_id,$patient_id)
    {
        try {
            $workload = PatientWorkLoadDimension::where('patient_id',$patient_id)->first();
            $data = $workload->work_load ?? '[]';
            $data = json_decode($data);
           return \response()->json(['status'=> true,'code' => 'SUCCESS','message' => '','data'=>$data]);
        }
        catch (\Exception $e){
            DB::rollBack();
            return \response()->json(['status' => false, 'code' => 'FAILED', 'message' => $e->getMessage(), 'data' => []],500);
        }

    }


    public function saveWorkLoad(Request $request,$hospital_id)
    {

        try {
            $validate = $request->validate(['patient_id'=>'required','staff_id'=>'required','workload'=>'required']);
            $workload = PatientWorkLoadDimension::where(['patient_id'=>$validate['patient_id'],'staff_id'=>$validate['staff_id'],'hospital_id'=>$hospital_id])->first();
            if(!$workload)
                $workload = new PatientWorkLoadDimension();
            $workload->patient_id = $validate['patient_id'];
            $workload->staff_id = $validate['staff_id'];
            $workload->work_load = json_encode($validate['workload']);
            $workload->hospital_id = $hospital_id;
            if($workload->save())
                return \response()->json(['status'=>true,'code' => 'SUCCESS','message' => 'Work Load Saved','data'=>[]]);
            else
                return \response()->json(['status'=>false,'code' => 'FAILED','message' => 'Oops something went wrong','data'=>[]]);
        }
        catch (ValidationException $e){
            return \response()->json(['status' => false, 'code' => 'FAILED', 'message' => $e->validator->errors()->first(), 'data' => []]);
        }
        catch (\Exception $e){
            DB::rollBack();
            return \response()->json(['status' => false, 'code' => 'FAILED', 'message' => $e->getMessage(), 'data' => []]);
        }


    }


    public function unitPatientCount(Request $request,$hospital_id,$unit_id){
        $patient = Patient::where(['hospital_id'=>$hospital_id,'discharged_at'=>null,'unit_id'=>$unit_id])->count();
        $allocationRooms = Patient::where(['discharged_at' => null, 'unit_id'=>$unit_id,'hospital_id' => $hospital_id])->get('room_number')->toArray();
        $rooms = Room::where(['hospital_id' => $hospital_id,'unit_id'=>$unit_id])->whereNotIn('room_number', $allocationRooms)->count();
        $data = ['patient'=>$patient,'rooms'=>$rooms];
        return \response()->json(['status'=> true,'code' => 'SUCCESS','message' => '','data'=>$data]);
    }

    public function updateDesignation(Request $request,$hospital_id){
        try {
            $validate = $request->validate(['patient_id'=>'required','designation'=>'required']);
            $patient = Patient::find($validate['patient_id']);
            if(!$patient)
                return \response()->json(['status'=>false,'code' => 'FAILED','message' => 'No Patient Found','data'=>[]]);
            $patient->designation = $validate['designation'];
            if($patient->save())
                return \response()->json(['status'=>true,'code' => 'SUCCESS','message' => 'Designation Updated','data'=>[]]);
            else
                return \response()->json(['status'=>false,'code' => 'FAILED','message' => 'Oops something went wrong','data'=>[]]);
        }
        catch (ValidationException $e){
            return \response()->json(['status' => false, 'code' => 'FAILED', 'message' => $e->validator->errors()->first(), 'data' => []]);
        }
        catch (\Exception $e){
            DB::rollBack();
            return \response()->json(['status' => false, 'code' => 'FAILED', 'message' => $e->getMessage(), 'data' => []]);
        }
    }


}

<?php


namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Models\FloorMap;
use App\Models\Hospital;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;

class HospitalController extends Controller
{

    /**
     * Get all hospital names
     * code by sethu
     *
     * @return mixed
     */

    public function hospitals(Request $request){
        $keyword = $request->get('search');
        if(!empty($keyword))
            $hospitals = Hospital::where('name','LIKE','%'.$keyword.'%')->get(['name','active']);
        else
            $hospitals = Hospital::get(['id','name','active']);
        return \response()->json(['status'=> true,'code' => 'SUCCESS','message' => '','data'=>['hospitals'=>$hospitals]]);
    }

    /**
     * Get all staff by hospitals
     * code by sethu
     *
     * @param $hospital_id
     * @return mixed
     */

    public function staffs(Request $request, $hospital_id){
        $keyword = $request->get('search');
        if(!empty($keyword)) {
            $staffs = Staff::where(function ($query) use($keyword){
                $query->where('first_name','LIKE','%'.$keyword.'%')
                    ->orWhere('middle_name','LIKE','%'.$keyword.'%')
                    ->orWhere('last_name','LIKE','%'.$keyword.'%');
            })->where(['hospital_id' => $hospital_id, 'active' => 1, 'suspended_at' => null])
                ->get(['id', 'first_name', 'middle_name', 'last_name']);
        }
        else
            $staffs = Staff::where(['hospital_id'=>$hospital_id,'active'=>1,'suspended_at'=>null])->get(['id','first_name','middle_name','last_name']);

        return \response()->json(['status'=> true,'code' => 'SUCCESS','message' => '','data'=>['staffs'=>$staffs]]);
    }


    /**
     * Get all Patient by hospitals
     * code by sethu
     *
     * @param $hospital_id
     * @return mixed
     */

    public function patients(Request $request,$hospital_id){
            $keyword = $request->get('search');
            if(!empty($keyword)) {
                $patients = Patient::where(function ($query) use ($keyword){
                    $query->where( 'first_name','LIKE','%'.$keyword.'%')
                        ->orWhere('middle_name','LIKE','%'.$keyword.'%')
                        ->orWhere('last_name','LIKE','%'.$keyword.'%')
                        ->orWhere('phone','LIKE','%'.$keyword.'%');
                })->where(['hospital_id'=>$hospital_id,'discharged_at'=>null])
                    ->get(['id','first_name','middle_name','last_name','phone']);
            }
            else
            $patients = Patient::where(['hospital_id'=>$hospital_id,'discharged_at'=>null])->get(['id','first_name','middle_name','last_name']);
        return \response()->json(['status'=> true,'code' => 'SUCCESS','message' => '','data'=>['patients'=>$patients]]);

    }




    /**
     * Get all Admin by hospitals
     * code by sethu
     *
     * @param $hospital_id
     * @return mixed
     */


    public function admins(Request $request){
        $keyword = $request->get('search');
        if(!empty($keyword)) {
            $admins = User::where(function ($query) use ($keyword){
                $query->where( 'first_name','LIKE','%'.$keyword.'%')
                    ->orWhere('middle_name','LIKE','%'.$keyword.'%')
                    ->orWhere('last_name','LIKE','%'.$keyword.'%');
            })->whereIn('type',['moderator','administrator'])
                ->get(['id','first_name','middle_name','last_name']);
        }
        else
            $admins = User::whereIn('type',['moderator','administrator'])->get(['id','first_name','middle_name','last_name']);
        return \response()->json(['status'=> true,'code' => 'SUCCESS','message' => '','data'=>['admins'=>$admins]]);

    }



    /**
     * Get Unit Over by hospitals
     * code by sethu
     *
     * @param $hospital_id
     * @return mixed
     */

    public function unitOverview(Request $request, $hospital_id){
        if($request->has('from_date') && $request->has('to_date')){
            $from_date = $request->get('from_date');
            $to_date = $request->get('to_date');
            $unit = Unit::where('hospital_id',$hospital_id)->with(['patient'=>function($query) use($from_date,$to_date){
                $query->whereDate('created_at','>=',$from_date)->whereDate('created_at','<=',$to_date);
            },'staffs'=>function($query) use($from_date,$to_date){
                $query->whereDate('created_at','>=',$from_date)->whereDate('created_at','<=',$to_date);
            },'floormap']);
        }
        else
            $unit = Unit::where('hospital_id',$hospital_id)->with(['patient','staffs','floormap']);

        if($request->has('keyword'))
            $unit->where('name','LIKE','%'.$request->get('keyword').'%');

        $unit = $unit->get();
        $data = [];
        foreach ($unit as $item){
            $floor_map = FloorMap::where('unit_id',$item->id)->first();
            $total_nurse = $item->staffs->where('active',0)->where('title','Nurse')->count();
            $total_aides = $item->staffs->where('active',0)->where('title','Aide')->count();
            $total_patients = $item->patient->where('discharge',null)->count();
            $rooms = $floor_map->rooms ?? "";
            $rooms = explode(',',$rooms);
            $total_rooms = count($rooms);
            $utilized_rooms = $total_rooms - $total_patients;
            $data[] = [
                'unit_id'=>$item->id,
                'unit_name'=>$item->name,
                'total_room'=>$total_rooms,
                'brand_color'=>$item->brand_color,
                'total_patients'=>$total_patients,
                'total_nurse'=>$total_nurse,
                'total_aides'=>$total_aides,
                'available_bed'=> $total_rooms - $total_patients,
                'nurse_ratio'=>$total_patients.":".$total_nurse,
                'aide_ratio'=>$total_patients.":".$total_aides
            ];
        }


        return \response()->json(['status'=> true,'code' => 'SUCCESS','message' => '','data'=>$data]);

    }
}

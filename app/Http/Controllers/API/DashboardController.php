<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\Patient;
use App\Models\Room;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DashboardController extends Controller
{
    function dashboard(Request $request)
    {

        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $total_hospitals = Hospital::where('active',1);
        $total_hospitals = $this->filterByDate($total_hospitals,$from_date,$to_date)->count();

        $inactive_hospitals = Hospital::where('active',0);
        $inactive_hospitals = $this->filterByDate($inactive_hospitals,$from_date,$to_date)->count();

        $total_staff = Staff::where('active',1);
        $total_staff = $this->filterByDate($total_staff,$from_date,$to_date)->count();

        $inactive_staff = Staff::where('active',0);
        $inactive_staff = $this->filterByDate($inactive_staff,$from_date,$to_date)->count();

        $total_patients = Patient::where('discharged_at',null);
        $total_patients = $this->filterByDate($total_patients,$from_date,$to_date)->count();

        $total_rooms = Room::where('status','available');
        $total_rooms = $this->filterByDate($total_rooms,$from_date,$to_date)->count();

        $data = compact('total_hospitals','inactive_hospitals','total_staff','inactive_staff','total_patients','total_rooms');
       return \response()->json(['status'=> true,'code' => 'SUCCESS','message' => '','data'=>$data]);
    }

    private function filterByDate($query,$from_date,$to_date){
        if($from_date && $to_date)
            return $query->whereDate('created_at','>=',$from_date)->whereDate('created_at','<=',$to_date);
        return $query;
    }
}

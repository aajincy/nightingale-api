<?php

namespace App\Http\Controllers;

use App\Http\Requests\HospitalCreateRequest;
use App\Http\Requests\HospitalUpdateRequest;
use App\Http\Requests\HospitalsRequest;
use App\Models\Hospital;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\Unit;
use App\Models\User;
use DB;
use Illuminate\Http\Request;

class HospitalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(HospitalsRequest $request)
    {
        $count      = $request->has('count') ? $request->count: 10;
        $sort_by    = $request->has('sortby') ? $request->sortby :'created_at';
        $sort_order = $request->has('sortorder') ? $request->sortorder :'asc';

        $query      = Hospital::orderBy($sort_by,$sort_order);
        try{
            if($request->has('name')){
                $data = $request->get('name');
                $query->where(function ($q) use($request,$data){
                    $q->where('name', 'like', "%{$data}%");
                    $q->orWhere('state', 'like', "%{$data}%");
                    $q->orWhere('zipcode', 'like', "%{$data}%");
                });

            }
            if($request->has('active')){
                $active = filter_var($request->active,FILTER_VALIDATE_BOOLEAN);
                $query->where('active',$active);
            }
            $hospitals  = $query->paginate($count);
            return response()->json(['status'=>true,'code' => 'SUCCESS','message' => '','data'=>$hospitals]);
        } catch (\Exception $ex) {
            return response()->json(['status'=>false,'code' => 'FAILED','message' => $ex->getMessage()]);
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\HospitalCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(HospitalCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $hospital = Hospital::create([
                "name"         => $request->name,
                "address"      => $request->address,
                "city"         => $request->city,
                "state"        => $request->state,
                "zipcode"      => $request->zipcode,
                "brand_colors" => $request->brand_colors,
                "active"       =>  filter_var($request->active, FILTER_VALIDATE_BOOLEAN),
                "default_status"  =>  filter_var($request->default_status, FILTER_VALIDATE_BOOLEAN)
            ]);
            $hospital->admins()->sync($request->admins);
            $hospital->load('admins','staff','units');
            DB::commit();
            return response()->json(['status'=> true,'code' => 'HOSPITAL_CREATED','message' => '','data'=>$hospital]);
        }
        catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['status'=>false,'code' => 'HOSPITAL_CREATION_FAILED','message' => $ex->getMessage()]);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Hospital  $hospital
     * @return \Illuminate\Http\Response
     */
    public function show(Hospital $hospital)
    {
        $hospital->load('admins','staff','units');
        return response()->json(['status'=> true,'code' => 'SUCCESS','message' => '','data'=>$hospital]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Hospital  $hospital
     * @return \Illuminate\Http\Response
     */
    public function edit(Hospital $hospital)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\HospitalUpdateRequest  $request
     * @param  \App\Models\Hospital  $hospital
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Hospital $hospital)
    {
        DB::beginTransaction();
        try {
            $active_status = filter_var($request->has('active') ? $request->active : $hospital->active , FILTER_VALIDATE_BOOLEAN);
            $system_default_color = filter_var($request->has('default_status') ? $request->default_status : $hospital->default_status , FILTER_VALIDATE_BOOLEAN);
            $hospital->update([
                "name"         => $request->has('name') ? $request->name : $hospital->name,
                "address"      => $request->has('address') ? $request->address : $hospital->address,
                "city"         => $request->has('city') ? $request->city : $hospital->city,
                "state"        => $request->has('state') ? $request->state : $hospital->state,
                "zipcode"      => $request->has('zipcode') ? $request->zipcode : $hospital->zipcode,
                "brand_colors" => $request->has('brand_colors') ? $request->brand_colors : $hospital->brand_colors,
                "active"       => $active_status,
                "default_status"=>$system_default_color
            ]);
            $hospital->admins()->sync($request->admins);
            DB::commit();
            $hospital->load('admins','staff','units');
            return response()->json(['status'=> true,'code' => 'HOSPITAL_UPDATED','message' => '','data'=>$hospital]);
        }
        catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['status'=>false,'code' => 'HOSPITAL_UPDATE_FAILED','message' => $ex->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Hospital  $hospital
     * @return \Illuminate\Http\Response
     */
    public function destroy(Hospital $hospital)
    {
        DB::beginTransaction();
        try {
            $hospital->delete();
            DB::commit();
            return response()->json(['status'=> true,'code' => 'HOSPITAL_DELETED','message' => '']);
        }
        catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['status'=>false,'code' => 'HOSPITAL_DELETE_FAILED','message' => $ex->getMessage()]);
        }
    }



    /**
     * Get all Unit by hospitals
     * code by sethu
     *
     * @param $hospital_id
     * @return mixed
     */

    public function units($hospital_id){
        $unit =  Unit::where('hospital_id',$hospital_id)->get(['id','name']);
        return response()->json(['status'=>true,'code' => 'SUCCESS','message' => '','data'=>$unit]);
    }

    /**
     * Get users activity by hospital
     *
     * @param $hospital_id
     * @return mixed
     */

    public function activity(Hospital $hospital, Request $request){
//        $staff = $hospital->staff->load(['user.tokens', function($query) use ($request){
//            if($request->has('start')){
//                $start  = Carbon::parse($request->start_date)->startOfDay();
//                $end    = $request->end_date    ? Carbon::parse($request->end_date)->endOfDay() : $start->endOfDay();
//                $query->whereBetween('login_at',[$start,$end]);
//            }
//            $query->select('location','id', 'login_at', 'last_active_on', 'logout_at');
//        }]);

    }



}

<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Hospital;
use App\Http\Requests\PatientCreateRequest;
use App\Http\Requests\PatientUpdateRequest;
use App\Http\Requests\PatientsRequest;
use DB;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Hospital $hospital,PatientsRequest $request)
    {
        $count      = $request->has('count') ? $request->count: 10;
        $sort_by    = $request->has('sortby') ? $request->sortby :'created_at';
        $sort_order = $request->has('sortorder') ? $request->sortorder :'asc';
        $query      = $hospital->patient();
        try{
            if($request->has('search')){
                $query->where('first_name','like', "%$request->search%")
                ->orWhere('last_name','like', "%$request->search%")
                ->orWhereRaw("concat(first_name, ' ', last_name) like '%$request->search%' ")
                ->orWhere('phone','like', "%$request->search%");

            }
            $patients  = $query->orderBy($sort_by,$sort_order)->paginate($count);

            return response()->json(['status'=>true,'code' => 'SUCCESS','message' => '','data'=>$patients]);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PatientCreateRequest $request,Hospital $hospital)
    {
        DB::beginTransaction();
        try {
            $patient = $hospital->patient()->create([
                "first_name"  => $request->first_name,
                "middle_name" => $request->middle_name ?? null,
                "last_name"   => $request->last_name,
                "email"       => $request->email,
                "phone" => $request->phone,
                "date_of_birth" => $request->date_of_birth,
                "weight" => $request->weight,
                "diagnosis" => $request->diagnosis,
                "admit_date" => $request->admit_date,
                "unit_id"=>$request->unit_id,
                "unit_name"=>$request->unit_name,
                "staff_id"=>$request->staff_id,
                "room_number" => $request->room_number,
                "notes" => $request->notes,
                "tags" => $request->tags,
                "discharged_at" => $request->discharged_at,
            ]);
            DB::commit();

            $patient->load('hospital');
            return response()->json(['status'=> true,'code' => 'PATIENT_CREATED','message' => '','data'=>$patient]);
        }
        catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['status'=>false,'code' => 'PATIENT_CREATION_FAILED','message' => $ex->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function show(Hospital $hospital,Patient $patient)
    {
        $patient->load('hospital');
        return response()->json(['status'=> true,'code' => 'SUCCESS','message' => '','data'=>$patient]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function edit(Patient $patient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function update(PatientUpdateRequest $request,Hospital $hospital,Patient $patient)
    {
        DB::beginTransaction();
        try {
            $patient ->update([
                "first_name"  => $request->first_name,
                "middle_name" => $request->middle_name ?? null,
                "last_name"   => $request->last_name,
                "email"       => $request->email,
                "phone" => $request->phone,
                "date_of_birth" => $request->date_of_birth,
                "weight" => $request->weight,
                "diagnosis" => $request->diagnosis,
                "admit_date" => $request->admit_date,
                "unit_id"=>$request->unit_id,
                "unit_name"=>$request->unit_name,
                "room_number" => $request->room_number,
                "notes" => $request->notes,
                "tags" => $request->has('tags') ? $request->tags : $patient->tags,
                "discharged_at" => $request->discharged_at,
            ]);
            DB::commit();

            $patient->load('hospital');
            return response()->json(['status'=> true,'code' => 'PATIENT_UPDATED','message' => '','data'=>$patient]);
        }
        catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['status'=>false,'code' => 'PATIENT_UPDATION_FAILED','message' => $ex->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function destroy(Hospital $hospital,Patient $patient)
    {
        DB::beginTransaction();
        try {
            $patient->delete();
            DB::commit();
            return response()->json(['status'=> true,'code' => 'PATIENT_DELETED','message' => '']);
        }
        catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['status'=>false,'code' => 'PATIENT_DELETE_FAILED','message' => $ex->getMessage()]);
        }
    }
}

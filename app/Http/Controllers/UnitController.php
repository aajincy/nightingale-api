<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Hospital;
use App\Http\Requests\UnitCreateRequest;
use App\Http\Requests\UnitUpdateRequest;
use App\Http\Requests\UnitsRequest;
use App\Models\WorkLoadDimension;
use DB;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Hospital $hospital,UnitsRequest $request)
    {

        $count = $request->has('count') ? $request->count: 10;
        $sort_by = $request->has('sortby') ? $request->sortby :'created_at';
        $sort_order = $request->has('sortorder') ? $request->sortorder :'asc';

        try {
            $query      = $hospital->units();

            if($request->has('name')){
                $data   = $request->get('name');
                $units  = $query->where('name', 'like', "%{$data}%");
            }
            if($request->has('select')){
                $explo = array_filter(explode(",",$request->select));
                $query->select("id",...$explo);
            }
            $query->addSelect(DB::raw("*,CONCAT(nurse_ratio_patient_day,':',nurse_ratio_nurse_day) as patient_nurse_day_ratio,
                CONCAT(aides_ratio_patient_day,':',aides_ratio_aide_day) as patient_aides_day_ratio,
                CONCAT(nurse_ratio_patient_night,':',nurse_ratio_nurse_night) as patient_nurse_night_ratio,
                CONCAT(aides_ratio_patient_night,':',aides_ratio_aide_night) as patient_aides_night_ratio
            "));

            if($sort_by == 'patient_nurse_day_ratio')
                $units = $query->orderBy('nurse_ratio_patient_day',$sort_order)->orderBy('nurse_ratio_nurse_day',$sort_order)->paginate($count);
            elseif ($sort_by == 'patient_aides_day_ratio')
                $units = $query->orderBy('aides_ratio_patient_day',$sort_order)->orderBy('aides_ratio_aide_day',$sort_order)->paginate($count);
            elseif ($sort_by == 'patient_nurse_night_ratio')
                $units = $query->orderBy('nurse_ratio_patient_night',$sort_order)->orderBy('nurse_ratio_nurse_night',$sort_order)->paginate($count);
            elseif ($sort_by == 'patient_aides_night_ratio')
                $units = $query->orderBy('aides_ratio_patient_night',$sort_order)->orderBy('aides_ratio_aide_night',$sort_order)->paginate($count);
            else
                $units = $query->orderBy($sort_by, $sort_order)->paginate($count);


            return response()->json(['status'=>true,'code' => 'SUCCESS','message' => '','data'=>$units]);
        } catch (\Exception $e) {
            return response()->json(['status'=>false,'code' => 'FAILED','message' => $e->getMessage()]);
        }


    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UnitCreateRequest $request,Hospital $hospital)
    {
        DB::beginTransaction();
        try {
            $unit = $hospital->units()->create([
                'name'                      => $request->name,
                'rooms'                     => $request->rooms,
                'brand_color'               => $request->brand_color,
                'nurse_ratio_patient_day'   => $request->nurse_ratio_patient_day,
                'nurse_ratio_nurse_day'     => $request->nurse_ratio_nurse_day,
                'aides_ratio_patient_day'   => $request->aides_ratio_patient_day,
                'aides_ratio_aide_day'      => $request->aides_ratio_aide_day,
                'nurse_ratio_patient_night' => $request->nurse_ratio_patient_night,
                'nurse_ratio_nurse_night'   => $request->nurse_ratio_nurse_night,
                'aides_ratio_patient_night' => $request->aides_ratio_patient_night,
                'aides_ratio_aide_night'    => $request->aides_ratio_aide_night,
                'designation'               => $request->designation,
                'aggregated'                => filter_var($request->aggregated, FILTER_VALIDATE_BOOLEAN),
                'workload_dimensions'       => $request->workload_dimensions,
            ]);
            $unit->staffs()->sync($request->staffs);
            $unit->load('hospital','staffs');

            DB::commit();
            return response()->json(['status'=> true,'code' => 'HOSPITAL_UNIT_CREATED','message' => '','data'=>$unit]);
        }
        catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['status'=>false,'code' => 'HOSPITAL_UNIT_CREATION_FAILED','message' => $ex->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function show(Hospital $hospital,Unit $unit)
    {
        $unit->load('hospital','staffs');
        return response()->json(['status'=> true,'code' => 'SUCCESS','message' => '','data'=>$unit]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function update(UnitUpdateRequest $request, Hospital $hospital, Unit $unit)
    {
        DB::beginTransaction();
        try {
            $unit->update([
                'name'                      => $request->has('name') ? $request->name : $unit->name,
                'rooms'                     => $request->has('rooms') ? $request->rooms : $unit->rooms,
                'brand_color'               => $request->has('brand_color') ? $request->brand_color : $unit->brand_color,
                'nurse_ratio_patient_day'   => $request->has('nurse_ratio_patient_day') ? $request->nurse_ratio_patient_day : $unit->nurse_ratio_patient_day,
                'nurse_ratio_nurse_day'     => $request->has('nurse_ratio_nurse_day') ? $request->nurse_ratio_nurse_day : $unit->nurse_ratio_nurse_day,
                'aides_ratio_patient_day'   => $request->has('aides_ratio_patient_day') ? $request->aides_ratio_patient_day : $unit->aides_ratio_patient_day,
                'aides_ratio_aide_day'      => $request->has('aides_ratio_aide_day') ? $request->aides_ratio_aide_day : $unit->aides_ratio_aide_day,
                'nurse_ratio_patient_night' => $request->has('nurse_ratio_patient_night') ? $request->nurse_ratio_patient_night : $unit->nurse_ratio_patient_night,
                'nurse_ratio_nurse_night'   => $request->has('nurse_ratio_nurse_night') ? $request->nurse_ratio_nurse_night : $unit->nurse_ratio_nurse_night,
                'aides_ratio_patient_night' => $request->has('aides_ratio_patient_night') ? $request->aides_ratio_patient_night : $unit->aides_ratio_patient_night,
                'aides_ratio_aide_night'    => $request->has('aides_ratio_aide_night') ? $request->aides_ratio_aide_night : $unit->aides_ratio_aide_night,
                'designation'               => $request->has('designation') ? $request->designation : $unit->designation,
                'aggregated'                => filter_var($request->aggregated, FILTER_VALIDATE_BOOLEAN),
                'workload_dimensions'       => $request->has('workload_dimensions') ? $request->workload_dimensions : $unit->workload_dimensions,
            ]);
            $unit->staffs()->sync($request->staffs);
            // if($request->has('staffs')){
            // }
            $unit->load('hospital','staffs');
            DB::commit();
            return response()->json(['status'=> true,'code' => 'HOSPITAL_UNIT_UPDATED','message' => '','data'=>$unit]);
        }
        catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['status'=>false,'code' => 'HOSPITAL_UNIT_UPDATE_FAILED','message' => $ex->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Hospital $hospital,Unit $unit)
    {
        DB::beginTransaction();
        try {
            $unit->delete();
            DB::commit();
            return response()->json(['status'=> true,'code' => 'HOSPITAL_UNIT_DELETED','message' => '']);
        }
        catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['status'=>false,'code' => 'HOSPITAL_UNIT_DELETE_FAILED','message' => $ex->getMessage()]);
        }
    }


    /**
     * code by sethu
     * Get WorkLoad corresponding to an unit
     *
     */

    public function workload(Request $request,$unit_id){
        $workload = Unit::find($unit_id);
        return response()->json(['status'=> true,'code' => 'SUCCESS','message' => '','data'=>$workload->workload_dimensions]);
    }

}

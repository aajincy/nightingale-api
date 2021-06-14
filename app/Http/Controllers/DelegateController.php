<?php

namespace App\Http\Controllers;
use App\Models\Delegate;
use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\Room;
use App\Models\Unit;
use App\Models\Patient;
use App\Http\Requests\DelegateCreateRequest;
use DB;

class DelegateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $delegates = Delegate::get()->toJson(JSON_PRETTY_PRINT);
        return response($delegates, 200);
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
    public function store(Unit $unit, Room $room, DelegateCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $delegates = $room->delegates()->create([
                "patient_id"     => $request->patient_id,
                "unit_id"        => $room->unit_id,
                "staff_id"       => $request->staff_id,
                "shift"          => $request->shift,
                "assigned_date"  => $request->assigned_date
            ]);
        DB::commit();
            $delegates->load('staff','room','patient');
            return response()->json(['status'=> true,'code' => 'PUBLISHED','message' => '','data'=>$delegates]);
     }
    catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['status'=>false,'code' => 'PUBLISH_FAILED','message' => $ex->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Delegate $delegates)
    {
        $delegates->load('staff');
        return response()->json(['status'=> true,'code' => 'SUCCESS','message' => '','data'=>$delegates]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

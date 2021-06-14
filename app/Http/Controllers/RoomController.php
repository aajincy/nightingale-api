<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use App\Http\Requests\RoomCreateRequest;
use App\Models\Unit;
use App\Models\Hospital;
use App\Http\Requests\RoomUpdateRequest;
use DB;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $room = Room::get()->toJson(JSON_PRETTY_PRINT);
        return response($room, 200);
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
    public function store(Hospital $hospital,Unit $unit,RoomCreateRequest $request)
    {
        DB::beginTransaction();
        try {
        $room = $unit->room()->create([
                "room_number"   => $request->room_number,
                "hospital_id"   => $request->hospital_id,
                "unit_id"       => $request->unit_id,
                "status"        => $request->status,
        ]);
        DB::commit();
        $room->load('hospital','unit');
        return response()->json(['status'=> true,'code' => 'ROOM_CREATED','message' => '','data'=>$room]);
        }
        catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['status'=>false,'code' => 'ROOM_CREATED_FAILED','message' => $ex->getMessage()]);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function show(Room $room)
    {
        $room->load('hospital');
        return response()->json(['status'=> true,'code' => 'SUCCESS','message' => '','data'=>$room]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function edit(Room $room)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function update(RoomUpdateRequest $request,Room $room,Hospital $hospital)
    {
        DB::beginTransaction();
        try {
            $room ->update([
                "room_number"    => $request->room_number,
                "hospital_id"    => $request->hospital_id,
                "unit_id"        => $request->unit_id,
                "status"         => $request->status,
        ]);
        DB::commit();
        return response()->json(['status'=> true,'code' => 'ROOM_UPDATED','message' => '','data'=>$room]);
    }
    catch (\Exception $ex) {
        DB::rollback();
        return response()->json(['status'=>false,'code' => 'ROOM_UPDATE_FAILED','message' => $ex->getMessage()]);
    }
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function destroy(Room $room)
    {
        DB::beginTransaction();
        try {
            $room->delete();
            DB::commit();
            return response()->json(['status'=> true,'code' => 'ROOM_DELETED','message' => '']);
        }
            catch (\Exception $ex) {
                DB::rollback();
                return response()->json(['status'=>false,'code' => 'ROOM_DELETE_FAILED','message' => $ex->getMessage()]);
            }
        }
    }


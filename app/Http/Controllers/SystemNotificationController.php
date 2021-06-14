<?php

namespace App\Http\Controllers;

use App\Models\SystemNotification;
use Illuminate\Http\Request;
use DB;
class SystemNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        $request->validate([
            "title" => ["required", "string"],
            "notification_triat" => ["required", "string"],
            "sms" => ["required", "boolean"],
            "email" => ["required", "boolean"],
            "push_notification" => ["required", "boolean"],
            "in_app_notification" => ["required", "boolean"],
        ]);
        DB::beginTransaction();
        try {
            $systemNotification = SystemNotification::create([
                "title" => $request->title,
                "notification_triat" => $request->notification_triat,
                "sms" => filter_var($request->sms, FILTER_VALIDATE_BOOLEAN),
                "email" => filter_var($request->email, FILTER_VALIDATE_BOOLEAN),
                "push_notification" => filter_var($request->push_notification, FILTER_VALIDATE_BOOLEAN),
                "in_app_notification" =>filter_var( $request->in_app_notification, FILTER_VALIDATE_BOOLEAN),
            ]);
            DB::commit();
            return response()->json(['status'=> true,'code' => 'SYSTEM_NOTIFICATION_CREATED','message' => '','data'=>$systemNotification]);
        }
        catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['status'=>false,'code' => 'SYSTEM_NOTIFICATION_CREATION_FAILED','message' => $ex->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SystemNotification  $systemNotification
     * @return \Illuminate\Http\Response
     */
    public function show(SystemNotification $systemNotification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SystemNotification  $systemNotification
     * @return \Illuminate\Http\Response
     */
    public function edit(SystemNotification $systemNotification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SystemNotification  $systemNotification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SystemNotification $systemNotification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SystemNotification  $systemNotification
     * @return \Illuminate\Http\Response
     */
    public function destroy(SystemNotification $systemNotification)
    {
        //
    }
}

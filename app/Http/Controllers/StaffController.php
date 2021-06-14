<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Staff;
use App\Models\User;
use App\Models\Hospital;
use App\Http\Requests\StaffCreateRequest;
use App\Http\Requests\StaffUpdateRequest;
use App\Http\Requests\StaffsRequest;
use App\Http\Requests\NotificationChannelRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Hospital $hospital,StaffsRequest $request)
    {

        $count      = $request->has('count') ? $request->count : $request->all();
        $sort_by    = $request->has('sortby') ? $request->sortby :'created_at';
        $sort_order = $request->has('sortorder') ? $request->sortorder :'asc';
        $query      = $hospital->staff();
        try{
            if($request->has('search')){
               $query->where('first_name','like', "%$request->search%")
               ->orWhere('last_name','like', "%$request->search%")
               ->orWhereRaw("concat(first_name, ' ', last_name) like '%$request->search%' ")
                ->orWhere('phone','like', "%$request->search%");
            }

            $staffs  = $query->orderBy($sort_by,$sort_order)->paginate($count);

            return response()->json(['status'=>true,'code' => 'SUCCESS','message' => '','data'=>$staffs]);
        } catch (\Exception $ex) {
            return response()->json(['status'=>false,'code' => 'FAILED','message' => $ex->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StaffCreateRequest $request, Hospital $hospital)
    {
        DB::beginTransaction();
        try {
            $staff = $hospital->staff()->create([
                "first_name"=>$request->first_name,
                "middle_name"=>$request->middle_name,
                "last_name"=>$request->last_name,
                "email"=>$request->email,
                "phone"=>$request->phone,
                "active"=>filter_var($request->active, FILTER_VALIDATE_BOOLEAN),
                "image" => $request->profile_picture,
                "type" => $request->type,
                "roles" => $request->roles,
                "title" => $request->title,
                "credentials" => $request->credentials,
              "sms_notifications" => filter_var($request->sms_notifications, FILTER_VALIDATE_BOOLEAN),
                "certifications" => $request->certifications,
                "start_date" => $request->start_date,
                "experience" => $request->experience,
            ]);
            DB::commit();
            $staff->units()->sync($request->units);
            $staff->load('hospital','user','units');
            return response()->json(['status'=> true,'code' => 'HOSPITAL_STAFF_CREATED','message' => '','data'=>$staff]);
        }
        catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['status'=>false,'code' => 'HOSPITAL_STAFF_CREATION_FAILED','message' => $ex->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function show(Hospital $hospital,Staff $staff)
    {
        $staff->load('hospital','user','units','cs_requests','user.tokens:location,id,login_at,last_active_on,logout_at');
        return response()->json(['status'=> true,'code' => 'SUCCESS','message' => '','data'=>$staff]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function edit(Hospital $hospital,Staff $staff)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function update(Hospital $hospital,StaffUpdateRequest $request, Staff $staff)
    {
        DB::beginTransaction();
        try {
            $staff->update([
                "first_name"=>$request->first_name,
                "middle_name"=>$request->middle_name,
                "last_name"=>$request->last_name,
                "email"=>$request->email,
                "phone"=>$request->phone,
                "active"=>filter_var($request->active, FILTER_VALIDATE_BOOLEAN),
                "image" => $request->profile_picture,
                "type" => $request->type,
                "roles" => $request->roles,
                "title" => $request->title,
                "credentials" =>$request->credentials,
                "sms_notifications" => filter_var($request->sms_notifications, FILTER_VALIDATE_BOOLEAN),
                "certifications" => $request->certifications,
                "start_date" => $request->start_date,
                "experience" => $request->experience,
            ]);
            DB::commit();
            if($request->has('units')){
                $staff->units()->sync($request->units);
            }
            $staff->load('hospital','user','units');
            return response()->json(['status'=> true,'code' => 'HOSPITAL_STAFF_UPDATED','message' => '','data'=>$staff]);
        }
        catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['status'=>false,'code' => 'HOSPITAL_STAFF_UPDATION_FAILED','message' => $ex->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function destroy(Hospital $hospital,Staff $staff)
    {
        DB::beginTransaction();
        try {
            $staff->delete();
            DB::commit();
            return response()->json(['status'=> true,'code' => 'HOSPITAL_STAFF_DELETED','message' => '']);
        }
        catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['status'=>false,'code' => 'HOSPITAL_STAFF_DELETION_FAILED','message' => $ex->getMessage()]);
        }
    }

    public function suspend(Hospital $hospital,Staff $staff,Request $request)
    {
        $suspend           = filter_var($request->suspend, FILTER_VALIDATE_BOOLEAN);
        $days              = $request->count;
        $suspended_till    = now()->addDays($days);

        DB::beginTransaction();
        try {

            if($suspend): // Suspend a staff
                if(!$staff->isSuspended()):
                    $staff->update([
                        "suspended_at" => now(),
                        "suspended_till" => $suspended_till,
                    ]);
                    $staff->user()->update(['active'=>false]);
                endif;
            else: //Remove Suspension
                $staff->update([
                    "suspended_at" => null,
                    "suspended_till" => null,
                ]);
                $staff->user()->update(['active'=>true]);
            endif;
            DB::commit();
            return response()->json(['status'=> true,'code' => 'HOSPITAL_STAFF_SUSPENDED','message' => '','data'=>$staff]);
            }catch (\Exception $ex) {
                DB::rollback();
                return response()->json(['status'=>false,'code' => 'HOSPITAL_STAFF_SUSPENSION_FAILED','message' => $ex->getMessage()]);
            }
    }

    public function csrequestindex(Request $request)
    {
        $user = auth()->user();
        if($user->staff()->exists()){
           $csr =  $user->staff->csrequest;
           $csr->load('staff');
           return response()->json(['status'=> true,'code' => 'SUCCESS','message' => '','data'=>$csr]);
          }
        else{
            return response()->json([],405);
        }
    }

    public function csrequeststore(Request $request)
    {
        $user = auth()->user();
        if($user->staff()->exists()){
            DB::beginTransaction();
            try {
                $customer_request = $user->staff->cs_requests()->create([
                    "hospital_id" => $user->staff->hospital_id,
                    "reason_for_contact" => $request->reason_for_contact,
                    "message" => $request->message
                    ]);
                DB::commit();
                $customer_request->load('staff','staff.user');
                return response()->json(['status'=> true,'code' => 'CS_REQUEST_CREATED','message' => '','data'=>$customer_request]);
            }
            catch (\Exception $ex) {
                DB::rollback();
                return response()->json(['status'=>false,'code' => 'CS_REQUEST_CREATION_FAILED','message' => $ex->getMessage()]);
            }
        }
        else{
            return response()->json([],405);
        }
    }
    public function notificationChannels()
    {
        $system_notfications = \App\Models\SystemNotification::get();
        $user                = auth()->user();
        $staff               = $user->staff;
        $response            = [];
        foreach ($system_notfications as $n) {
            $s_f = $staff->notifications()->where('system_notification_id',$n->id)->first();
            $response[] = [
                "notification_id"               => $n->id,
                "title"                         => $n->title,
                "sms"                           => $n->sms,
                "email"                         => $n->email,
                "push_notification"             => $n->push_notification,
                "in_app_notification"           => $n->in_app_notification,
                "sms_status"                    => $s_f->sms ?? false,
                "email_status"                  => $s_f->email ?? false,
                "push_notification_status"      => $s_f->push_notification ?? false,
                "in_app_notification_status"    => $s_f->in_app_notification ?? false,
                "staff_id"                      => $staff->id
            ];
        }
        return $response;
    }
    public function notificationChannel(NotificationChannelRequest $request)
    {

        DB::beginTransaction();
        $user   =   auth()->user();
        $staff  =   $user->staff;
        $systemNotification = \App\Models\SystemNotification::find($request->notification_id);
        if($systemNotification->{$request->type} == true){
            $notification = $staff->notifications()->updateOrCreate(
                [
                    "staff_id" => $staff->id,
                    "system_notification_id" => $request->notification_id,
                ],
                [
                    $request->type => filter_var($request->status, FILTER_VALIDATE_BOOLEAN),
                ]
            );
            DB::commit();
            $notification->load('staff');
            return response()->json(['status'=> true,'message' => '','data'=>$notification]);
        }
        else{
            return response()->json(['status'=>false,'code' => 'NOTIFICATION_CHANNEL_UPDATE_FAILED','message' => ""],402);
        }
    }

}

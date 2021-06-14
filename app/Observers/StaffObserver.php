<?php

namespace App\Observers;

use DB;
use App\Models\Staff;
use App\Models\User;
use App\Models\SystemNotification;
use App\Notifications\StaffCreationNotification;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StaffCreateRequest;

class StaffObserver
{
    /**
     * Handle the Staff "created" event.
     *
     * @param  \App\Models\Staff  $staff
     * @return void
     */
    public function creating(Staff $staff)
    {
        $request = request();
        $user = User::create([
            "first_name"  => $staff->first_name,
            "middle_name" => $staff->middle_name ?? null,
            "last_name"   => $staff->last_name,
            "email"       => $staff->email,
            "phone"       => $staff->phone,
            "active"      => filter_var($staff->active, FILTER_VALIDATE_BOOLEAN),
            "type"        => $staff->type,
            "password"    => Hash::make($request->password),
        ]);
        $staff->user_id = $user->id;
    }
    /**
     * Handle the Staff "created" event.
     *
     * @param  \App\Models\Staff  $staff
     * @return void
     */
    public function created(Staff $staff)
    {
        $staff->user->notify(new StaffCreationNotification($staff->user));
        /** Copy Notifcations */
        $notificationChannels = SystemNotification::get();
        foreach ($notificationChannels as $notificationChannel) {
            $notification = $staff->notifications()->create([
                    "staff_id"                 => $staff->id,
                    "system_notification_id"   => $notificationChannel->id,
                    "sms"                      => $notificationChannel->sms,
                    "email"                    => $notificationChannel->email,
                    "in_app_notification"      => $notificationChannel->in_app_notification,
                    "push_notification"        => $notificationChannel->push_notification,
            ]);
        }        
    }

    /**
     * Handle the Staff "updated" event.
     *
     * @param  \App\Models\Staff  $staff
     * @return void
     */
    public function saving(Staff $staff)
    {
        $request = request();
        DB::beginTransaction();
        try {
            $staff->user()->update([
                "first_name"  => $staff->first_name,
                "middle_name" => $staff->middle_name ?? null,
                "last_name"   => $staff->last_name,
                "email"       => $staff->email,
                "type"        => $staff->type,
                "active"      => filter_var($staff->active, FILTER_VALIDATE_BOOLEAN),
                "phone"       => $staff->phone,
            ]);
            if($request->has('password') && !empty($request->password)){
                $staff->user()->update([
                    "password"    => Hash::make($request->password)
                ]);
            }
            DB::commit();
        }
        catch (\Exception $ex) {
            DB::rollback();
        }
    }

    /**
     * Handle the Staff "updated" event.
     *
     * @param  \App\Models\Staff  $staff
     * @return void
     */
    public function updated(Staff $staff)
    {
        //
    }

    /**
     * Handle the Staff "deleted" event.
     *
     * @param  \App\Models\Staff  $staff
     * @return void
     */
    public function deleted(Staff $staff)
    {
        $staff->units()->sync([]);
        $staff->user()->delete();
        //
    }

    /**
     * Handle the Staff "restored" event.
     *
     * @param  \App\Models\Staff  $staff
     * @return void
     */
    public function restored(Staff $staff)
    {
        //
    }

    /**
     * Handle the Staff "force deleted" event.
     *
     * @param  \App\Models\Staff  $staff
     * @return void
     */
    public function forceDeleted(Staff $staff)
    {
        //
    }
}

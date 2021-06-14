<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\UserLoginLog;
use Carbon\Carbon;
use DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UsersRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UsersRequest $request)
    {

        $count = $request->has('count') ? $request->count: 10;
        $sort_by    = $request->has('sortby') ? $request->sortby :'created_at';
        $sort_order = $request->has('sortorder') ? $request->sortorder :'desc';
        $query = User::whereIn('type',['administrator','moderator']);

        try{
            if($request->has('search')){
               $query->where('first_name','like', "%$request->search%")
               ->orWhere('last_name','like', "%$request->search%")
               ->orWhereRaw("concat(first_name, ' ', last_name) like '%$request->search%' ")
                ->orWhere('phone','like', "%$request->search%");
            }
        $admins = $query->orderBy($sort_by,$sort_order)->paginate($count);
        return response()->json(['status'=>true,'code' => 'SUCCESS','message' => '','data'=>$admins]);
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
    public function store(UserCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $admin = User::create([
                "first_name"  => $request->first_name,
                "middle_name" => $request->middle_name ?? null,
                "last_name"   => $request->last_name,
                "email"       => $request->email,
                "type"        => $request->type,
                "active"      => filter_var($request->active, FILTER_VALIDATE_BOOLEAN),
                "phone"       => $request->phone,
                "password"    => Hash::make($request->password),
            ]);
            DB::commit();
            //$admin = User::where('email', request()->input('email'))->first();

            //$notification = $admin->notify(new \App\Notifications\UserCreationNotification($admin));

            return response()->json(['status'=> true,'code' => 'ADMINISTRATOR_CREATED','message' => '','data'=>$admin]);
        }
        catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['status'=>false,'code' => 'ADMINISTRATOR_CREATION_FAILED','message' => $ex->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $admin
     * @return \Illuminate\Http\Response
     */
    public function show(User $admin)
    {
        return response()->json(['status'=> true,'code' => 'SUCCESS','message' => '','data'=>$admin]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $admin
     * @return \Illuminate\Http\Response
     */
    public function edit(User $admin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $admin
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, User $admin)
    {
        DB::beginTransaction();
        try {
            $admin->update([
                "first_name"  => $request->first_name,
                "middle_name" => $request->middle_name ?? null,
                "last_name"   => $request->last_name,
                "email"       => $request->email,
                "type"        => $request->type,
                "active"      => filter_var($request->active, FILTER_VALIDATE_BOOLEAN),
                "phone"       => $request->phone,
            ]);
            // TODO : Remove on New
            // if($request->has('password')){
            //     $admin->update([
            //         "password"    => Hash::make($request->password)
            //     ]);
            // }
            DB::commit();
            return response()->json(['status'=> true,'code' => 'ADMINISTRATOR_UPDATED','message' => '','data'=>$admin]);
        }
        catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['status'=>false,'code' => 'ADMINISTRATOR_UPDATION_FAILED','message' => $ex->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $admin
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $admin)
    {
        DB::beginTransaction();
        try {
            $admin->delete();
            DB::commit();
            return response()->json(['status'=> true,'code' => 'ADMINISTRATOR_DELETED','message' => '']);
        }
        catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['status'=>false,'code' => 'ADMINISTRATOR_DELETION_FAILED','message' => $ex->getMessage()]);
        }
    }

    public static function updateUserActivity($user_id){
        $today = Carbon::now()->toDateString();
        $user_activity = UserLoginLog::where(['staff_id'=>$user_id,'date'=>$today])->first();
        if(!$user_activity){
            $ull = new UserLoginLog();
            $ull->staff_id = $user_id;
            $ull->date = $today;
            $ull->save();
        }
    }


}

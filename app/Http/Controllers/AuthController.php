<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use DB, Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        try {
            $credentials = [
                "email" => $request->username,
                "password" => $request->password
            ];

            /** Attempt to Login */
            if (!Auth::attempt($credentials)):
                throw new \Exception("User Credentials Incorrect", 1);
            endif;
            if($request->has('role')){
                $staff = Staff::where(['email'=>$request->username, 'roles' => $request->get('role')])->first();
                if($staff)
                    $user = User::where('email', $request->username)->first();
                else
                    throw new \Exception("User Credentials Incorrect", 1);
            }
           else
               $user  = User::where('email',$request->username)->first();
            return response()->json([
                'status'  => true,
                'response_code' => 200,
                'code' => 'USER_LOGIN_SUCCESS',
                'data' => $user->makeToken(),
                'message' => null,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status'  => false,
                'response_code' => 401,
                'code' => 'USER_LOGIN_FAILED',
                'message' => $ex->getMessage(),
            ]);
        }
    }
    public function logout(Request $request)
    {
        try {
            $auth   = $request->bearerToken();
            $user = auth()->user()->token($auth)->logout();
            return response()->json([
                'status'  => true,
                'code'    => 'USER_LOGOUT_SUCCESS',
                'data'    => $user,
                'message' => null,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status'  => false,
                'code' => 'USER_LOGOUT_FAILED',
                'message' => $ex->getMessage(),
            ]);
        }
    }
    public function forgotPassword(Request $request)
    {

        try {
            $credentials = $request->validate(['email' => 'required|email|exists:users,email'],['email.exists'=>'User not exist']);
            $status = Password::sendResetLink($credentials);
            dd($status);
            if($status === Password::RESET_THROTTLED) throw new \Exception("A Reset Email has already been Sent.", 1);
            if($status === Password::INVALID_USER)  throw new \Exception("Invalid Email ID/User", 1);
            if($status === Password::RESET_LINK_SENT):
                return response()->json([
                    'status'  => true,
                    'code' => 'PASSWORD_RESET_MAIL_SUCCESS',
                    'message' => '',
                ]);
            endif;
        }
        catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'code' => 'PASSWORD_RESET_MAIL_FAILED',
                'message' => $e->validator->errors()->first(),
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'code' => 'PASSWORD_RESET_MAIL_FAILEDd',
                'message' => $e->getMessage(),
            ]);
        }

    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
            ]);

            try {

                $status = Password::reset(
                    $request->only('email', 'password', 'password_confirmation', 'token'),
                    function ($user, $password) use ($request) {
                        $user->forceFill([
                            'password' => Hash::make($password)
                        ]);
                        $user->save();
                        event(new PasswordReset($user));
                    }
                );
                if($status === Password::INVALID_TOKEN) throw new \Exception("Invalid Token", 1);
                if($status === Password::INVALID_USER)  throw new \Exception("Invalid Email Id", 1);
                if($status === Password::PASSWORD_RESET):
                    return response()->json([
                        'status'  => true,
                        'code' => 'PASSWORD_RESET_SUCCESS',
                        'message' => '',
                    ]);
                endif;
            }
            catch (ValidationException $e) {
                return response()->json([
                    'status'  => false,
                    'code' => 'PASSWORD_RESET_MAIL_FAILED',
                    'message' => $e->validator->errors()->first(),
                ]);
            }
            catch (\Exception $e) {
                return response()->json([
                    'status'  => false,
                    'code' => 'PASSWORD_RESET_FAILED',
                    'message' => $e->getMessage(),
                ]);
            }
    }
    public function register(RegisterRequest $request)
    {
        try {

            $user = User::create([
                "first_name"  => $request->first_name,
                "middle_name" => $request->middle_name ?? null,
                "last_name"   => $request->last_name,
                "email"       => $request->email,
                "type"        => $request->type,
                "active"      => filter_var($request->active, FILTER_VALIDATE_BOOLEAN),
                "phone"       => $request->phone,
                "password"    => Hash::make($request->password),
            ]);
            // To do: On Creation Sent Verification Email // Mark as Event
            return response()->json(['status'=> true,'code' => 'USER_CREATED','message' => '']);
        }
        catch (\Exception $ex) {
            return response()->json(['status'=>false,'code' => 'USER_CREATION_FAILED','message' => $ex->getMessage()]);
        }
    }
    public function user()
    {
        $user = auth()->user();
        if($user->type == 'hospital_staff'){
            $user->load('staff','staff.hospital','staff.units');
        }
        return response()->json(['status'=>true,'code' => 'SUCCESS','message' => '','data'=>$user]);
    }

    public function userActivity(Request $request)
    {

        $filter = $request->get('filter_by','total');

        $data = DB::table('user_login_log')
            ->select('date', DB::raw('count(*) as count'))
            ->groupBy('date');

        if ($filter == 'total')
            $data = $data->get();
        if ($filter == 'date'){
            $from = $request->get('from');
            $to = $request->get('to');
            $data = $data->whereDate('date','>=',$from)->whereDate('date','<=',$to)->get();
        }
        if ($filter == '30-days'){
            $today = Carbon::now();
            $to = $today->toDateString();
            $from = $today->subDays(30)->toDateString();
            $data = $data->whereDate('date','>=',$from)->whereDate('date','<=',$to)->get();
        }
        if ($filter == '7-days'){
            $today = Carbon::now();
            $to = $today->toDateString();
            $from = $today->subDays(7)->toDateString();
            $data = $data->whereDate('date','>=',$from)->whereDate('date','<=',$to)->get();
        }
        if ($filter == 'year'){
            $year = Carbon::now()->year - 1;
            $data = $data->whereYear('date',$year)->get();
        }

        return response()->json(['status'=>true,'code' => 'SUCCESS','message' => '','data'=>$data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(LoginUpdateRequest $request)
    {
        // return $request;
        $user = auth()->user();
        // password,password_confirmation,current_password

        DB::beginTransaction();
        try {
            $user->update([
                "first_name"  => $request->has('first_name') ? $request->first_name : $user->first_name,
                "last_name"   => $request->has('last_name') ? $request->last_name : $user->last_name,
                "email"       => $request->has('email') ? $request->email : $user->email,

            ]);
            if($request->has('password')){
                $user->update([
                    "password"    => Hash::make($request->password)
                ]);
            }
            DB::commit();
            return response()->json(['status'=> true,'code' => 'USER_UPDATED','message' => '','data'=>$user]);
        }
        catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['status'=>false,'code' => 'USER_UPDATION_FAILED','message' => $ex->getMessage()]);
        }
    }
}

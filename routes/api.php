<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


/**
 * Version 1
 */
Route::prefix('v1')->group(function(){

    /** Code By Sethu */

    Route::middleware('auth:api')->get('/hospital/list','App\Http\Controllers\API\HospitalController@hospitals');
    Route::middleware('auth:api')->get('/hospital/{hospital_id}/staff/list','App\Http\Controllers\API\HospitalController@staffs');
    Route::middleware('auth:api')->get('/hospital/{hospital_id}/patient/list','App\Http\Controllers\API\HospitalController@patients');
    Route::middleware('auth:api')->get('/hospital/admin/list','App\Http\Controllers\API\HospitalController@admins');
    Route::middleware('auth:api')->get('/hospital/{hospital_id}/floor/map','App\Http\Controllers\API\FloorController@load');
    Route::middleware('auth:api')->post('/hospital/{hospital_id}/floor/map','App\Http\Controllers\API\FloorController@save');
    Route::middleware('auth:api')->get('/hospital/{hospital_id}/unit/{unit_id}/rooms','App\Http\Controllers\API\RoomController@rooms');
    Route::middleware('auth:api')->get('/hospital/{hospital_id}/staff/role','App\Http\Controllers\API\StaffController@staffRoles');
    Route::middleware('auth:api')->get('/hospital/{hospital_id}/unit/{unit_id}/patient','App\Http\Controllers\API\PatientController@unitPatientCount');
    Route::middleware('auth:api')->get('/hospital/{hospital_id}/unit/{unit_id}/workload','App\Http\Controllers\UnitController@workload');
    Route::middleware('auth:api')->get('/hospital/{hospital_id}/unit/overview','App\Http\Controllers\API\HospitalController@unitOverview');
    Route::middleware('auth:api')->post('/hospital/{hospital_id}/staff/notification/preference','App\Http\Controllers\API\StaffController@saveNotificationPreference');
    Route::middleware('auth:api')->get('/hospital/{hospital_id}/staff/{staff_id}/notification/preference','App\Http\Controllers\API\StaffController@loadNotificationPreference');
    Route::middleware('auth:api')->post('/hospital/{hospital_id}/patient/update/designation','App\Http\Controllers\API\PatientController@updateDesignation');

    Route::middleware('auth:api')->get('/dashboard','App\Http\Controllers\API\DashboardController@dashboard');

    Route::middleware('auth:api')->post('/hospital/{hospital_id}/staff/deactivate','App\Http\Controllers\API\StaffController@deactivate');
    Route::middleware('auth:api')->post('/hospital/{hospital_id}/staff/activate','App\Http\Controllers\API\StaffController@activate');
    Route::middleware('auth:api')->get('/hospital/{hospital_id}/search/staff','App\Http\Controllers\API\StaffController@search');

    Route::middleware('auth:api')->post('/hospital/{hospital_id}/patient/discharge','App\Http\Controllers\API\RoomController@discharge');
    Route::middleware('auth:api')->get('/hospital/{hospital_id}/available/room','App\Http\Controllers\API\RoomController@availableRooms');
    Route::middleware('auth:api')->post('/hospital/{hospital_id}/patient/transfer','App\Http\Controllers\API\RoomController@transfer');

    Route::middleware('auth:api')->get('/hospital/{hospital_id}/patient/{patient_id}/workload','App\Http\Controllers\API\PatientController@workLoadList');
    Route::middleware('auth:api')->post('/hospital/{hospital_id}/patient/workload','App\Http\Controllers\API\PatientController@saveWorkLoad');

    Route::post('/hospital/{hospital_id}/grid','App\Http\Controllers\API\StaffController@grid');
    Route::post('/hospital/{hospital_id}/assignment','App\Http\Controllers\API\StaffController@assignment');
    Route::post('/hospital/{hospital_id}/census','App\Http\Controllers\API\StaffController@census');
    Route::post('/hospital/{hospital_id}/map','App\Http\Controllers\API\StaffController@map');






    Route::middleware('auth:api')->get('/hospital/{hospital}/activity',"App\Http\Controllers\HospitalController@activity");
    Route::get('/hospital/{hospital}/export',"App\Http\Controllers\ApiController@index");
    Route::post('/login',"App\Http\Controllers\AuthController@login");
    Route::post('/forgot-password',"App\Http\Controllers\AuthController@forgotPassword")->name('password.forgot');
    Route::post('/reset-password',"App\Http\Controllers\AuthController@resetPassword")->name('password.reset');

    Route::post('/register',"App\Http\Controllers\AuthController@register");

    Route::middleware('auth:api')->post('/logout',"App\Http\Controllers\AuthController@logout");
    Route::middleware('auth:api')->get('/user',"App\Http\Controllers\AuthController@user");
    Route::middleware('auth:api')->get('/user/activity',"App\Http\Controllers\AuthController@userActivity");
    Route::middleware('auth:api')->put('/user/update',"App\Http\Controllers\AuthController@update");
    Route::middleware('auth:api')->resource('/admin',"App\Http\Controllers\UserController");
    Route::middleware('auth:api')->resource('/hospital',"App\Http\Controllers\HospitalController");
    Route::middleware('auth:api')->resource('/hospital/{hospital}/unit',"App\Http\Controllers\UnitController");
    Route::middleware('auth:api')->resource('/hospital/{hospital}/staff',"App\Http\Controllers\StaffController");
    Route::middleware('auth:api')->get('/customer-support-request',"App\Http\Controllers\CustomerSupportRequestController@csrequestlist");
    Route::middleware('auth:api')->resource('/hospital/{hospital}/customer-support-request',"App\Http\Controllers\CustomerSupportRequestController");
    Route::middleware('auth:api')->put('/hospital/{hospital}/staff/{staff}/suspend',"App\Http\Controllers\StaffController@suspend");
    //Route::middleware('auth:api')->get('/customer-support-request',"App\Http\Controllers\StaffController@csrequestindex");
    Route::middleware('auth:api')->post('/customer-support-request',"App\Http\Controllers\StaffController@csrequeststore");
    Route::middleware('auth:api')->resource('/hospital/{hospital}/patient',"App\Http\Controllers\PatientController");
    Route::middleware('auth:api')->get('/options',"App\Http\Controllers\OptionController@index");
    Route::middleware('auth:api')->put('/options',"App\Http\Controllers\OptionController@update");
    Route::middleware('auth:api')->resource('/hospital/{hospital}/unit/{unit}/room',"App\Http\Controllers\RoomController");
    Route::middleware('auth:api')->resource('/unit/{unit}/room/{room}/delegate',"App\Http\Controllers\DelegateController");
    /** Notifications Channels for Staff */
    Route::middleware('auth:api')->get('/notifications-channels',"App\Http\Controllers\StaffController@notificationChannels");
    Route::middleware('auth:api')->put('/notifications-channels',"App\Http\Controllers\StaffController@notificationChannel");

    /** Only For Developers Use */
    Route::resource('/developers/system-notifications',"App\Http\Controllers\SystemNotificationController");


    Route::get('/mail',function (){
        try {
           dd(\Illuminate\Support\Facades\Mail::to('sethuksethu@gmail.com')->send(new \App\Mail\StaffDeactivated()));
        }
       catch (Exception $e){
            dd($e);
       }
    });





});

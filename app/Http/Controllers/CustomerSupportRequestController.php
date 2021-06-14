<?php

namespace App\Http\Controllers;

use DB;
use App\Models\CustomerSupportRequest;
use App\Models\Hospital;
use App\Models\Staff;
use App\Http\Requests\CsrUpdateRequest;
use App\Http\Requests\CsrRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;


class CustomerSupportRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Hospital $hospital,CsrRequest $request)
    {

        $count = $request->has('count') ? $request->count: 10;
        $sort_by = $request->has('sortby') ? $request->sortby :'created_at';
        $sort_order = $request->has('sortorder') ? $request->sortorder :'asc';
        $customer_support_requests = $hospital->cs_requests;
        $query      = $hospital->cs_requests();
        $customer_support_requests  = $query->orderBy($sort_by,$sort_order)->paginate($count);


        return response()->json(['status'=>true,'code' => 'SUCCESS','message' => '','data'=>$customer_support_requests]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store( Staff $staff)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function show(Hospital $hospital,CustomerSupportRequest  $customerSupportRequest)
    {
        $customerSupportRequest->load('staff');
        return response()->json(['status'=> true,'code' => 'SUCCESS','message' => '','data'=>$customerSupportRequest]);
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
     * @param  \App\Http\Requests\CsrUpdateRequest  $request
     * @param  \App\Models\Hospital  $hospital
    * @param  \App\Models\CustomerSupportRequest  $customerSupportRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Hospital $hospital,CustomerSupportRequest  $customerSupportRequest)
    {

        DB::beginTransaction();
        $user   =   auth()->user();
        try {
            $customerSupportRequest->update([

                "status" => 'Resolved',
                "resolved_by" => $user->id
            ]);
            DB::commit();

            $customerSupportRequest->load('staff');
            return response()->json(['status'=> true,'code' => 'REQUEST_UPDATED','message' => '','data'=>$customerSupportRequest]);
        }
        catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['status'=>false,'code' => 'REQUEST_UPDATION_FAILED','message' => $ex->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomerSupportRequest  $customerSupportRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(Hospital $hospital,CustomerSupportRequest  $customerSupportRequest)
    {
        DB::beginTransaction();
        try {
            $customerSupportRequest->delete();

            DB::commit();
            return response()->json(['status'=> true,'code' => 'CUSTOMERSUPPORTREQUEST_DELETED','message' => '']);
        }
        catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['status'=>false,'code' => 'CUSTOMERSUPPORTREQUEST_DELETION_FAILED','message' => $ex->getMessage()]);
        }
    }

    public function csrequestlist()
    {

        $query = CustomerSupportRequest::whereIn('status',['Inprogress']);
        $query->has('staff')->with('staff');


        $customer_requests = $query->get();
       return response()->json(['status'=>true,'code' => 'SUCCESS','message' => '','data'=>$customer_requests]);
    }



}

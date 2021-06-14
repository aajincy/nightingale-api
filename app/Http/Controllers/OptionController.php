<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class OptionController extends Controller
{
    public function index()
    {
        $data = DB::table('options')->select('value','option','title')->get();
        return response()->json(['status'=>true,'code' => 'SUCCESS','data' => $data]);
    }
    public function update(Request $request)
    {
       $data = $request->all();
       foreach ($data as $key => $value) {
           if($value){
               $query = DB::table('options')->where('option',$key);
               $d1    = $query->first();
               if($d1){
                    $query->update([
                        "value" => $value
                    ]);
                }
            }
       }
        $d2 = DB::table('options')->select('value','option','title')->get();
        return response()->json(['status'=>true,'code' => 'SUCCESS','data' => $d2]);
    }
}

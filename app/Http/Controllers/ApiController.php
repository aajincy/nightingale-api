<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hospital;
use App\Models\Staff;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;

class ApiController extends Controller
{
    public function index(Hospital $hospital,Request $request)
    {
        $relationType   = $request->type ?? "staff";
        $fields         = $request->fields ?? "user.first_name";
        $ex_fields      = explode(",",$fields);

        try {
            $headers = ['Content-Type' => 'text/csv','Content-Disposition' => 'attachment; filename="tweets.csv"',];
            $csv = Writer::createFromStream(fopen('php://temp', 'r+'));
            $data = [];

            $csv->insertOne($ex_fields);
            foreach ($hospital->staff as $key) {
                $item = [];
                $staff_table_fields = [
                    "first_name",
                    "last_name",
                    "email",
                    "phone",
                    "certifications",
                    "title",
                    "experience",
                    "credentials",
                    "start_date"
                ];
                $hospital_table_fields = [
                    "hospital_name" => "name",
                ];
                foreach ($ex_fields as $ls) {
                    if(in_array($ls,$staff_table_fields)):
                        $k  =  $key->{$ls};
                    elseif(in_array($ls,array_keys($hospital_table_fields))):
                        $k  =  $key->hospital->{$hospital_table_fields[$ls]};
                    endif;
                    $item[$ls] = $k;
                }
                $data[] = $item;
            }
            $csv->insertAll($data);
            $filename    =   now()->format("d_m_Y")."_staff.csv";
            $csv->output($filename);
        } catch (Exception | RuntimeException $e) {
            return response()->json(['status'=>false,'code' => 'FAILED','message' => $e->getMessage()],422);
        }
        
    }
}

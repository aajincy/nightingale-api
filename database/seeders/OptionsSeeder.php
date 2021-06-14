<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
class OptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = 
        [
            [
                "option" => "default_colors",
                "value" => "#0B56A3,#199FC8,#327FBF",
                "title" => "Default System Colors",
            ],
            [
                "option" => "system_version",
                "value" => "0.21",
                "title" => "Application Version",
            ],

            [
                "option" => "staff_title",
                "value" => "Nurse,Aide",
                "title" => "Staff Title",
            ],
            [
                "option" => "staff_shift",
                "value" => "7a-7p,7p-7a",
                "title" => "Staff Shift",
            ],
            [
                "option" => "reason_for_contact",
                "value" => "Miscellaneous Feedback, Technical Support, Other",
                "title" => "Reason for Contact",
            ],
            [
                "option" => "dashboard_graph_list",
                "value" => "Last 7 Days,Last 30 Days,Last year,Lifetime",
                "title" => "Dashboard Graph List",
            ],
        ];
        DB::table('options')->insert($data);
    }
}

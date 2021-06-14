<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

class StaffSuspend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'staff:suspend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cron Job for Staff Suspend';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::now()->toDateString();
        $staffs = Staff::where('suspended_till',$today)->get();
        foreach ($staffs as $staff){
            $staff->delete();
        }
    }
}

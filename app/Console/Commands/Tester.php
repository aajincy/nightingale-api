<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Tester extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tester';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $user = \App\Models\User::where('email',"neethap90@gmail.com")->first();
        $password = "XXXXXXX"; // $request->password;
        $notification = $user->notify(new \App\Notifications\UserCreationNotification($user,$password));
        $this->info($notification);
    }
}

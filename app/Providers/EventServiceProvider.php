<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

use App\Models\Staff;
use App\Observers\StaffObserver;
use App\Models\Hospital;
use App\Observers\HospitalObserver;
use App\Models\Unit;
use App\Observers\UnitObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Staff::observe(StaffObserver::class);
        Hospital::observe(HospitalObserver::class);
        Unit::observe(UnitObserver::class);
    }
}

<?php

namespace App\Providers;


use App\Http\Controllers\UserController;
use Auth;
use App\Models\User;
use App\Models\AuthToken;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use Illuminate\Http\Request;
use Illuminate\Auth\Notifications\ResetPassword;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return "http://ec2-3-142-32-39.us-east-2.compute.amazonaws.com/resetPassword?token=$token&email=$user->email";
        });
        Auth::viaRequest('nightingale', function (Request $request) {
            $auth   = $request->bearerToken();
            $token  = AuthToken::where('access_token',$auth)->whereNull('logout_at')->first();
            if($token){
                $token->update(['last_active_on' => now()]);
                UserController::updateUserActivity($token->user_id);
                return $token->user;
            }
        });
    }
}

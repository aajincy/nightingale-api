<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\UserCreationNotification;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Triats\UserTriat;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, UserTriat, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'type',
        'email',
        'phone',
        'password',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'active'=>'boolean'
    ];
    public function tokens()
    {
        return $this->hasMany(AuthToken::class);
    }
    public function makeToken()
    {
        $token          = generateToken($this->email);
        $refresh_token  = generateToken($this->email);
        return $this->tokens()->create([
            'access_token' => $token,
            'refresh_token' => $refresh_token,
            'location' => null,
            'login_at' => now(),
            'last_active_on' => now(),
        ]);
    }
    public function token($token)
    {
        return $this->tokens()->where('access_token', $token)->first();
    }
    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

//    public function hospitaluser()
//    {
//        return $this->hasOneThrough
//    }

    public function routeNotificationForTwilio()
    {
        return $this->phone;
    }

    public function PasswordResetNotification($token)
    {

       $this->notify(new UserCreationNotification($token));
   }
}

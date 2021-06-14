<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class AuthToken extends Model
{
    protected $fillable = [
        'user_id','access_token','refresh_token','location','login_at','last_active_on','logout_at'
    ];
    protected $dates = [
        'login_at','last_active_on','logout_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logout()
    {
        $this->update([
            'logout_at' => now(),
        ]);
        return true;
    }
}

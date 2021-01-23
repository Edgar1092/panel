<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'phone','is_active','is_admin','uuid','is_promotor'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function screens()
    {
        return $this->hasMany('App\Screen');
    }
    
    public function contents()
    {
        return $this->hasMany('App\Content');
    }
    
    public function playlists()
    {
        return $this->hasMany('App\Playlist');
    }
    
    public function schedules()
    {
        return $this->hasMany('App\ScheduleUser');
    }

    public function userScreens()
    {
        return $this->hasMany('App\UserScreens');
    }
}

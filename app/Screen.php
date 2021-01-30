<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Screen extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'name', 'serial', 'lat', 'lng', 'brand', 'manufacturer', 'os', 'version', 'offline', 'sync_at','user_id'
    ];

    protected $hidden = [
        'user_id',
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function getCreatedAtAttribute($date)
    {
        // return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y');
    }

    public function setCreatedAtAttribute($date)
    {
        $this->attributes['created_at'] = Carbon::parse($date);
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
    public function playlists()
    {
        return $this->hasMany('App\Playlist');
    }
    
    public function schedules()
    {
        return $this->hasMany('App\SchedulePlaylist');
    }

    public function userScreens()
    {
        return $this->hasMany('App\UserScreens');
    }
}

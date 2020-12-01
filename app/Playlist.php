<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    protected $fillable = [
        'name', 'interval'
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    /* protected $hidden = [
        'id',
    ]; */

    /* public function screen()
    {
        return $this->belongsTo('App\Screen');
    } */

    protected $appends = [
        'content',
      
    ];

    public function getCreatedAtAttribute($date)
    {
        // return Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y');
    }

    public function setCreatedAtAttribute($date)
    {
        $this->attributes['created_at'] = Carbon::parse($date);
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
    public function schedules()
    {
        return $this->hasMany('App\SchedulePlaylist');
    }

    public function content()
    {
        return $this->hasMany('App\Content','playlist_id');
    }

    public function getcontentAttribute()
    {
        $quotation = $this->playlistContent()->leftjoin('PlaylistContent','playlist_contents.content_id','=','contents.id')->get();

        if(!empty($quotation))
        {
            return $quotation;
        }else{
            return '';
        }

    }
    
    public function playlistContent()
    {
        return $this->hasMany('App\PlaylistContent', 'playlist_id');
    }
}
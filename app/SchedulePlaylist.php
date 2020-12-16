<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchedulePlaylist extends Model
{
    protected $fillable = [
        'schedule_id', 'playlist_id', 'screen_id', 'fulltime','locked'
    ];

    protected $hidden = [
    ];

    public function schedule()
    {
        return $this->belongsTo('App\Schedule');
    }

    public function playlist()
    {
        return $this->belongsTo('App\Playlist');
    }

    public function screen()
    {
        return $this->belongsTo('App\Screen');
    }
}
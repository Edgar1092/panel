<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlaylistContent extends Model
{
    protected $fillable = [
        'type', 'content_id', 'start_at', 'end_at'
    ];

    protected $hidden = [
        'playlist_id',
    ];

    /* public function screen()
    {
        return $this->belongsTo('App\Screen');
    } */

    public function playlist()
    {
        return $this->belongsTo('App\Playlist');
    }

    public function content()
    {
        return $this->belongsTo('App\Content');
    }
}
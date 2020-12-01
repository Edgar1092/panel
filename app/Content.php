<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = [
        'name', 'type', 'mime', 'size', 'offline'
    ];

    protected $hidden = [
        'user_id',
    ];

    public function getTypeAttribute()
    {
        if (strstr($this->mime, "video/")) return "video";
        //if (strstr($this->mime, "image/")) return "image";
        return "image";
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
    public function slideshows()
    {
        return $this->hasMany('App\Slideshow');
    }
}

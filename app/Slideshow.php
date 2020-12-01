<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Slideshow extends Model
{
    protected $fillable = [
        'name', 'slide_id',
    ];

    protected $hidden = [
        'content_id',
    ];

    public function content()
    {
        return $this->belongsTo('App\Content');
    }
}

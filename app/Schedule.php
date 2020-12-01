<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'init_at', 'ends_at'
    ];

    protected $hidden = [
    ];
    
    public function users()
    {
        return $this->hasMany('App\ScheduleUser');
    }
    
    /* public function getInitAtAttribute($value)
    {
        return strtotime($value);
    }

    public function getEndsAtAttribute($value)
    {
        return strtotime($value);
    } */
}

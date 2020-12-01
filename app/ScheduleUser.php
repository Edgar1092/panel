<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduleUser extends Model
{
    protected $fillable = [
        'schedule_id', 'user_id'
    ];

    protected $hidden = [
    ];

    public function schedule()
    {
        return $this->belongsTo('App\Schedule');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
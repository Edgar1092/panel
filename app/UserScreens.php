<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserScreens extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'id', 'user_id', 'screen_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function screen()
    {
        return $this->belongsTo('App\Screen');
    }
}

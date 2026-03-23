<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $fillable = [
        'user_id',
        'activity_id',
        'status'
    ];
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }   
    public function activity()
    {
        return $this->belongsTo(\App\Models\Activity::class);
    }
}

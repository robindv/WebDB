<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    function group()
    {
        return $this->belongsTo('App\Models\Group');
    }
}
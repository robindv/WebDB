<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerUser extends Model
{
    function server()
    {
        return $this->belongsTo('App\Models\Server');
    }

    function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
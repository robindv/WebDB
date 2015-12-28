<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ServerDomain extends Model
{
    function server()
    {
        return $this->belongsTo('App\Models\Server');
    }
}
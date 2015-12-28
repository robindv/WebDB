<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    function students()
    {
        return $this->hasMany('App\Models\Student');
    }

    function project()
    {
        return $this->belongsTo('App\Models\Project');
    }

    function assistant()
    {
        return $this->belongsTo('App\Models\User');
    }

    function server()
    {
        return $this->hasOne('App\Models\Server');
    }
}
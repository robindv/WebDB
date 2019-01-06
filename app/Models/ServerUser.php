<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerUser extends Model
{
    public static $states = ['In wachtrij', 'Aangemaakt', 'In wachtrij voor verwijderen', "Verwijderd"];
    public $appends = ['state'];

    function getStateAttribute()
    {
        return self::$states[$this->created];
    }

    function server()
    {
        return $this->belongsTo('App\Models\Server');
    }

    function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
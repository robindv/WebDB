<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ServerTask extends Model
{
    public static $actions = ['create' => 'VM aanmaken',
                              'configure' => 'Configureren',
                              'ssl' => 'SSL certificaat aanvragen'];
    public static $states = ['In wachtrij', 'Voltooid', 'Mislukt'];

    function server()
    {
        return $this->belongsTo('App\Models\Server');
    }
}
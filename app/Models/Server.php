<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    function group()
    {
        return $this->belongsTo('App\Models\Group');
    }

    function domains()
    {
        return $this->hasMany('App\Models\ServerDomain');
    }

    function users()
    {
        return $this->hasMany('App\Models\ServerUser');
    }


    function refresh()
    {
        try
        {
            $states = json_decode(file_get_contents('http://'.env("WEBDB_API").'/vm/'.$this->name));
            $this->state = $states->State;
            $this->uptime = $states->UpTime;
            $this->memory = $states->MemoryAssigned;

        }
        catch(\Exception $exception)
        {
            $this->state = '?';
            $this->uptime = '?';
            $this->memory = 0;
        }
        $this->save();
    }

    function start()
    {
        /* Refresh state */
        $this->refresh();

        /* Only start when not running at this moment. */
        if($this->state != 'Running')
        {
            $ch = curl_init(env("WEBDB_API")."/start/".$this->name);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query([]));
            $response = curl_exec($ch);
        }
    }

    function stop()
    {
        /* Refresh state */
        $this->refresh();

        /* Only start when not running at this moment. */
        if($this->state != 'Off')
        {
            $ch = curl_init(env("WEBDB_API")."/stop/".$this->name);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query(array()));
            $response = curl_exec($ch);
        }
    }
}
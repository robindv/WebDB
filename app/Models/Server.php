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
        if($this->cloudstack_id == null)
            return;
        $connector = new \App\Connectors\CloudStackConnector();
        $vm = $connector->get_virtual_machine_info($this->cloudstack_id);
        if($vm == null)
            return;

        $this->name = $vm->name;
        $this->state = $vm->state;
        $this->uptime = '?';
        $this->memory = $vm->memory;
        $this->ip_address = $vm->nic[0]->ipaddress;
        $this->save();
    }

    function getHostnameAttribute()
    {
        $ip = explode(".", $this->ip_address);
        if(count($ip) != 4)
            return "";

        return str_replace("XXX", $ip[3] ,env('WEBDB_VM_DOMAINS'));
    }

    function start()
    {
        if($this->cloudstack_id == null)
            return;
        $connector = new \App\Connectors\CloudStackConnector();
        $connector->start_virtual_machine($this->cloudstack_id);
        sleep(3);
    }

    function stop()
    {
        if($this->cloudstack_id == null)
            return;
        $connector = new \App\Connectors\CloudStackConnector();
        $connector->stop_virtual_machine($this->cloudstack_id);
        sleep(3);
    }
}

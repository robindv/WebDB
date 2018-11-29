<?php

namespace App\Models;


use Carbon\Carbon;

class Server extends \Eloquent
{
    public $dates = ['ssl_valid_from', 'ssl_valid_to'];

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

    function tasks()
    {
        return $this->hasMany('App\Models\ServerTask');
    }

    function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    function course()
    {
        return $this->belongsTo(Course::class);
    }

    function refresh()
    {
        if($this->cloudstack_id == null)
            return;

        $connector = new \App\Connectors\CloudStackConnector();
        $vm = $connector->get_virtual_machine_info($this->cloudstack_id);

        if($vm == null)
        {
            $this->state = '?';
            $this->memory = '?';
            $this->ip_address = null;
        }
        else
        {
            $this->name = $vm->name;
            $this->state = $vm->state;
            $this->memory = $vm->memory;
            $this->ip_address = $vm->nic[0]->ipaddress;
        }

        $this->save();
    }

    function getHostnameAttribute()
    {
        if($this->provider->type == 'openstack')
            return $this->name . ".". $this->provider->hostname_suffix;

        $c = Course::where('examples_site_ip',$this->ip_address)->first();
        if($c != null)
            return $c->examples_site;


        if($this->ip_address == env("WEBDB_EXAMPLES_IP"))
            return env('WEBDB_EXAMPLES');

        $ip = explode(".", $this->ip_address);
        if(count($ip) != 4)
            return "";

        return str_replace("XXX", sprintf("%03d",$ip[3]), env('WEBDB_VM_DOMAINS'));
    }

    function getCreatedAttribute()
    {
        return $this->cloudstack_id != null || $this->provider->type == 'openstack';
    }

    function deploy()
    {
        if($this->cloudstack_id != null)
            return;

        $connector = new \App\Connectors\CloudStackConnector();

        $zoneid    = Configuration::where('name','cloudstack_zoneid')->first()->value;
        $networkid = Configuration::where('name','cloudstack_networkid')->first()->value;
        $serviceofferingid = Configuration::where('name','cloudstack_serviceofferingid')->first()->value;
        $templateid = Configuration::where('name','cloudstack_templateid')->first()->value;

        $response = $connector->deploy_virtual_machine($this->name, $serviceofferingid, $templateid, $zoneid, $networkid)->deployvirtualmachineresponse;

        if(isset($response->errorcode))
            return $response->errortext;

        $this->cloudstack_id = $response->id;
        $this->save();
        return true;

    }

    function destroy_server()
    {
        if($this->cloudstack_id == null)
            return;
        $connector = new \App\Connectors\CloudStackConnector();
        $connector->destroy_virtual_machine($this->cloudstack_id);
        $this->cloudstack_id = null;
        $this->save();
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

    function refresh_ssl_info()
    {
        try{
            $get = stream_context_create(array("ssl" => array("capture_peer_cert" => TRUE)));
            $read = stream_socket_client("ssl://".$this->hostname.":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get);
            $cert = stream_context_get_params($read);
            $certinfo = openssl_x509_parse($cert['options']['ssl']['peer_certificate']);

            $this->ssl_issuer = $certinfo['issuer']['CN'];
            $this->ssl_valid_from = Carbon::createFromTimestampUTC($certinfo['validFrom_time_t'])->timezone('Europe/Amsterdam');
            $this->ssl_valid_to = Carbon::createFromTimestampUTC($certinfo['validTo_time_t'])->timezone('Europe/Amsterdam');
            $this->save();
            return;
        }
        catch(\ErrorException $e)
        {
            $this->ssl_issuer = null;
            $this->ssl_valid_from = null;
            $this->ssl_valid_to = null;
            $this->save();
            return;
        }

    }
}

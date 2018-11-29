<?php

namespace App\Http\Controllers;

use App\Connectors\CloudStackConnector;
use App\Models\Server;
use App\Models\ServerDomain;
use App\Models\Configuration;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    private $connector;

    function __construct()
    {
        $this->connector = new CloudStackConnector();
    }

    function getServers()
    {
        $data['servers'] = Server::orderBy('name')->with('group')->where('course_id', \Auth::user()->current_course)->get();


        return view('layout')->nest('page','admin.servers',$data);
    }

    function getServer(Server $server)
    {
        $server->refresh();

        return view('layout')->nest('page','admin.server',['server' => $server]);
    }

    function getServerOn(Server $server)
    {
        $server->start();
        $server->refresh();

        return redirect()->back();
    }

    function getServerOff(Server $server)
    {
        $server->stop();

        return redirect()->back();
    }

    function getConfig()
    {
        $data['zones'] = [];
        $data['networks'] = [];
        $data['serviceofferings'] = [];
        $data['templates'] = [];
        foreach($this->connector->list_zones()->listzonesresponse->zone as $zone)
            $data['zones'][$zone->id] = $zone->name;
        foreach($this->connector->list_templates("executable")->listtemplatesresponse->template as $template)
            $data['templates'][$template->id] = $template->name." (".$template->displaytext.", ".$template->ostypename.")";
        foreach($this->connector->list_networks()->listnetworksresponse->network as $network)
            $data['networks'][$network->id] = $network->name. " (".$network->displaytext.")";
        foreach($this->connector->list_service_offerings()->listserviceofferingsresponse->serviceoffering as $serviceoffering)
            $data['serviceofferings'][$serviceoffering->id] = $serviceoffering->name. " (".$serviceoffering->displaytext.")";

        $data['current_zoneid']    = Configuration::where('name','cloudstack_zoneid')->first()->value;
        $data['current_networkid'] = Configuration::where('name','cloudstack_networkid')->first()->value;
        $data['current_serviceofferingid'] = Configuration::where('name','cloudstack_serviceofferingid')->first()->value;
        $data['current_templateid'] = Configuration::where('name','cloudstack_templateid')->first()->value;

        return view('layout')->nest('page','admin.config',$data);
    }

    function postConfig(Request $request)
    {
        Configuration::where('name','cloudstack_zoneid')->first()->fill(['value' => $request->get('cloudstack_zoneid')])->save();
        Configuration::where('name','cloudstack_networkid')->first()->fill(['value' => $request->get('cloudstack_networkid')])->save();
        Configuration::where('name','cloudstack_serviceofferingid')->first()->fill(['value' => $request->get('cloudstack_serviceofferingid')])->save();
        Configuration::where('name','cloudstack_templateid')->first()->fill(['value' => $request->get('cloudstack_templateid')])->save();


        return redirect()->back();
    }



}

<?php

namespace App\Http\Controllers;

use App\Connectors\CloudStackConnector;
use App\Models\Server;
use App\Models\ServerDomain;
use App\Models\Configuration;
use Illuminate\Http\Request;

class AdminController extends Controller
{

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


}

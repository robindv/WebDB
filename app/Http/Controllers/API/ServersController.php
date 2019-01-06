<?php

namespace App\Http\Controllers\API;


use Illuminate\Http\Request;

class ServersController
{

    function getServers(Request $request)
    {
        if(!$request->user()->is_admin)
            return response( ['error' => "Verboden toegang"], 403);

        return \App\Models\Server::where('course_id', current_course_id())
            ->get()
            ->map(function($server) {
                if($server->group != null)
                    $server->group->setVisible(['name']);

               return $server->setVisible(['id','name','ip_address','hostname','memory','state','group','ssl_issuer','created','configured']);
            });
    }


    function getServer(Request $request, \App\Models\Server $server) {

        if(!$request->user()->is_admin)
            return response( ['error' => "Verboden toegang"], 403);

        $server = $server->load('users')->load('group')
               ->setVisible(['id','name','hostname','ip_address', 'state', 'group','ssl_issuer','ssl_valid_from','ssl_valid_to','users']);

        if($server->group != null)
            $server->group->setVisible(['name']);

        $server->users->map(function($user) {
            $user->user->setVisible(['name','uvanetid']);

            return $user->setVisible(['id','username','password','state','user']);
       });

        return $server;
    }
}
<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    function getUser(Request $request)
    {
        $user = $request->user()->load('student')->setVisible(['id','name','name','firstname','uvanetid','email','is_teacher','is_admin','is_assistant','is_student','student']);

        if($user->student != null)
            $user->student->setVisible(['programme']);

        return $user;
    }

    function getServerUsers(Request $request)
    {
        $user = $request->user();

        return \App\Models\ServerUser::with('server','server.group')
            ->whereHas('server',function($q){ $q->where('configured',1);})
            ->orderBy('server_id')
            ->where('created',1)
            ->where('user_id', $user->id)
            ->get()
            ->map(function($serveruser) {

                if($serveruser->server->group != null)
                    $serveruser->server->group->setVisible(['name']);
                $serveruser->server->setVisible(['hostname','ip_address','group']);

                return $serveruser->setVisible(['username','password','server']);
            });
    }

    function getAssistants(Request $request)
    {
        if(! $request->user()->is_teacher && ! $request->user()->is_assistant)
            return response(['error' => 'Mag niet'], 403);

        return \App\Models\User::isAssistant()->where('course_id',current_course_id())->orderBy('lastname')->get()->map(function ($user) { return $user->makeVisible(['id','name']); });
    }

}
<?php

namespace App\Http\Controllers;

use App\Models\ServerUser;
use Auth;

class HomeController extends Controller
{

    function getIndex()
    {
        return view('layout')->nest('page','pages.home');
    }

    function getVoorbeeldcode()
    {
        return view('layout')->nest('page','pages.voorbeeldcode');
    }

    public function getProfile()
    {
        $user_id = Auth::user()->id;

        $data['user'] = Auth::user();
        $data['passwords'] = ServerUser::with('server')->whereHas('server',function($q){ $q->where('configured',1);})->orderBy('server_id')->where('user_id',$user_id)->get();

        return view('layout')->nest('page','pages.profile',$data);
    }


}
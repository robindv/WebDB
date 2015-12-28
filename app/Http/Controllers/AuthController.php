<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Auth;

class AuthController extends Controller
{
    function getLogin()
    {
        $token = env('IVO_TOKEN');
        $base_url = env('IVO_URL');
        $url = $base_url."/ticket";
        $callback_url = url('callback?ticket={#ticket}');

        $ch = curl_init();
        $postfields = "token=".$token."&callback_url=".urlencode($callback_url);

        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, 2);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$postfields);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result);

        $redirect_url = $base_url."/login/".$response->ticket;

        return redirect($redirect_url);
    }

    function getCallback(Request $request)
    {
        $token = env('IVO_TOKEN');
        $ticket = $request->input('ticket');
        $base_url = env('IVO_URL');
        $url = $base_url."/status";

        $ch = curl_init();
        $postfields = "token=".$token."&ticket=".$ticket;

        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, 2);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$postfields);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result);

        if($response->status == "success")
        {
            $uvanetid =  $response->attributes->{'urn:mace:dir:attribute-def:uid'}[0];
            $user =	User::where('uvanetid',$uvanetid)->first();

            if($user)
                Auth::login($user);
            else
                return redirect('login-failed');

            return redirect('/');

        }
        else
            return redirect('/');
    }

    function getLoginFailed()
    {
        return view('layout')->nest('page','pages.login_failed');
    }

    public function getLogout()
    {
        Auth::logout();
        return redirect(env('IVO_URL')."/logout");
    }
}
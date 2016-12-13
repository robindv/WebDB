<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Auth;

class AuthController extends Controller
{

    function getLogin()
    {
        if(Auth::id())
            return redirect('/');

        $url = env("CAS_URL") . "/login?service=" . url('/callback');

        return redirect($url);
    }

    function getCallback(Request $request)
    {
        if(! $request->get('ticket'))
            return redirect('login-failed');

        $url = env("CAS_URL") . "/serviceValidate?service=".url('/callback')."&ticket=" . $request->get('ticket');
        $xml = new \SimpleXMLElement(file_get_contents($url));
        $res = $xml->xpath('/cas:serviceResponse/cas:authenticationSuccess/cas:user');
        if ($res)
        {
            $user =	User::where('uvanetid',trim($res[0]))->first();

            if($user)
               Auth::login($user);
            else
               return redirect('login-failed');
        }

        return redirect('/');
    }

    function getLoginFailed()
    {
        return view('layout')->nest('page','pages.login_failed');
    }

    public function getLogout()
    {
        Auth::logout();

        return redirect(env('CAS_URL')."/logout");
    }
}

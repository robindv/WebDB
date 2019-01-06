<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    function getIndex(Request $request) {

        $ticket = $request->get('ticket');

        if(!$ticket || $ticket == "undefined")
            return response()->json(['error'=>'invalid request']);


        $url = env("CAS_URL") . "/serviceValidate?service=".url('/login_callback')."&ticket=" . $ticket;

        if(env('APP_ENV') == 'local') {
            $user = User::find(env('APP_DEBUG_USER_ID'));
        }
        else {
            $content = file_get_contents($url);
            $xml = new \SimpleXMLElement($content);
            $res = $xml->xpath('/cas:serviceResponse/cas:authenticationSuccess/cas:user');
            if (!$res)
                return response()->json(['error' => '?']);

            $user = User::where('uvanetid', trim($res[0]))->first();
        }

        if($user) {
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            $token->expires_at = \Carbon\Carbon::now()->addHour(1);
            $token->save();

            return response()->json([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => \Carbon\Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
            ]);
        }

        return response()->json(['error'=>'login-failed']);
    }


}
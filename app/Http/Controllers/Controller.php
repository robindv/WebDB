<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    static function modal($title = '', $view, $data = array(), $submit_text = "Opslaan", $class = "primary")
    {
        if(!Request::ajax())
            return redirect('/');

        return view('modal',array('title' => $title,'submit_text'=>$submit_text,'class'=>$class))->nest('form',$view,$data)->render();
    }
}

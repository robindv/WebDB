<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class SPAController
{
    function getIndex(Request $request)
    {
        return view('index');
    }
}
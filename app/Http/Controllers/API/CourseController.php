<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CourseController extends Controller
{

    function getIndex(Request $request)
    {
        $fields = ['id','name','prefix','examples_site'];
        if($request->user('api') != null) {
            $fields[] = 'admin_email';
        }

        return current_course()->setVisible($fields);
    }

}
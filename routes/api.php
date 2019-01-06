<?php

use Illuminate\Http\Request;



Route::get('/login', function(Request $request) {
    if(env('APP_ENV') == 'local')
        return redirect()->to(url('/login_callback?ticket=debug'));

    return redirect()->to('https://secure.uva.nl/cas/login?service='.url('/login_callback'));
});
Route::get('/course', '\App\Http\Controllers\API\CourseController@getIndex');
Route::get('/auth', '\App\Http\Controllers\API\AuthController@getIndex');


Route::middleware('auth:api')->get('/user', '\App\Http\Controllers\API\UsersController@getUser');
Route::middleware('auth:api')->get('/user/serverusers', '\App\Http\Controllers\API\UsersController@getServerUsers');

Route::middleware('auth:api')->get('/assistants', '\App\Http\Controllers\API\UsersController@getAssistants');

Route::middleware('auth:api')->get('/students', '\App\Http\Controllers\API\StudentsController@getStudents');
Route::middleware('auth:api')->post('/students', '\App\Http\Controllers\API\StudentsController@postStudents');

Route::middleware('auth:api')->get('/servers', '\App\Http\Controllers\API\ServersController@getServers');
Route::middleware('auth:api')->get('/servers/{server}', '\App\Http\Controllers\API\ServersController@getServer');

Route::middleware('auth:api')->get('/groups', '\App\Http\Controllers\API\GroupsController@getGroups');
Route::middleware('auth:api')->post('/groups', '\App\Http\Controllers\API\GroupsController@postGroups');

Route::middleware('auth:api')->get('/group', '\App\Http\Controllers\API\GroupsController@getGroup');
Route::middleware('auth:api')->post('/group/project', '\App\Http\Controllers\API\GroupsController@postProject');

Route::middleware('auth:api')->get('/projects', function(Request $request) {


    $q = \App\Models\Project::where('course_id', current_course_id());

    if($request->user()->is_student)
        $q->where('advanced',0);

    return $q->get()->map(function($project) {
        return $project->setVisible(['id','name']);
    });
});



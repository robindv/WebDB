<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    Route::get('/','HomeController@getIndex');
    Route::get('/voorbeeldcode','HomeController@getVoorbeeldcode');

    Route::get('/profile','HomeController@getProfile')->middleware('auth');

    Route::get('/login','AuthController@getLogin');
    Route::get('/callback','AuthController@getCallback');
    Route::get('/logout','AuthController@getLogout');
    Route::get('/login-failed','AuthController@getLoginFailed');

    Route::get('/apache','BackendController@getApache');

    Route::group(['middleware'=>'staff'], function() {

        Route::get('/staff/groups', 'StaffController@getGroups');
        Route::get('/staff/groups-export/webdb-groepen.csv', 'StaffController@getGroupsExport');


        Route::get('/staff/ports', 'StaffController@getPorts');
        Route::get('/staff/servers', 'StaffController@getServers');
        Route::get('/staff/server/{server}', 'StaffController@getServer');
        Route::get('/staff/server-on/{server}', 'StaffController@getServerOn');
        Route::get('/staff/server-off/{server}', 'StaffController@getServerOff');

        Route::get('/staff/students', 'StaffController@getStudents');
        Route::get('/staff/students-export/webdb-studenten.csv', 'StaffController@getStudentsExport');
        Route::get('/staff/student-modal/{student}','StaffController@getStudentModal');
        Route::post('/staff/student-modal/{student}','StaffController@postStudentModal');

        Route::get('/staff/group-modal/{group}','StaffController@getGroupModal');
        Route::post('/staff/group-modal/{group}','StaffController@postGroupModal');
    });

    Route::group(['middleware'=>'student'], function() {
        Route::get('/student/project','StudentController@getProject');
        Route::post('/student/project','StudentController@postProject');
        Route::get('/student/server','StudentController@getServer');

        Route::get('/student/domain-add-modal','StudentController@getDomainAddModal');
        Route::post('/student/domain-add-modal','StudentController@postDomainAddModal');
        Route::get('/student/domain-delete-modal/{domain}','StudentController@getDomainDeleteModal');
        Route::post('/student/domain-delete-modal/{domain}','StudentController@postDomainDeleteModal');

    });
});

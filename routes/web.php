<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
|
Route::get('/', function () {
    return view('welcome');
});

*/
// Route::get('/testemail', function () {
//       $fromEmail = env('MAIL_FROM_ADDRESS');
//       $fromName  = env('MAIL_FROM_NAME');

//       $firstName = "Irina";
//       $netID = "ivn2";
//       $lastName = "Naydich";
//       $email ="ivn2@cornell.edu";
//       Mail::send('emails.AccountCreated',
//                      array(
//                         'firstName' => $firstName,
//                         'netID' => $netID,
//                         'lastName' => $lastName,
//                         'email' => $email),
//                      function($message) use ($netID, $fromEmail, $fromName, $email){
//                         $message
//                             ->from($fromEmail, $fromName)
//                             ->to($email)
//                             ->subject("Canvas@Cornell User Account");
//                      }
//                 );
//     return '***Hello World';
// });

Route::group(['middleware' => 'userrole'], function () {
    Route::get('/', ['as' => 'index', 'uses' => 'WelcomeController@index']);

    Route::get('createAccount', [
        'as' => 'createAccount',
        'uses' => 'AccountController@account',
    ]);

    Route::post('createAccount', [
        'as' => 'postCreateAccount',
        'uses' => 'AccountController@create',
    ]);

    Route::get('createCourse', [
        'as' => 'createCourse',
        'uses' => 'CourseController@check',
    ]);

    Route::get('copyright', [
        'as' => 'copyright',
        'uses' => 'CourseController@copyright',
    ]);

    Route::post('copyright', [
        'as' => 'copyright',
        'uses' => 'CourseController@copyright',
    ]);

    Route::get('courseInfo', [
        'as' => 'courseInfo',
        'uses' => 'CourseController@info',
    ]);

    Route::post('courseInfo', [
        'as' => 'courseInfoPost',
        'uses' => 'CourseController@courseInfo',
    ]);

    Route::get('confirmation', [
        'as' => 'confirmation',
        'uses' => 'CourseController@confirmation',
    ]);

    Route::get('tester', [
        'as' => 'tester',
        'uses' => 'TesterController@process',
    ]);

    Route::get('badIDRedirect', [
        'as' => 'badIDRedirect',
        'uses' => 'CourseController@badIDRedirect',
    ]);

    Route::get('log', ['as' => 'log', 'uses' => 'LogController@index']);

    Route::get('log/{id}', ['as' => 'showLog', 'uses' => 'LogController@show']);
});

Route::get('ldapError', [
    'as' => 'ldapError',
    function () {
        return view('ldapError');
    },
]);

Route::get('blackboardError', [
    'as' => 'blackboardError',
    function () {
        return view('blackboardError');
    },
]);

Route::get('badCourseID', [
    'as' => 'badCourseID',
    function () {
        return view('badCourseID');
    },
]);

Route::group(['middleware' => 'test'], function () {
    Route::get('debug', ['uses' => 'debugController@testSomething']);
    // Route::get('debug2', ['uses' => 'debugController@testSomething2']);
    // Route::get('debug3', ['uses' => 'debugController@testSomething3']);
    Route::get('ADgroups', ['uses' => 'debugController@testSomething4']);
});

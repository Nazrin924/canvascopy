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

Route::group(["middleware" => "userrole"], function() {
    Route::get('/', ['as' => 'index', 'uses' => 'WelcomeController@index']);

    Route::get('createAccount', [
        'as' => 'createAccount',
        'uses' => 'AccountController@account'
    ]);

    Route::post('createAccount', [
        'as' => 'postCreateAccount',
        'uses' => 'AccountController@create'
    ]);

    Route::get('createCourse', [
        'as' => 'createCourse',
        'uses' => 'CourseController@check'
    ]);

    Route::get('copyright', [
        'as' => 'copyright',
        'uses' => 'CourseController@copyright'
    ]);

    Route::post('copyright', [
        'as' => 'copyright',
        'uses' => 'CourseController@copyright'
    ]);

    Route::get('courseInfo', [
        'as' => 'courseInfo',
        'uses' => 'CourseController@info'
    ]);

    Route::post('courseInfo', [
        'as' => 'courseInfoPost',
        'uses' => 'CourseController@courseInfo'
    ]);

    Route::get('confirmation', [
        'as' => 'confirmation',
        'uses' => 'CourseController@confirmation'
    ]);

    Route::get('tester', [
        'as' => 'tester',
        'uses' => 'TesterController@process'
    ]);

    Route::get('badIDRedirect', [
        'as' => 'badIDRedirect',
        'uses' => 'CourseController@badIDRedirect'
    ]);

    Route::get('log', ['as' => 'log', 'uses' => 'LogController@index']);

    Route::get('log/{id}', ['as' => 'showLog', 'uses' => 'LogController@show']);
});

Route::get('ldapError', [
    'as' => 'ldapError',
    function() {
        return view('ldapError');
    }
]);

Route::get('blackboardError', [
    'as' => 'blackboardError',
    function() {
        return view('blackboardError');
    }
]);

Route::group(["middleware" => "test"], function() {
    Route::get('debug', ['uses' => 'debugController@testSomething']);
    //Route::get('debug2', ['uses' => 'debugController@testSomething2']);
    //Route::get('debug3', ['uses' => 'debugController@testSomething3']);
    Route::get('ADgroups', ['uses' => 'debugController@testSomething4']);
});


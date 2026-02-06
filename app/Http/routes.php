<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\TesterController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\debugController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::middleware('userrole')->group(function () {
    Route::get('/', [WelcomeController::class, 'index'])->name('index');

    Route::get('createAccount', [AccountController::class, 'account'])->name('createAccount');

    Route::post('createAccount', [AccountController::class, 'create'])->name('postCreateAccount');

    Route::get('createCourse', [CourseController::class, 'check'])->name('createCourse');

    Route::get('copyright', [CourseController::class, 'copyright'])->name('copyright');

    Route::post('copyright', [CourseController::class, 'copyright'])->name('copyright');

    Route::get('courseInfo', [CourseController::class, 'info'])->name('courseInfo');

    Route::post('courseInfo', [CourseController::class, 'courseInfo'])->name('courseInfoPost');

    Route::get('confirmation', [CourseController::class, 'confirmation'])->name('confirmation');

    Route::get('tester', [TesterController::class, 'process'])->name('tester');

    Route::get('badIDRedirect', [CourseController::class, 'badIDRedirect'])->name('badIDRedirect');

    Route::get('log', [LogController::class, 'index'])->name('log');

    Route::get('log/{id}', [LogController::class, 'show'])->name('showLog');
});

Route::get('ldapError', function () {
        return view('ldapError');
    })->name('ldapError');

Route::get('blackboardError', function () {
        return view('blackboardError');
    })->name('blackboardError');

Route::middleware('test')->group(function () {
    Route::get('debug', [debugController::class, 'testSomething']);
    // Route::get('debug2', ['uses' => 'debugController@testSomething2']);
    // Route::get('debug3', ['uses' => 'debugController@testSomething3']);
    Route::get('ADgroups', [debugController::class, 'testSomething4']);
});

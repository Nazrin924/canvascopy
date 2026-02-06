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

Route::get('badCourseID', function () {
        return view('badCourseID');
    })->name('badCourseID');

Route::middleware('test')->group(function () {
    Route::get('debug', [debugController::class, 'testSomething']);
    // Route::get('debug2', ['uses' => 'debugController@testSomething2']);
    // Route::get('debug3', ['uses' => 'debugController@testSomething3']);
    Route::get('ADgroups', [debugController::class, 'testSomething4']);
});

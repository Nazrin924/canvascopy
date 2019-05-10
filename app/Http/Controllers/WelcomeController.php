<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Helpers\Testers;

use App\Helpers\BbPhp;

use App\Helpers\Blackboard;


class WelcomeController extends Controller
{

	/*
	|--------------------------------------------------------------------------
	| Welcome Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(){

		//$this->middleware('guest');
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index(Request $request){
		$netID = $request->headers->get(env('CU_REMOTE'));
        $netID = session()->get('netID');
		$fName = session()->get('firstname');
		$lName = session()->get('lastname');
		session()->put('copyrighted', false);
		return view('index', [
			'fName' => $fName,
			'lName' => $lName,
			'netID' => $netID,
			'canCreateSite' => session()->get("canCreateSite"),
			'canCreateUser' => session()->get("canCreateUser"),
			'isDebugger' => session()->get("isDebugger"),
			'testers' => Testers::getTesters()
		]);
	}

}

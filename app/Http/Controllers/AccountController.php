<?php

namespace App\Http\Controllers;

use App\Helpers\CanvasAPI;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Jobs\DoAccountCreation;

use Redirect;
use Validator;
use Queue;

/**
 * Controller for the account creation process
 *
 * AccountController controls each step of the account creation process.
 * This includes displaying the initial window for creating an account,
 * as well as processing the input from that window and queueing the account
 * creation for Web Services.
 *
 * @author Adam Gleisner amg295@cornell.edu
 * @author Jeremy Miller jdm389@cornell.edu
 */
class AccountController extends Controller
{
	/**
	 * A blank constructor for the controller
	 */
	public function __construct() {

	}

	/**
	 * Shows the dialog for creating an account
	 * @param  Request $request The HTTP request
	 * @return \Illuminate\View\View The dialog for account creation
 	 */
	public function account(Request $request) {
		$netID = session()->has('netid') ? session()->get('netid') : $request->headers->get(env('CU_REMOTE'));
		$lName = session()->get('lastname');
		$fName = session()->get('firstname');
		$realm = session()->get('realm');
		$email = $netID;
		if($realm == env('CU_REALM')) {
			$email .= '@cornell.edu';
			$weill = false;
		}
		else {
      if(strpos($netID, '@cumed')) {
        $netID = str_replace("@cumed", "", $netID);
      }
			$email = "$netID@med.cornell.edu";
			$weill = true;
		}
    if($request->session()->has('email') && $request->session()->get('email') != '') {
      $email = $request->session()->get('email');
    }

		if(!session()->get("canCreateUser")) {
			return view("accountError");
    }

		return view('createAccount', [
		    'fName'   => $fName,
		    'lName'   => $lName,
		    'netID'   => $netID,
        'email'   => $email,
		    'success' => false,
        'weill'   => $weill
    	]);
	}

	/**
	 * Queues the account creation process
	 * @param  Request $request The HTTP Request
	 * @return \Illuminate\View\View The view of account creation
	 */
	public function create(Request $request) {
		if($request->has('cancel')) {
			return Redirect::route('index');
		}

    $realm = $request->session()->get('realm');

		$validator = Validator::make($request->all(), [
			'txtNetID'   => ['required', 'min:3', 'max:50'],
			'noChange' => 'min:3|max:50',
		], [
			'txtNetID.required' => 'The NetID field is required.',
			'txtNetID.min'      => 'The NetID must contain more than 3 characters.',
			'txtNetID.max'      => 'The NetID must contain less than 50 characters.',
			'noChange.min'      => 'The NetID must contain more than 3 characters.',
			'noChange.max'      => 'The NetID must contain less than 50 characters.'
		]);

		if($validator->fails()) {
			return Redirect::to('courseInfo')->withInput()->withErrors($validator);
		}

		$netID = $request->has('noChange') ? $request->get('noChange') : $request->get('txtNetID');
    if(session()->get('realm') != env('CU_REALM')) {
      if(!strpos($netID, '@cumed')) {
        $netID .= "@cumed";
      }
    }
    $firstName = $request->get("txtFirstName");
    $lastName = $request->get("txtLastName");
    $email = $request->has('noChangeEmail') ? $request->get('noChangeEmail') : $request->get("txtEmail");

    if(!CanvasAPI::findUser($netID)) {
        try {
        Queue::push(new DoAccountCreation($netID, $firstName, $lastName, $email, $realm));

      } catch(\Exception $e) {
        return view('accountError');
      }
    }
    else {
      return view('accountError');
    }

		session()->put('canCreateUser', false);
		return view('createAccount', [
			'fName' => $firstName,
			'lName' => $lastName,
			'netID' => $netID,
      'email' => $email,
			'success' => true]);
	}

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Testers;

use Redirect;
use Log;

class TesterController extends Controller
{

	public function __construct() {

	}

	public function process(Request $request){
		if(!session()->get('isDebugger'))
			return redirect('/');

		$action = $request->input("action");

		if($action == "add") {
			$testers = Testers::add($request->input("user"));
		}
		elseif ($action == 'remove') {
			$testers = Testers::remove($request->input("user"));
		}
		elseif ($action == 'toggleSite') {
			session()->put("canCreateSite", !session()->get("canCreateSite"));
            session()->put("toggleSite", !session()->get("toggleSite"));
		}
		elseif ($action == 'toggleUser') {
			session()->put("canCreateUser", !session()->get("canCreateUser"));
            session()->put("toggleUser", !session()->get("toggleUser"));
		}
		$maskNetid = $request->input("maskNetid");
        $maskRealm = $request->input("maskRealm");
        if ($maskNetid != "") {
            session()->put("maskedNetid", $maskNetid);
            session()->put("maskedRealm", $maskRealm);
        } else {
            session()->put("maskedNetid", $maskNetid);
            session()->put("maskedRealm", $maskRealm);
        }

		return redirect('/');
	}

}

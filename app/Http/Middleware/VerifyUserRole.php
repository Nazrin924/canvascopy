<?php namespace App\Http\Middleware;

use Closure;

use \App\Helpers\LDAP;

use \App\Helpers\CanvasAPI;

use \App\Helpers\Testers;

use App\User;

use Log;
use Redirect;
/**
 * Verifies the user's abilities within the website
 *
 * VerifyUserRole verifies the user's ability to create an account
 * (based on whether an account under their netID or weillID exists),
 * create a course (based on whether they're an undergraduate student or not),
 * and test the site (based on the helper Testers).
 *
 * @author Adam Gleisner amg295@cornell.edu
 * @author Jeremy Miller jdm389@cornell.edu
 */
class VerifyUserRole
{

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
        $maskedNetid = session()->get("maskedNetid");
        $maskedRealm = session()->get("maskedRealm");
        if($maskedNetid){
            $netID = $maskedNetid;
            $realm = $maskedRealm;
            session()->put("toggleSite", false);
            session()->put("toggleUser", false);
        }
        else{
		    $netID = $request->headers->get('CUWA-REMOTE-USER');
		    $realm = $request->headers->get('CUWA-REALM');
            if ($netID){
                $user = User::where("name", $netID)->first();
                if (!$user) {
                    $user = new User();
                    $user->name = $netID;
                    $user->email = "$netID@cornell.edu";
                    $user->password = "secret";
                    $user->remember_token = "";
                    $user->save();
                }
                $user->updated_at = now();
                $user->update();    // update record to set time last login
               }else{
                    Log::info("error no netid");
               }
        }
        $realm = strtoupper($realm);
        session()->put("realm", $realm);
        session()->put('netID', $netID);
    try {
        $data = LDAP::data($netID, $realm);
      if(!array_filter($data)) {
              session()->put('errorMsg', "No $netID user found in LDAP");
              //$this->checkCreation($netID, $realm);
              session()->put("maskedNetid","");
              session()->put("maskedRealm","");
              $netID = $request->headers->get(env('CU_REMOTE'));
              $realm = $request->headers->get('CUWA-REALM');
              session()->put("realm", $realm);
              session()->put('netID', $netID);
              $this->checkCreation($netID, $realm);
              session()->put("toggleSite", false);
              session()->put("toggleUser", false);

      } elseif($realm == env('CU_REALM')) {
        session()->put('errorMsg', "");
        $this->checkCreation($netID, $realm);
      } else {
          $this->checkCreation($netID, $realm);
        if(!strpos($netID, '@wcmc')) {
          $netID .= "@wcmc";
        }
      }
    } catch(Exception $e) {
      return Redirect::route('ldapError');
    }

		if(!session()->has('isDebugger') and !$maskedNetid) {
			session()->put('isDebugger', Testers::check($netID, $realm));
		}

    try {
        $groups = LDAP::getADGroups($netID ,$realm);
    } catch(Exception $e) {
        return Redirect::route('ldapError');
    }


		$inGroup = in_array(env("AD_GROUP"),$groups);
        $inGroup = $inGroup || ($netID == env('TEST_NETID'));
		session()->put('isTester', $inGroup);

/*    try {
      if($realm == env('CU_REALM')) {
        $this->checkAccount($netID);
      }
      else {
        if(!strpos($netID, '@wcmc')) {
          $netID .= "@wcmc";
        }
        $this->checkAccount($netID);
      }
    } catch(Exception $e) {
        return Redirect::route('blackboardError');
    }*/

		if($inGroup) {
			session()->put('canCreateSite', true);
			session()->put('canCreateUser', true);
		}

        if(session()->get("toggleSite")) {
            session()->put('canCreateSite', !session()->get("canCreateSite"));
        }

        if(session()->get("toggleUser")) {
            session()->put('canCreateUser', !session()->get("canCreateUser"));
        }

		return $next($request);
	}

	/**
	 * Checks whether an account with that netID already exists
	 *
	 * @param string $netID The netID or weillID of the current user
	 */
	public function checkAccount($netID)
	{
		// Check whether they already have a BB account with that netid
		$name = CanvasAPI::findUser($netID);
    if(!session()->has('bbsession')) {
  		Log::info("User $netID has been checked on Canvas - they".
  		       (!$name ? " don't " : " do ")."have a current account");
    }
		session()->put('canCreateUser', !$name);
		session()->put('bbsession', $session);

    if($name) {
      session()->put('bbUserId', $name);
    }
	}


	/**
	 * Checks whether the user has permission to create a site
	 *
	 * @param string $netID The netID or WeillID of the current user
	 */
	public function checkCreation($netID, $realm)
	{
        $data = LDAP::data($netID, $realm);
        if(!array_filter($data)) {
            return Redirect::route('ldapError');
        }
        if(!strpos($netID, '@wcmc') && $realm == "A.WCMC-AD.NET") {
            $netID .= "@wcmc";
        }
		session()->put("firstname",	$data["firstname"]);
		session()->put("lastname", $data["lastname"]);
		session()->put("netid", $netID);
        session()->put("email", $data["email"]);
        if($netID == env("TEST_NETID")) {
          session()->put('canCreateSite', true);
        }
        else {
            $name = CanvasAPI::findUser(trim($netID));
            session()->put('canCreateUser', !$name);
            session()->put('canCreateSite', $data["canCreateSite"] && $name);
            Log::info("User $netID has been checked on Canvas - they".
                (!$name ? " don't " : " do ")."have a current account");

        }
	}

}

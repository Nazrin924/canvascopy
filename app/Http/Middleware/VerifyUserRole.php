<?php

namespace App\Http\Middleware;

use App\Helpers\CanvasAPI;
use App\Helpers\LDAP;
use App\Helpers\Testers;
use App\User;
use Closure;
use Illuminate\Support\Facades\Auth;
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
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Log::info($_SERVER);
        $maskedNetid = session()->get('maskedNetid');
        $maskedRealm = session()->get('maskedRealm');
        if ($maskedNetid) {
            $netID = $maskedNetid;
            $realm = $maskedRealm;
            session()->put('toggleSite', false);
            session()->put('toggleUser', false);
        } else {
            // $netID = $request->headers->get('REMOTE_USER');
            // $realm = $request->headers->get('CUWA-REALM');
            $netID = env('REMOTE_USER');
            Log::info("netID set to $netID");
            $realm = env('Shib_Identity_Provider');
            if ($realm == 'https://shibidp-test.cit.cornell.edu/idp/shibboleth' or $realm == 'https://shibidp.cit.cornell.edu/idp/shibboleth') {
                $realm = 'CIT.CORNELL.EDU';
            } else {
                $realm = 'A.WCMC-AD.NET';
            }
            if ($netID) {
                $user = User::where('name', $netID)->first();
                if (! $user) {
                    $user = new User;
                    $user->name = $netID;
                    $user->email = "$netID@cornell.edu";
                    $user->password = 'secret';
                    $user->remember_token = '';
                    $user->save();
                }
                $user->updated_at = now();
                $user->update();    // update record to set time last login
                if (! Auth::guard('web')->check()) {
                    Auth::guard('web')->login($user);
                }
            } else {
                Log::info('error no netid');
            }
        }
        $realm = strtoupper($realm);
        session()->put('realm', $realm);
        session()->put('netID', $netID);
        try {
            $data = LDAP::data($netID, $realm);
            if (! array_filter($data)) {
                session()->put('errorMsg', "No $netID user found in LDAP");
                // $this->checkCreation($netID, $realm);
                session()->put('maskedNetid', '');
                session()->put('maskedRealm', '');
                // $netID = $request->headers->get(env('CU_REMOTE'));
                // $realm = $request->headers->get('CU-REALM');
                $netID = env('CU_REMOTE');
                $realm = env('CU_REALM');
                session()->put('realm', $realm);
                session()->put('netID', $netID);
                $this->checkCreation($netID, $realm);
                session()->put('toggleSite', false);
                session()->put('toggleUser', false);

            } elseif ($realm == env('CU_REALM')) {
                session()->put('errorMsg', '');
                $this->checkCreation($netID, $realm);
            } else {
                $this->checkCreation($netID, $realm);
                if (! strpos($netID, '@cumed')) {
                    $netID .= '@cumed';
                }
            }
        } catch (\Exception $e) {
            Log::info('ldapError 3');

            return Redirect::route('ldapError');
        }

        if (! session()->has('isDebugger') and ! $maskedNetid) {
            session()->put('isDebugger', Testers::check($netID, $realm));
        }

        if ($netID == env('TEST_NETID')) {
            session()->put('isTester', true);
            $inGroup = true;
        } else {
            try {
                $groups = LDAP::getADGroups($netID, $realm);
            } catch (\Exception $e) {
                Log::info('ldapError 1');

                return Redirect::route('ldapError');
            }
            $inGroup = in_array(env('AD_GROUP'), $groups);
            session()->put('isTester', $inGroup);
        }

        /*    try {
              if($realm == env('CU_REALM')) {
                $this->checkAccount($netID);
              }
              else {
                if(!strpos($netID, '@cumed')) {
                  $netID .= "@cumed";
                }
                $this->checkAccount($netID);
              }
            } catch(Exception $e) {
                return Redirect::route('blackboardError');
            }*/

        if ($inGroup) {
            session()->put('canCreateSite', true);
            session()->put('canCreateUser', true);
        }

        if (session()->get('toggleSite')) {
            session()->put('canCreateSite', ! session()->get('canCreateSite'));
        }

        if (session()->get('toggleUser')) {
            session()->put('canCreateUser', ! session()->get('canCreateUser'));
        }

        return $next($request);
    }

    /**
     * Checks whether an account with that netID already exists
     *
     * @param  string  $netID  The netID or weillID of the current user
     */
    public function checkAccount($netID)
    {
        // Check whether they already have a BB account with that netid
        $name = CanvasAPI::findUser($netID);
        if (! session()->has('bbsession')) {
            Log::info("User $netID has been checked on Canvas - they".
                   (! $name ? " don't " : ' do ').'have a current account');
        }
        session()->put('canCreateUser', ! $name);
        session()->put('bbsession', $session);

        if ($name) {
            session()->put('bbUserId', $name);
        }
    }

    /**
     * Checks whether the user has permission to create a site
     *
     * @param  string  $netID  The netID or WeillID of the current user
     */
    public function checkCreation($netID, $realm)
    {
        $data = LDAP::data($netID, $realm);
        if (! array_filter($data)) {
            Log::info('ldapError 2');

            return Redirect::route('ldapError');
        }
        if (! strpos($netID, '@cumed') && $realm == 'A.WCMC-AD.NET') {
            $netID .= '@cumed';
        }
        session()->put('firstname', $data['firstname']);
        session()->put('lastname', $data['lastname']);
        session()->put('netid', $netID);
        session()->put('email', $data['email']);
        if ($netID == env('TEST_NETID')) {
            session()->put('canCreateSite', true);
        } else {
            $name = CanvasAPI::findUser(trim($netID));
            session()->put('canCreateUser', ! $name);
            session()->put('canCreateSite', $data['canCreateSite'] && $name);

        }
    }
}

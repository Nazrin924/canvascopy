<?PHP namespace App\Helpers;

use \App\Helpers\BbPhp;

/**
 * A helper class for dealing with Blackboard WebServices
 *
 * This class provides the means for contacting Blackboard via WebServices.
 * It relies heavily on the BbPhp helper from St. Edward's University,
 * which provides a means for creating arbitrary Blackboard requests.
 * This class is primarily meant to deal with the creation and searching
 * of both courses and users.
 *
 * @author Richard Marisa rjm2@cornell.edu
 * @author Adam Gleisner amg295@cornell.edu
 * @author Jeremy Miller jdm389@cornell.edu
 */
class Blackboard
{

  /* Blackboard web services for the Guestid project
   * June, 2015
   * Richard Marisa
   * CIT Custom Development
   */

  /**
   * Initializes the BBWebServices instance
   * THIS FUNCTION IS NOT USED.
   * @param  string $netID The NetID of the initializing user
   *
   * @return string        The session ID
   */
  public static function initialize($netID) {
    	$debug       = false;
      $username    = "session";
    	$password    = "nosession";
    	$time        = gmdate("Y-m-d\TH:i:s\Z");
    	$timeplusone = gmdate("Y-m-d\TH:i:s\Z", time() + 10);

      $request = <<<REQUEST_BODY
    	<soapenv:Envelope xmlns:soapenv=
        "http://schemas.xmlsoap.org/soap/envelope/">
    	   <soapenv:Header>
    	    <wsse:Security
            xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/".
            "oasis-200401-wss-wssecurity-secext-1.0.xsd"
    	      xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/".
            "oasis-200401-wss-wssecurity-utility-1.0.xsd">
    	      <wsu:Timestamp>
    	        <wsu:Created>$time</wsu:Created>
    	        <wsu:Expires>$timeplusone</wsu:Expires>
    	      </wsu:Timestamp>
    	      <wsse:UsernameToken>
    	        <wsse:Username>$username</wsse:Username>
    	        <wsse:Password
              Type="http://docs.oasis-open.org/wss/2004/01/".
              "oasis-200401-wss-username-token-profile-1.0#PasswordText" >
              $password
              </wsse:Password>
    	      </wsse:UsernameToken>
    	    </wsse:Security>
    	</soapenv:Header>
    	   <soapenv:Body/>
    	</soapenv:Envelope>
REQUEST_BODY;
    if($debug) {
        print "REQUEST:\n$request\n";
    }

    	$url = env('BB_WS_URL') . '/Context.WS';
      try {
      	$curl = curl_init();
      	curl_setopt($curl, CURLOPT_URL, $url);
      	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
      	curl_setopt($curl, CURLOPT_TIMEOUT, 10);
      	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      	curl_setopt($curl, CURLOPT_POST, true);
      	curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
      	curl_setopt($curl, CURLOPT_COOKIEJAR,
          storage_path().'/'.$netID.'_'.'cookies.txt');
      	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: text/xml',
      												'Content-Length: '.strlen($request),
      	            								'SOAPAction: initialize'));
      	$result = curl_exec($curl);
        if (curl_errno($curl)) { 
          print curl_error($curl); 
          exit;
        } 
        //var_dump($result);exit;
      	$doc = new \SimpleXMLElement(strstr($result, '<?xml'));
      	$doc->registerXPathNamespace("ns", "http://context.ws.blackboard");

      	$dom = new \DOMDocument("1.0");
      	$dom->preserveWhiteSpace = false;
      	$dom->formatOutput = true;
      	$dom->loadXML($doc->asXML());
      	$xmlstring = $dom->saveXML(); // formatted xml string
      	if ($debug) {
      		print "RESPONSE:\n";
      		print "\n".$xmlstring."\n";
      	}

      	$sessionid = false;
      	$response = $doc->xpath("//ns:return");
        if ($response) {
          $sessionid = (string)$response[0];
        }
      } catch(Exception $e) {
        return false;
      }

    	return $sessionid;

  }

/**
 * Logs us into the session - must do after initialize
 * @param  string $sessionid The ID of the current session
 * @param  string $netID     the NetID of the current user
 * @return bool              Whether the login was successful
 */
public static function login($sessionid, $netID) {

	$username    = "session";
  $debug       = false;
	$password    = $sessionid;
	$time        = gmdate("Y-m-d\TH:i:s\Z");
	$timeplusone = gmdate("Y-m-d\TH:i:s\Z", time() + 10);
  if ($debug) {
    print "Time is ". $time. "\n";
  }

	$loginusername = env("BB_WS_USERNAME", "cuwsguest");
	$loginpassword = env("BB_WS_PASSWORD", "cuwsguest");

$request = <<<REQUEST_BODY
  <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:con="http://context.ws.blackboard">
    <soapenv:Header>
      <wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
        <wsu:Timestamp>
          <wsu:Created>$time</wsu:Created>
          <wsu:Expires>$timeplusone</wsu:Expires>
        </wsu:Timestamp>
        <wsse:UsernameToken>
          <wsse:Username>$username</wsse:Username>
          <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText" >$password</wsse:Password>
        </wsse:UsernameToken>
      </wsse:Security>
  </soapenv:Header>
     <soapenv:Body>
        <con:login>
           <!--Optional:-->
           <con:userid>$loginusername</con:userid>
           <!--Optional:-->
           <con:password>$loginpassword</con:password>
           <!--Optional:-->
           <con:clientVendorId>blackboard</con:clientVendorId>
           <!--Optional:-->
           <con:clientProgramId>Blackboard Inc.</con:clientProgramId>
           <!--Optional:-->
           <con:loginExtraInfo>true</con:loginExtraInfo>
           <!--Optional:-->
           <con:expectedLifeSeconds>140000</con:expectedLifeSeconds>
        </con:login>
     </soapenv:Body>
  </soapenv:Envelope>
REQUEST_BODY;

	//$url = Config::get('app.ps_url');
	$url = env('BB_WS_URL') . '/Context.WS';

  try {
  	$curl = curl_init();
  	curl_setopt($curl, CURLOPT_URL, $url);
  	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
  	curl_setopt($curl, CURLOPT_TIMEOUT, 10);
  	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
  	curl_setopt($curl, CURLOPT_POST, true);
  	curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
  	curl_setopt($curl, CURLOPT_COOKIEJAR,
      storage_path().'/'.$netID.'_'.'cookies.txt');
    curl_setopt($curl, CURLOPT_COOKIEFILE,
      storage_path().'/'.$netID.'_'.'cookies.txt');
  	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: text/xml',
  												'Content-Length: '.strlen($request),
  	            								'SOAPAction: login'));
  	$result = curl_exec($curl);
  	 if ($debug) {
         print "URL: \n";
  		 print "\n".$url."\n";
         print "RESULT: \n";
  		 print "\n".$result."\n";
  	 }

  	$doc = new \SimpleXMLElement(strstr($result, '<?xml'));
  	$doc->registerXPathNamespace("ns", "http://context.ws.blackboard");

  	$dom = new \DOMDocument("1.0");
  	$dom->preserveWhiteSpace = false;
  	$dom->formatOutput = true;
  	$dom->loadXML($doc->asXML());
  	$xmlstring = $dom->saveXML(); // formatted xml string
  	if ($debug) {
  		print "RESPONSE:\n";
  		print "\n".$xmlstring."\n";
  	}


  	$loggedin = $doc->xpath("//ns:return");
      if ($debug) {
          print "LOGGED IN:\n";
          print "\n".count($loggedin)."\n";
      }

  	if (count($loggedin) > 0) {
  		return $loggedin[0];
  	}
    else {
  		return false;
    }
  } catch(Exception $e) {
    return false;
  }
}

/**
 * Finds an existing user on Blackboard
 * @param  string $user  the username to look for
 * @param  string $netID the NetID of the current user
 * @return string|bool   user ID if success, false if failure
 */
public static function find_name($user, $netID) {
  $blackboard = new BbPhp(env('BB_WS_URL') . '/User.WS');
  Blackboard::login($blackboard->getSessionId(), $netID);
  //Calling methods with complex types.
  $results = $blackboard->User("getUser", array("filterType" => '6',
                                                'name' => $user), $netID);
  if(isset($results["id"])) {
    \Log::info("Name: ".$results["id"]);
    return $results;
  }
  else {
    \Log::info("Results of failed find_name: ".$results);
    return false;
  }
}

/**
 * Gets the list of datasources from Blackboard.
 * I don't believe this function is used - I stored the DSK in the .env
 *
 * @param string $netID The $netID of the current user
 * @return \Illuminate\View\View
 */
public static function get_datasources($netID) {
  $blackboard = new BbPhp(env('BB_WS_URL') . '/Util.WS');
  Blackboard::login($blackboard->getSessionId(), $netID);
  //Calling methods with complex types.
  $results = $blackboard->Util("getDataSources");
  if(isset($results)) {
    foreach($results as $result) {
      if(isset($result) && $result["batchUid"] == "BBTOOLS") {
        \Log::info("DSK is ".$result["id"]);
        return $result["id"];
      }
    }
    \Log::info("Could not find data source BBTOOLS");
    return false;
  }
  else {
    \Log::info("Could not find data source BBTOOLS");
    return false;
  }
}

/**
 * Checks to see if a course with a particular courseID exists already
 * @param  string $id    The courseID of the course we're searching for
 * @param  string $netID The netID of the current user
 * @return string|bool   CourseID if success, false if failure
 */
public static function checkCourse($id, $netID) {
  $blackboard = new BbPhp(env('BB_WS_URL') . '/Course.WS');
  Blackboard::login($blackboard->getSessionId(), $netID);

  $results = $blackboard->Course("getCourse", array("filterType" => '2',
                                                    'batchUids' => strtolower($id)), $netID);
  \Log::info('Course Search Results are: '.(isset($results["batchUid"]) ? $results["batchUid"] : "nothing"));
  if(count($results) > 1) {
    return isset($results["courseId"]) ? $results["courseId"] : true;
  }
  else {
    sleep(2);
    $results = $blackboard->Course("getCourse", array("filterType" => '2',
                                                      'batchUids' => strtolower($id)), $netID);
    \Log::info('Course Search Results are: '.(isset($results["batchUid"]) ? $results["batchUid"] : "nothing"));
    if(count($results) > 1) {
        \Log::info('Exists');
      return isset($results["courseId"]) ? $results["courseId"] : true;
    }
    else {
        \Log::info('Not Exists');
      return false;
    }
  }
}

/**
 * Attempts to create a course and enroll the current user as an instructor
 * @param  string $id    The courseID to be created
 * @param  string $name  The name of the course to be created
 * @param  string $netID The netID of the current user
 * @return string        The result of the course creation or failure
 */
public static function saveCourse($id, $name, $netID) {
  \Log::info("Starting save course for course $id created by $netID");
  $check = Blackboard::checkCourse($id, $netID);
  $wasAlready = isset($check["batchUid"]) ? $check["batchUid"] : false;

  // This function must sleep every so often in order to give
  // WebServices time to catch up with the creation of courses.
  $blackboard = new BbPhp(env('BB_WS_URL') . '/Course.WS');
  Blackboard::login($blackboard->getSessionId(), $netID);
  $dsk = env('DSK');
  \Log::info("Datasource Key is $dsk");
  sleep(10);
  // Attempts to save the course
  $results = $blackboard->Course("saveCourse", array('courseId' => $id,
     'batchUid' => strtolower($id),
     'dataSourceId' => $dsk,
     'available' => 'true',
     'showInCatalog' => 'true',
     'name' => $name), $netID);

  if($results) {
    \Log::info("Course with courseID $id has been saved, in theory");
    session()->put('created', $results);
  }
  else {
    \Log::info("Course $id could not be created at this time.");
  }

  sleep(5);

  // Checks if the course exists
  $resulting  = $blackboard->Course("getCourse", array("filterType" => '2',
                                                   'batchUids' => strtolower($id)), $netID);

  $exists = isset($resulting["batchUid"]) ? $resulting["batchUid"] : false;
  sleep(5);

  if($exists && $results) {
    \Log::info("And now it exists");
  }
  elseif($exists && !$results) {
    \Log::info("Course $id appears to have already existed");
  }
  elseif(!$exists && $results) {
    \Log::info("But it doesn't exist...");
  }
  else {
    \Log::info("Course $id neither exists, nor was created");
  }

  // Gets the user's Blackboard info
  $user = $blackboard->User("getUser", array("filterType" => "6",
                                             "name" => $netID), $netID);
  sleep(5);

  if($user) {
    \Log::info("The current user has a Blackboard account");
  }
  else {
    \Log::info("The current user does not have a Blackboard account");
  }

  // If there is a user, and the course was created
  if(($results || session()->has('created') || !$wasAlready && $exists) && $user) {
    \Log::info("The course was created, it now exists, and we have a user!");
    $userID        = $user["id"];
    // Check if already enrolled.
    $alreadyEnrolled = $blackboard->CourseMembership("getCourseMembership",
                                      array('filterType' => '2',
                                        'courseId' => session()->get('created'),
                                        'courseIds' => session()->get('created'),
                                        'userIds' => $userID,
                                        'roleIds' => 'P'));

    // Enroll the user as an instructor
    $resultsEnroll = $blackboard->CourseMembership("saveCourseMembership",
                                  array('available' => 'true',
                                    'courseId' => session()->get('created'),
                                    'userId' => $userID,
                                    'roleId' => 'P'));
    if($resultsEnroll || $alreadyEnrolled) {
      \Log::info("The user was enrolled successfully in course ".session()->get('created'));
      session()->forget('created');
      return "true";
    }
    else {
      \Log::info("The user was not enrolled successfully in course ".session()->get('created'));
      return " failure to enroll user.";
    }
  }
  else {
    if($user) {
      return " failure to create course.";
    }
    else {
      return " failure to find user.";
    }
  }
}

/**
 * Deletes a course with the given courseID from Blackboard
 * The app doesn't really support this functionality, so this isn't used.
 * @param  string $id    The courseID to be deleted
 * @param  string $netID The netID of the current user
 * @return string|bool   ID on success, false on failure
 */
public static function deleteCourse($id, $netID) {
  $blackboard = new BbPhp(env('BB_WS_URL') . '/Course.WS');
  Blackboard::login($blackboard->getSessionId(), $netID);
  //Calling methods with complex types.
  $results = $blackboard->Course("deleteCourse", array('ids' => $id));
  \Log::info("var dump results delete: ".print_r($results, true));
  if($results) {
    \Log::info("should have been deleted");
    return $results;
  }
  else {
    \Log::info($results);
    return false;
  }
}

/**
 * Deletes a user with the given userID from Blackboard
 * The app doesn't really support this functionality, so this isn't used.
 * @param  string $userid The userID to be deleted
 * @param  string $netID  The netID of the current user
 * @return bool           Whether the user was deleted
 */
public static function deleteUser($userid, $netID) {
  $blackboard = new BbPhp(env('BB_WS_URL') . '/User.WS');
  Blackboard::login($blackboard->getSessionId(), $netID);
  //Calling methods with complex types.
  $results = $blackboard->User("deleteUser", array("userId" => $userid));
  if(count($results) > 0) {
    return true;
  }
  else {
    \Log::info($results);
    return false;
  }
}

/**
 * Attempts to create a user on Blackboard
 * @param  string $firstname The first name of the user
 * @param  string $lastname  The last name of the user
 * @param  string $email     The email of the user
 * @param  string $account   The username of the user
 * @param  string $netID     The netID or WeillID of the user
 * @return string|bool       User on success, false on failure
 */
public static function create_user($firstname, $lastname,
                                   $email, $account, $netID) {
    $blackboard = new BbPhp(env('BB_WS_URL') . '/User.WS');
  Blackboard::login($blackboard->getSessionId(), $netID);
  $exists = Blackboard::find_name($account, $netID);
  if($exists) {
    \Log::info("$account already exists.");
    return false;
  }
  $dsk = env('DSK');
  \Log::info("Datasource key is $dsk");
  //Calling methods with complex types.
  $results = $blackboard->User("saveUser", array("name" => $account,
    'dataSourceId' => $dsk,
    'isAvailable' => 'true',
    'extendedInfo' => array(
      'emailAddress' => $email,
      'familyName' => $lastname,
      'givenName' => $firstname
    ),
    'userBatchUid' => $account
  ));
  if(count($results) > 0) {
    return $results[0];
  }
  else {
    return false;
  }
}

// THIS FUNCTION IS NOT USED
public static function change_user_batch_uid($old, $new, $sessionid, $netID) {

	$debug = false;

	$url = env('BB_WS_URL') . '/User.WS';
	$time = gmdate("Y-m-d\TH:i:s\Z");
	$timeplusone = gmdate("Y-m-d\TH:i:s\Z", time() + 10);

	if ($debug) print "changeUserBatchUid routine:\n";

$request = <<<REQUEST_BODY
	<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
  xmlns:user="http://user.ws.blackboard">
  <soapenv:Header>
	<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/".
    "oasis-200401-wss-wssecurity-secext-1.0.xsd"
	       xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/".
          "oasis-200401-wss-wssecurity-utility-1.0.xsd">
		<wsu:Timestamp>
		<wsu:Created>$time</wsu:Created>
		<wsu:Expires>$timeplusone</wsu:Expires>
		</wsu:Timestamp>
		<wsse:UsernameToken>
		<wsse:Username>session</wsse:Username>
		<wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/".
      "oasis-200401-wss-username-token-profile-1.0#PasswordText" >
      $sessionid</wsse:Password>
		</wsse:UsernameToken>
	</wsse:Security>
  </soapenv:Header>
	   <soapenv:Body>
	      <user:changeUserBatchUid>
	         <!--Optional:-->
	         <user:originalBatchUid>$old</user:originalBatchUid>
	         <!--Optional:-->
	         <user:batchUid>$new</user:batchUid>
	      </user:changeUserBatchUid>
	   </soapenv:Body>
     </soapenv:Envelope>
REQUEST_BODY;

	if ($debug) {
		print "REQUEST:\n";
		print "\n$request\n";
	}

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($curl, CURLOPT_TIMEOUT, 10);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
	curl_setopt($curl, CURLOPT_COOKIEJAR,
    storage_path().'/'.$netID.'_'.'cookies.txt');
  curl_setopt($curl, CURLOPT_COOKIEFILE,
    storage_path().'/'.$netID.'_'.'cookies.txt');
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: text/xml',
											'Content-Length: '.strlen($request),
											'SOAPAction: changeUserBatchUid'));
	$result = curl_exec($curl);
	if ($debug) {
		print "\n".$url."\n";
		//print "\n".$result."\n";
	}

	$doc = new \SimpleXMLElement(strstr($result, '<?xml'));
	$doc->registerXPathNamespace("ns", "http://user.ws.blackboard");

	$dom = new \DOMDocument("1.0");
	$dom->preserveWhiteSpace = false;
	$dom->formatOutput = true;
	$dom->loadXML($doc->asXML());

	$return = $doc->xpath("//ns:return");
	if ($debug) {
		print "Here is the return value\n " . var_dump($return);
	}

	if ($return) {
		$id = (string)$return[0];
		if ($debug) print "changeUserBatchUid returning return value: $id\n";
		return $id;
	} else {
		if ($debug) print "changeUserBatchUid returning Did not update user\n";
		return "Did not change user batch uid";
	}
}

// THIS FUNCTION IS NOT USED.
public static function change_user_datasource_id($user, $dsid,
  $sessionid, $netID) {

	$debug = false;

	$url = env('BB_WS_URL') . '/User.WS';
	$time = gmdate("Y-m-d\TH:i:s\Z");
	$timeplusone = gmdate("Y-m-d\TH:i:s\Z", time() + 10);

  if ($debug) {
    print "changeUserDataSourceId routine:\n";
  }

$request = <<<REQUEST_BODY
	<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
  xmlns:user="http://user.ws.blackboard">
  <soapenv:Header>
	<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/".
    "oasis-200401-wss-wssecurity-secext-1.0.xsd"
	       xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/".
          "oasis-200401-wss-wssecurity-utility-1.0.xsd">
		<wsu:Timestamp>
		<wsu:Created>$time</wsu:Created>
		<wsu:Expires>$timeplusone</wsu:Expires>
		</wsu:Timestamp>
		<wsse:UsernameToken>
		<wsse:Username>session</wsse:Username>
		<wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/".
      "oasis-200401-wss-username-token-profile-1.0#PasswordText" >
      $sessionid</wsse:Password>
		</wsse:UsernameToken>
	</wsse:Security>
  </soapenv:Header>
	   <soapenv:Body>
	      <user:changeUserDataSourceId>
	         <!--Optional:-->
	         <user:userId>$user</user:userId>
	         <!--Optional:-->
	         <user:dataSourceId>$dsid</user:dataSourceId>
	      </user:changeUserDataSourceId>
	   </soapenv:Body>
	</soapenv:Envelope>

REQUEST_BODY;

	if ($debug) {
		print "REQUEST:\n";
		print "\n$request\n";
	}

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($curl, CURLOPT_TIMEOUT, 10);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
	curl_setopt($curl, CURLOPT_COOKIEJAR,
    storage_path().'/'.$netID.'_'.'cookies.txt');
  curl_setopt($curl, CURLOPT_COOKIEFILE,
    storage_path().'/'.$netID.'_'.'cookies.txt');
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: text/xml',
											'Content-Length: '.strlen($request),
											'SOAPAction: changeUserDataSourceId'));
	$result = curl_exec($curl);
	if ($debug) {
		print "\n".$url."\n";
		//print "\n".$result."\n";
	}

	$doc = new \SimpleXMLElement(strstr($result, '<?xml'));
	$doc->registerXPathNamespace("ns", "http://user.ws.blackboard");

	$dom = new \DOMDocument("1.0");
	$dom->preserveWhiteSpace = false;
	$dom->formatOutput = true;
	$dom->loadXML($doc->asXML());

	$return = $doc->xpath("//ns:return");
	if ($debug) {
		print "Here is the return value\n " . var_dump($return);
	}

	if ($return) {
		$id = (string)$return[0];
		if ($debug) print "changeUserDataSourceId returning return value: $id\n";
		return $id;
	} else {
    if ($debug) {
      print "changeUserDataSourceId returning Did not update user\n";
    }

		return "Did not change datasource id";
	}
}

}

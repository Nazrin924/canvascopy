<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Http\Requests;

use Illuminate\Http\Request;

use App\Helpers\Blackboard;
use \App\Helpers\BbPhp;

class debugController extends Controller {

	public function __construct() {

	}

	public function testSomething(Request $request){
		$debug = array();
		$log = function($item) use(&$debug){
			array_push($debug,$item);
		};

		//-------------------Test code below----------------//

			// $id = "Test_01-Test_01-Summer2016";
			// $name = "A new course name";
			// $netID = "jdm389";
			//
			// $log("Trying to create $id");
			//
			// $session = Blackboard::initialize($netID);
			// Blackboard::login($session, $netID);
			//
			$blackboard = new BbPhp(env('BB_WS_URL') . '/Course.WS');
			Blackboard::login($blackboard->getSessionId(), 'amg295');
			// sleep(1);
			// // Attempts to save the course
			//
			// $results = null;
			// try
			// {
			// 	$results = $blackboard->Course("saveCourse", array('courseId' => $id,
			// 	   'batchUid' => $id,
			// 	   'dataSourceId' => 'BBTools',
			// 	   'available' => 'true',
			// 	   'showInCatalog' => 'true',
			// 	   'name' => $name));
			// }
			// catch(\Exception $e){}
			//
			//
			// sleep(3);
			// $log("results");
			// $log($results);


			// Checks if the course exists
			$exists = null;
			try
			{
				$exists  = $blackboard->Course("getCourse", array("filterType" => '2','batchUids' => 'test12345-gleisner-summer2016'));
			}
			catch(\Exception $e){}

			sleep(3);
			$log("exists");
			$log($exists);

			$dataSources = Blackboard::get_datasources('amg295');
			$log($dataSources);


			// Gets the user's Blackboard info
			//
			// $user = $blackboard->User("getUser", array("filterType" => "6", "name" => $netID));
			//
			// sleep(3);
			// // If there is a user, and the course was created
			// if(($results || $exists) && $user)
			// {
			// 	$userID = $user["id"];
			// 	// Enroll the user as an instructor
			// 	$resultsEnroll = $blackboard->CourseMembership("saveCourseMembership",array(
			// 		'available' => 'true',
			// 		'courseId' => $results,
			// 		'userId' => $userID,
			// 		'roleId' => 'P'));
			// 	if($resultsEnroll)
			// 	{
			// 	    $log("Successfully enrolled user");
			// 	}
			// 	else
			// 		$log("failure to enroll user.");
			// }
			// else
			// {
			// 	if($user) $log(" failure to create course.");
			// 	else $log(" failure to find user.");
			// }



		//-------------------TEST CODE ABOVE----------------//
		return view("debug", ['debug' => $debug]);
	}


	public function testSomething2(Request $request){
		$debug = array();
		$log = function($item) use(&$debug){
			array_push($debug,$item);
		};

		//-------------------Test code below----------------//

			$id = "asdf-asdf-NA2016";
			$netID = "jdm389";

			$log("Deleting $id");

			$blackboard = new BbPhp(env('BB_WS_URL') . '/Course.WS');
			Blackboard::login($blackboard->getSessionId(), $netID);

			//Calling methods with complex types.
			$results = $blackboard->Course("deleteCourse", array('ids'=>$id));

			$log('Results: ');
			$log($results);

		//-------------------TEST CODE ABOVE----------------//
		return view("debug", ['debug' => $debug]);
	}


	public function testSomething3(Request $request){
		$debug = array();
		$log = function($item) use(&$debug){
			array_push($debug,$item);
		};

		//-------------------Test code below----------------//

			$id = "test_01-test_01-Summer2016";
			$netID = "jdm389";

			$log("Checking to see whether $id exists");

			$blackboard = new BbPhp(env('BB_WS_URL') . '/Course.WS');
			Blackboard::login($blackboard->getSessionId(), $netID);

			$exists = $blackboard->Course("getCourse", array("filterType" => '1','courseIds' => $id));
			//$exists = $blackboard->Course("getCourse", array("filterType" => '1'));
			$log($exists);

			//$batchExists = $blackboard->Course("getCourse", array("filterType" => '2','batchUids' => $id));

			//$log($batchExists);

		//-------------------TEST CODE ABOVE----------------//
		return view("debug", ['debug' => $debug]);
	}


	//sites that currently exist
	//asdf-asdf-NA2016

	// test_01-test_01-Summer2016
	// Test1-Test3-winter2017
	// Test1-Test3-Winter2017
	// Test1-Test3-Winter2017

	// Test_01-Test_01-Summer2016


	public function testSomething4(Request $request)
	{
		$debug = array();
		$log = function($item) use(&$debug){
			array_push($debug,$item);
		};

		//-------------------Test code below----------------//

			$netID = $request->headers->get(env('CU_REMOTE'));
			$log(\App\Helpers\LDAP::getADGroups($netID));

			$log(\App\Helpers\Testers::getTesters());

			$log(\App\Helpers\Testers::check($netID));

		//-------------------TEST CODE ABOVE----------------//
		return view("debug", ['debug' => $debug]);
	}
}

?>

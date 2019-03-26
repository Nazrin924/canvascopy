<?php

namespace App\Http\Controllers;

use Validator;

use App\Http\Controllers\Controller;

use App\Jobs\DoCourseCreation;

use Redirect;
use Log;

use Illuminate\Http\Request;

use Queue;

use App\Helpers\CanvasAPI;

/**
 * Controller for the course creation process
 *
 * CourseController displays each step of the course creation process.
 * This includes the initial "check facultycenter" page,
 * the copyright confirmation page, the course info page,
 * the course confirmation page, and finally actually queueing the course
 * creation and returning a message.
 *
 * @author Adam Gleisner amg295@cornell.edu
 * @author Jeremy Miller jdm389@cornell.edu
 */
class CourseController extends Controller
{

	/**
	 * An empty constructor for the controller.
	 */
	public function __construct() {

	}

	/**
	 * Shows the first page for creating a new course - confirmation that
	 * it isn't already available in faculty center.
	 *
	 * @param Request $request The HTTP Request
	 *
	 * @return \Illuminate\View\View The view for checking faculty center
	 */
	public function check(Request $request) {
		if(!session()->get("canCreateSite")) {
			return view('courseError');
    }
		session()->put('copyrighted', false);
		$netID = $request->headers->get(env('CU_REMOTE'));

		return view('check', [
          'name' => session()->get('firstname')." ".session()->get('lastname'),
          'netID' => $netID
        ]);
	}

	/**
	 * Shows the copyright agreement page.
	 *
	 * @param Request $request The HTTP Request
	 *
	 * @return \Illuminate\View\View The view for copyright confirmation
	 */
	public function copyright(Request $request)
	{
		if(!session()->get("canCreateSite")) {
			return view('courseError');
    }
		if($request->has('submit')) {
			session()->put('copyrighted', true);
			return Redirect::route('courseInfo');
		}

		session()->put('copyrighted', false);
		$netID = session()->has('netid') ? session()->get('netid') : $request->headers->get(env('CU_REMOTE'));
		return view('copyright', ['name' => "", 'netID' => $netID]);
	}

	/**
	 * Shows the course info page, where a course can be added.
	 *
	 * @param Request $request The HTTP Request which validates the input
	 *
	 * @return \Illuminate\View\View The view for adding course info
	 */
	public function info(Request $request)
	{
		if(!session()->get("canCreateSite")) {
			return view('courseError');
    }

		if($request->has('copyrighted')) {
			session()->put('copyrighted', true);
    }

		if($request->has('cancel')) {
			return Redirect::route('index');
    }

		if(!session()->get('copyrighted')) {
			return Redirect::route('copyright');
    }
		else {
			if($request->get('submit') == "Submit" || $request->get('submit') == "Return") {
				return $this->courseInfo($request);
			}
			return view('info');
		}
	}

	/**
	 * Validates the input after posting
	 * @param  Request $request The HTTP Request
	 * @return \Illuminate\View\View The result view
	 */
	public function courseInfo(Request $request)
	{
		$yearNow = getdate()["year"];
		$yearOne = $yearNow + 1;
		$yearTwo = $yearNow + 2;

		$validator = Validator::make($request->all(), [
			'txtCourseID'   => ['required', 'regex:/^[A-Za-z0-9\-_]+$/', 'max:25'],
			'txtCourseName' => 'required|string|max:128',
      'txtLastName'   => [
        'required',
        'string',
        'max:25',
        'regex:/^[A-Za-z0-9\-_.]+$/'
      ],
			'semester'      => ['required', "regex:/^(Summer|Winter|Spring|Fall|NA)$/"],
			'year'          => ['required', "regex:/^(NA|$yearNow|$yearOne|$yearTwo)$/"]
		], [
			'txtCourseID.required'   => 'The courseID field is required.',
			'txtCourseID.regex'      => 'The courseID must contain only A-z, 0-9, -, or _',
			'txtCourseID.max'        => 'The courseID must contain less than 25 characters.',
			'txtCourseName.max'      => 'The course name must contain less than 128 characters.',
			'txtLastName.max'        => 'The last name must be less than 25 characters',
      'txtLastName.regex'      => 'The last name must contain only A-z, 0-9, -, ., or _',
			'txtCourseName.required' => 'The course name field is required.',
			'txtLastName.required'   => 'The last name field is required',
			'semester.regex'         => 'Please select one of the provided terms.',
			'year.regex'             => 'Please select one of the provided years.'
		]);

		if($validator->fails()) {
			return Redirect::to('courseInfo')->withInput()->withErrors($validator);
		}

		if($request->get('submit') == "Return") {
			session()->flash('error', 'Please choose another course ID');
			return Redirect::to('courseInfo')->withInput();
    }
		return $this->confirm($request);
	}

	/**
	 * Reflahses data and redirects back to course info
	*/
	public function badIDRedirect(Request $request)
	{
		session()->reflash();
		return Redirect::to('courseInfo');
	}

	/**
	 * Shows the course info page, where a course can be added.
	 *
	 * @param Request $request The HTTP Request
	 *
	 * @return \Illuminate\View\View The view for confirming course creation
	 */
	public function confirm(Request $request) {
		if(!session()->get("canCreateSite")) {
			return view('courseError');
    }

		if(!session()->get('copyrighted')) {
        return Redirect::route('copyright');
    }

		$confirmedCourse = array();

		$confirmedCourse['courseID'] = $request->get('txtCourseID');
		$confirmedCourse['courseName'] = $request->get('txtCourseName');
		$confirmedCourse['lastName'] = $request->get('txtLastName');
		$confirmedCourse['semester'] = $request->get('semester');
		$confirmedCourse['year'] = $request->get('year');

		$confirmedCourse['courseIDReal'] = $confirmedCourse['courseID']."-".$confirmedCourse['lastName'];
		$confirmedCourse['courseIDReal'] = preg_replace("/'/", "", $confirmedCourse["courseIDReal"]);
		if($confirmedCourse['semester'] !="NA") {
			$confirmedCourse['courseIDReal'] .= "-";
            $confirmedCourse['courseIDReal'] .= $confirmedCourse['semester']=="Fall"?"FA":"";
            $confirmedCourse['courseIDReal'] .= $confirmedCourse['semester']=="Spring"?"SP":"";
            $confirmedCourse['courseIDReal'] .= $confirmedCourse['semester']=="Summer"?"SU":"";
            $confirmedCourse['courseIDReal'] .= $confirmedCourse['semester']=="Winter"?"WI":"";
		}
        if($confirmedCourse['year'] !="NA") {
            $confirmedCourse['courseIDReal'] .= "-";
            $confirmedCourse['courseIDReal'] .= $confirmedCourse['year']!="NA"?$confirmedCourse['year']:"";
        }

		session()->put("confirmedCourse", $confirmedCourse);
		return view('confirm', [
		  'courseID' => $confirmedCourse['courseIDReal'],
		  'courseName' => $confirmedCourse['courseName']
		]);
	}

	/**
	 * Verifies the course, and queues the creation.
	 *
	 * @param Request $request The HTTP Request
	 *
	 * @return \Illuminate\View\View The result view for course creation
	 */
	public function confirmation(Request $request) {
		$netID = session()->has('netid') ? session()->get('netid') : $request->headers->get(env('CU_REMOTE'));
    if(session()->get('realm') != env('CU_REALM')) {
      if(!strpos($netID, '@wcmc')) {
        $netID .= "@wcmc";
      }
    }
		if(!session()->get("canCreateSite")) {
			return view('courseError');
        }

		if(!session()->has('confirmedCourse')) {
			return Redirect::to('courseInfo');
        }

		if(!session()->get('copyrighted')) {
          return Redirect::route('copyright');
        }

		$confirmedCourse = session()->get('confirmedCourse');
		$firstName = session()->get('firstname');
		session()->forget('confirmedCourse');

		if($request->has('cancel')) {
			session()->flash('txtCourseName',$confirmedCourse['courseName']);
			session()->flash('txtCourseID',$confirmedCourse['courseID']);
			session()->flash('txtLastName',$confirmedCourse['lastName']);
			session()->flash('semester',$confirmedCourse['semester']);
			session()->flash('year',$confirmedCourse['year']);

			return Redirect::to('courseInfo');
		}

    try {
      $exists = CanvasAPI::findCourse(
        htmlspecialchars(utf8_encode($confirmedCourse['courseIDReal']), ENT_QUOTES, 'UTF-8')
      );
    } catch(Exception $e) {
      return view('blackboardError');
    }
        \Log::info("Got here 1");
    if($exists) {
        session()->flash('txtCourseName',$confirmedCourse['courseName']);
        session()->flash('txtCourseID',$confirmedCourse['courseID']);
        session()->flash('txtLastName',$confirmedCourse['lastName']);
        session()->flash('semester',$confirmedCourse['semester']);
        session()->flash('year',$confirmedCourse['year']);

        return view("badCourseID");
    }
    $email = "";
    if(strpos($netID, '@wcmc') !== false) {
      $repNetID = str_replace("@wcmc", "", $netID);
      $email = "$repNetID@med.cornell.edu";
    }
    elseif(session()->get('realm') != env('CU_REALM')) {
      $email = "$netID@med.cornell.edu";
    }
    elseif(session()->get('realm') == env('CU_REALM')) {
      $email = "$netID@cornell.edu";
    }
    if(session()->has('email') && session()->get('email') != '') {
      $email = session()->get('email');
    }

    try {
  		Queue::push(new DoCourseCreation($confirmedCourse['courseIDReal'], $confirmedCourse['courseName'], $netID, $firstName, session()->get('realm'), $email));
    } catch(Exception $e) {
        \Log::info("Got here 4");
      return view('blackboardError');
    }

    return view('confirmation', [
        'courseName' => $confirmedCourse['courseName'],
        'courseID' => $confirmedCourse['courseIDReal'],
        'success' => true
    ]);
	}
}

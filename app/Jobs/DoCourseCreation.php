<?php

namespace App\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Mail\Mailer;

use \App\Helpers\CanvasAPI;
use Queue;
use Log;
use Mail;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * A class for attempting to create a course on Canvas
 *
 * DoQueueStuff is a Job that gets put into the Queue every time
 * a user requests a course creation on Canvas. Each time it runs,
 * it will check if a course with the same courseID exists already,
 * and also attempt to create a course. On success, it sends the user
 * an email letting them know the course was successfully created; on failure,
 * it sends the administrator an email letting them know the course creation
 * failed.
 *
 * @author Adam Gleisner amg295@cornell.edu
 * @author Jeremy Miller jdm389@cornell.edu
 */
class DoCourseCreation extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

  	protected $courseID;
  	protected $courseName;
  	protected $netID;
  	protected $firstName;
  	protected $realm;
    protected $email;

  	/**
  	 * Create a new command instance.
  	 *
  	 * @return void
  	 */
  	public function __construct($courseID, $courseName, $netID, $firstName, $realm, $email) {
  		$this->courseID = $courseID;
  		$this->courseName = $courseName;
  		$this->netID = $netID;
      $this->email = $email;
      $this->firstName = $firstName;
  		$this->realm = $realm;
  		\Log::info("Course request for $courseName with courseID ".
        "$courseID by netID $netID in realm $realm has been started.");
  	}

  	/**
  	 * Creates canvas site; sends an email to user
  	 *
  	 * @return void
  	 */
  	public function handle(Mailer $mailer) {
  		$attempts = $this->attempts();
  		\Log::info("email to $this->netID for course ".
        "$this->courseID on attempt $attempts");

      \Log::info($this->courseID);

      try {
    		$course = CanvasAPI::createCourse(
          htmlspecialchars(utf8_encode($this->courseID), ENT_QUOTES, 'UTF-8'),
          htmlspecialchars(utf8_encode($this->courseName), ENT_QUOTES, 'UTF-8')
        );
      } catch(Exception $e) {
        \Log::error("Canvas error in course creation - aborting.");
        return false;
      }
  		//send out email to support if it's been over 5 hours.
  		if($this->attempts() > env('NUM_ATTEMPTS')) {
  			$netID = $this->netID;
        try {
    			$mailer->send('emails.BlackboardSiteFailed',
            array('courseID' => $this->courseID,
                  'netID' => $this->netID,
                  'courseName' => $this->courseName,
                  'firstName' => $this->firstName),
              function($message) use ($netID){

        				$message
                  ->from(env('EMAIL_ADMIN'), "Center for Teaching Innovation")
        	        ->to(env('EMAIL_ADMIN'))
                  ->subject("[CanvasTools] - Course Creation Failure");
    	         });
        } catch(Exception $e) {
          Log::error("Mail error in course creation - aborting.");
          $this->delete();
          return false;
        }
  			Log::error("Course $this->courseID for $netID has ".
          "failed to be created. It has been attempted more than 6 times.");
  			Log::info("Email to ".env('EMAIL_ADMIN').", Subject: [CanvasTools] - Course Creation Failure,".
  				" Content ".view('emails.BlackboardSiteFailed',
  				array('netID' => $this->netID,
  					'courseID' => $this->courseID))->__toString());
  			$this->delete();
  		}
  		//Check if exists.
  		elseif($course != "true") {
  			Log::info("Course creation failed because of".$course);
  			$this->release(100);
  		}
  		// course exists - send out email to client
  		else {
  			$netID = $this->netID;
        $email = $this->email;
        try {
  				Mail::send('emails.BlackboardSiteInfo',
  	        array('netID' => $this->netID,
  	          'courseID' => $this->courseID,
  	          'courseName' => $this->courseName,
  	          'firstName' => $this->firstName),
  	        function($message) use ($netID, $email){

  	  				$message
  	  					->from(env('EMAIL_ADMIN'), "Center for Teaching Innovation")
  	  					->to($email)
  	  					->subject("Canvas@Cornell - Course Creation");
  	        });
        } catch(Exception $e) {
          Log::info("Mail error in course creation (successfully created).");
          return false;
        }
  			Log::info("Course $this->courseID for $netID has been ".
          "successfully created, and an email was sent out.");
  			Log::info("Email to $email, Subject [Canvas@Cornell Course Creation],".
  				" Content ".view('emails.BlackboardSiteInfo',
  				array('netID' => $this->netID,
  					'courseID' => $this->courseID,
  					'courseName' => $this->courseName,
  					'firstName' => $this->firstName))->__toString());
  		}
  	}
}

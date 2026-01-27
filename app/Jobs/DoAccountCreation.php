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
 * A class for attempting to create an account on Canvas
 *
 * The DoAccountCreation class specifies a Job that is put into the Queue
 * whenever a user requests an account. Every minute, the handle() function
 * is run to attempt to create an account for the user. If it succeeds, an
 * email is sent to the user; if it fails, an email is sent to the administrator
 * detailing that failure.
 *
 * @author Adam Gleisner amg295@cornell.edu
 * @author Jeremy Miller jdm389@cornell.edu
 *
 */
class DoAccountCreation extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

  	protected $netID;
  	protected $firstName;
  	protected $lastName;
  	protected $email;
  	protected $realm;

  	/**
  	* Create a new command instance.
  	*
  	* @return void
  	*/
  	public function __construct($netID, $firstName, $lastName, $email, $realm)
  	{
  		$this->netID = $netID;
  		$this->firstName = $firstName;
  		$this->lastName = $lastName;
  		$this->email = $email;
  		$this->realm = $realm;
  		Log::info("Account request for $firstName $lastName ".
        "with NetID $netID has been started.");
  	}

  	/**
  	 * Creates canvas site; sends an email to user
  	 *
  	 * @return void
  	 */
  	 public function handle(Mailer $mailer)
  	{
  		$attempts = $this->attempts();
		$fromEmail = env('MAIL_FROM_ADDRESS');
        $fromName  = env('MAIL_FROM_NAME');
		
  		// Create account
  		try {
    		if(CanvasAPI::findUser($this->netID)) {
    			 Log::info("CanvasAPI::createUser - the account for $this->netID was created");
                 $netID = $this->netID;
				 $email = $this->email;
                 //Adding emailing here
                 Mail::send('emails.AccountCreated',
                     array(
                        'firstName' => $this->firstName,
                        'netID' => $this->netID,
                        'lastName' => $this->lastName,
                        'email' => $this->email),
                     function($message) use ($netID, $email, $fromEmail, $fromName) {
                        $message
                            ->from($fromEmail, $fromName)
                            ->to($email)
                            ->subject("Canvas@Cornell User Account");
                     }
                );
                Log::info("CanvasAPI::createUser - an email informing $this->netID about user account creation was sent");
    			$this->delete();
    			return;
    		}
    		$user = CanvasAPI::createUser(
                htmlspecialchars(utf8_encode($this->firstName), ENT_QUOTES, 'UTF-8'),
                htmlspecialchars(utf8_encode($this->lastName), ENT_QUOTES, 'UTF-8'),
                $this->email,
                $this->netID
            );
            sleep(60);

        } catch(Exception $e) {
          Log::error("Canvas failure in account creation - aborting");
          return false;
        }
  		 //send out email to support if it's been over 5 hours.
        //Log::info("Attempts check: ". $this->attempts() . " compared with ".env('NUM_ATTEMPTS'));

  		if($this->attempts() > env('NUM_ATTEMPTS')) {
            //Log::info("Got to the attempts checkup $attempts");

  			$netID = $this->netID;
			$email = $this->email;
            try {
        			 $mailer->send('emails.AccountFailed',
                        array(
                            'firstName' => $this->firstName,
                            'netID' => $this->netID,
                            'lastName' => $this->lastName,
                            'email' => $this->email
                        ),
                        function($message) use ($netID, $email, $fromEmail, $fromName) {

                                 $message
                                     ->from($fromEmail, $fromName)
                                     ->to(env('EMAIL_ADMIN'))
                                     ->subject("CanvasTools - User Account Creation Failure");
                        });
            } catch(Exception $e) {
               Log::error("Mail error in account creation - aborting.");
               $this->delete();
               return false;
            }
  			Log::error("Account for $netID has failed to be created. It has been in the queue for over 5 hours.");
  			$this->delete();
  		}

  		//Check if exists.
  		elseif(!CanvasAPI::findUser($this->netID)) {
  			 $this->release(300);
  		}

  		 // account exists - send out email to client
  		else {
  			session()->put('bbUserId', CanvasAPI::findUser($this->netID));
  			$netID = $this->netID;
			$email = $this->email;
            try {
    			 Mail::send('emails.AccountCreated',
                    array(
                        'firstName' => $this->firstName,
                        'netID' => $this->netID,
                        'lastName' => $this->lastName,
    					'email' => $this->email
                    ),
                    function($message) use ($netID, $email, $fromEmail, $fromName) {

    				    $message
      				        ->from($fromEmail, $fromName)
      				        ->to($email)
      				      ->subject("Canvas@Cornell User Account Creation");
                    });
            } catch(Exception $e) {
                Log::error("Mail error in account creation (successfully created).");
                return false;
            }

  			Log::info("Account for $netID has been successfully created, and an email was sent out.");
  			Log::info("Email to $this->email, Subject Canvas@Cornell User Account Creation,".
   				" Content ".view('emails.AccountCreated',
   				array(
                    'netID' => $this->netID,
   					'firstName' => $this->firstName,
   					'lastName' => $this->lastName,
   					'email' => $this->email
                ))->__toString()
            );
  		}
  	}
}

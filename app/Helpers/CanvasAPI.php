<?php
namespace App\Helpers;

use GuzzleHttp\Client;

/**
 * A helper class for dealing with Canvas WebServices
 *
 * This class provides the means for contacting Canvas API.
 * It primarily deals with connecting to Canvas API with Guzzle Client and
 * running checks on existing users and courses, and creating new ones.
 *
 * @author Nazrin Tingstrom nst37@cornell.edu
 */
class CanvasAPI {


    /*
    |--------------------------------------------------------------------------
    | Declarations
    |--------------------------------------------------------------------------
    */

    private $token; // generated in Canvas

    private $apiHost;




    function __construct() {

        $this->token = env("CVS_WS_TOKEN");
        $this->apiHost = env("CVS_WS_URL");
    }



    /*
    |--------------------------------------------------------------------------
    | API Calls
    |--------------------------------------------------------------------------
    |
    | Methods that actually perform the API calls to Canvas APIs.
    |
    */

    /**
     * apiCall() - performs a call to the Canvas API
     *
     * @param string $method - Request method (e.g. GET, POST, HEAD, etc.)
     * @param string $params - the relative endpoint URL (e.g. /user, /account)
     *
     * @throws
     * @return mixed The decoded data.
     *
     */

    public function apiCall($method, $params) {

        $client = new Client(['base_uri' => $this->apiHost]);
        $headers = [
            'Authorization' => 'Bearer ' . $this->token,
            'Accept'        => 'application/json',
            'http_errors' => true,
        ];

        // perform the call
        $response = $client->request($method, $params, [
            'headers' => $headers
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }




    /*
    |--------------------------------------------------------------------------
    | Methods
    |--------------------------------------------------------------------------
    |
    | API methods - note that this will likely expand as the API is fleshed out
    |
    */

    /**
     * findUser() - function to check if user exists
     *
     * @param $netid
     *
     * @throws \Exception
     * @return boolean
     */
    public static function findUser($netid) {
        \Log::info("In findUser method");
        $token = env("CVS_WS_TOKEN");
        $apiHost = env("CVS_WS_URL");
        $client = new Client();
        try{
        $response = $client->request("GET", $apiHost."accounts/self/users", [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
                'http_errors' => true,
            ],
            'form_params' => [
                'search_term'   => str_replace("@cumed", "", $netid),
            ]
        ]);
        $results = json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            \Log::info("Failed at findUser call $netid.") . $e->getResponse()->getStatusCode();
        } catch(Exception $e) {
            \Log::info("Failed at findUser call $netid.");
            return false;
            
        }
        
        if(isset($results[0]["id"])) {
            //dd($results);
            \Log::info("User $netid exists in Canvas.");
            return true;
        }
        else {
            \Log::info("Did not find $netid in Canvas. ");
            return false;
        }

    }

    /**
     * getUserID() - function to get Canvas user ID given a netid
     *
     * @param $netid
     *
     * @throws \Exception
     * @return int
     */
    public static function getUserID($netid) {
        $token = env("CVS_WS_TOKEN");
        $apiHost = env("CVS_WS_URL");
        $client = new Client();
        $response = $client->request("GET", $apiHost."accounts/self/users", [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
                'http_errors' => true,
            ],
            'form_params' => [
                'search_term'   => str_replace("@cumed", "", $netid),
            ]
        ]);
        $results = json_decode($response->getBody(), true);

        if(isset($results[0]["id"])) {
            return $results[0]["id"];
        }
        else {
            return 0;
        }

    }    

    /**
     * findCourse() - function to check if a course exists
     *
     * @param $courseId
     *
     * @throws \Exception
     * @return boolean
     */

    public static function findCourse($courseId) {
        $token = env("CVS_WS_TOKEN");
        $apiHost = env("CVS_WS_URL");
        $client = new Client();
        $response = $client->request("GET", $apiHost."accounts/1/courses", [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
                'http_errors' => true,
            ],
            'form_params' => [
                'search_term'   => $courseId,
            ]
        ]);
        $results = json_decode($response->getBody(), true);

        if(isset($results["id"])) {
            \Log::info("Course $courseId exists in Canvas.");
            return true;
        }
        else {
            \Log::info("Did not find course $courseId in Canvas.");
            return false;
        }


    }

    /**
     * createUser() - function to create a user
     *
     * @param $firstName
     * @param $lastName
     * @param $email
     * @param $netid
     *
     * @throws \Exception
     * @return boolean
     */
    public static function createUser($firstName, $lastName,$email, $netid) {
        \Log::info("In createUser method");
        $token = env("CVS_WS_TOKEN");
        $apiHost = env("CVS_WS_URL");
        // $realm = session()->get('realm'); // no access to realm when called from job
        \Log::info("CanvasAPI::createUser was started for:".$netid);
        //\Log::info((strpos($email, '@cornell.edu')));
        //\Log::info((strpos($email, '@med.cornell.edu')));
        // if($realm == env('CU_REALM')) {
        if (strpos($email, '@cornell.edu') !== false) {
            $integration_id = $netid . "-cornell-canvastools";
            $login_id = $netid;
            $user_id=$netid;
            $authentication_provider_id=5;
            \Log::info("Cornell netid is ".$netid);
            \Log::info("Cornell integration_id is ".$integration_id);
            \Log::info("Cornell login_id is ".$login_id);
            \Log::info("Cornell user_id is ".$user_id);
        }else {
            //if(strpos($netid, '@wcmc')) {
            if (strpos($email, '@med.cornell.edu') !== false) {
                $integration_id = $netid . "-cu_weill-canvastools";
                $login_id = $email;
                //$user_id=$netid."@cumed";
                $user_id=$netid;
                $authentication_provider_id=41;
                \Log::info("Weill netid is ".$netid);
                \Log::info("Weill integration_id is ".$integration_id);
                \Log::info("Weill login_id is ".$login_id);
                \Log::info("Weill user_id is ".$user_id);
            }
        }

        $client = new Client();
        $index=null;
        do{
        try {
            $tryAgain = false;
            $response = $client->request("POST", $apiHost."accounts/1/users", [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
                'http_errors' => true,
            ],
            'form_params' => [
                'user[name]'    => $firstName.' '.$lastName,
                'communication_channel[type]' => "email",
                'communication_channel[address]'   => $email,
                'user[login_id]'      => $login_id,
                'user[user_id]'       => $user_id,
                'pseudonym[integration_id]'=> $integration_id.$index,
                'user[status]'        => "active",
                'pseudonym[authenication_provider_id]' => $authentication_provider_id,
                'pseudonym[unique_id]' => $user_id,
            ]
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $tryAgain = false;
            if (strpos($email, 'is already in use') !== false) {
                $tryAgain = true;
                $index=$index++;
            }
        } catch(Exception $e) {
            $tryAgain = false;
            \Log::info($e->getMessage());
            \Log::info("Canvas failure in account creation");
          return false;
        }
        } while($tryAgain);
        \Log::info("Got here and response is : ".$response);
        $results = json_decode($response->getBody(), true);
        
        \Log::info("CanvasAPI::createUser: ".$netid." was created successfully ");

        return true;

    }


    /**
     * createCourse() - function to create course
     *
     * @param $courseId
     * @param $courseName
     *
     * @throws \Exception
     * @return boolean
     */
    public static function createCourse($courseId,$courseName,$netid) {
        $params="accounts/51/courses?course[name]=".$courseName."&course[code]=".$courseId."&course[term_id]=46"."&course[is_public]=false";
        //$results = apiCall('post', $params,$form_params[]);
        $results = (new self)->apiCall('post', $params);
        $enrolled = (new self)->enrollUser($netid, $results["id"]);
        return true;
        //return courseId;
    }

    /**
     * enrollUser() - function to enroll a given user in a given course
     *
     * @param $netid
     * @param $courseId
     *
     * @throws \Exception
     * @return boolean
     */
    public static function enrollUser($netid, $courseId) {
        $userID=(new self)->getUserID($netid);
        $params="courses/".$courseId."/enrollments?enrollment[user_id]=".$userID."&enrollment[type]=TeacherEnrollment&enrollment[enrollment_state]=active&enrollment[limit_privileges_to_course_section]=false&enrollment[notify]=false";
        $results = (new self)->apiCall('post', $params);
        return true;
        //return courseId;

    }


}
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
                'search_term'   => $netid,
            ]
        ]);
        $results = json_decode($response->getBody(), true);

        if(isset($results[0]["id"])) {
            //dd($results);
            \Log::info("User exists: ".$results[0]["id"]);
            return true;
        }
        else {
            \Log::info("Failed findUser $netid. ");
            return false;
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
            \Log::info("Course exists: ".$results["id"]);
            return true;
        }
        else {
            \Log::info("Failed findCourse. ");
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
        $token = env("CVS_WS_TOKEN");
        $apiHost = env("CVS_WS_URL");
        // $realm = session()->get('realm'); // no access to realm when called from job
        \Log::info("CanvasAPI::createUser: email is ".$email);
        // if($realm == env('CU_REALM')) {
        if (strpos($email, '@cornell.edu')) {
            $integration_id = $netid . "-cornell-canvastools";
            $login_id = $netid;
            $user_id=$netid;
            $authentication_provider_id=5;
        }else {
            //if(strpos($netid, '@wcmc')) {
            if (strpos($email, '@med.cornell.edu')) {
                $integration_id = $netid . "-cu_weill-canvastools";
                $login_id = $email;
                $user_id=$netid."@cumed";
                $authentication_provider_id=41;
            }
        }

        $client = new Client();
        $response = $client->request("POST", $apiHost."accounts/1/users", [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
                'http_errors' => true,
            ],
            'form_params' => [
                'user[name]'    => $firstName.' '.$lastName,
                'user[email]'   => $email,
                'user[login_id]'      => $login_id,
                'user[user_id]'       => $user_id,
                'user[integration_id]'=> $integration_id,
                'user[status]'        => "active",
                'user[authenication_provider_id]' => $authentication_provider_id,
                'pseudonym[unique_id]' => $email,

            ]
        ]);
        $results = json_decode($response->getBody(), true);

        \Log::info("CanvasAPI::createUser: results ".$response->getBody());
        
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
    public static function createCourse($courseId,$courseName) {
        $params="accounts/51/courses?course[name]=".$courseName."&course[code]=".$courseId."&course[term_id]=46"."&course[is_public]=false";
        //$results = apiCall('post', $params,$form_params[]);
        $results = (new self)->apiCall('post', $params);
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
        $params="accounts/51/courses".$courseId."/enrollments"."&enrollment[user_id]=".$netid."&enrollment[type]=TeacherEnrollment&enrollment[enrollment_state]=active&enrollment[limit_privileges_to_course_section]=false&enrollment[notify]=false";
        $results = (new self)->apiCall('post', $params);
        return true;
        //return courseId;

    }


}
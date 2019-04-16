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
     * @throws
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
                'search_term'   => str_replace("@cumed", "", $netid),
            ]
        ]);
        $results = json_decode($response->getBody(), true);

        if(isset($results[0]["id"])) {
            //\Log::info("User $netid exists in Canvas.");
            return true;
        }
        else {
            //\Log::info("Did not find $netid in Canvas. ");
            return false;
        }

    }

    /**
     * getUserID() - function to get Canvas user ID given a netid
     *
     * @param $netid
     *
     * @throws
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
     * @throws
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

        if(isset($results[0]["id"])) {
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
     * @throws
     * @return boolean
     */
    public static function createUser($firstName, $lastName,$email, $netid) {
        $token = env("CVS_WS_TOKEN");
        $apiHost = env("CVS_WS_URL");
        \Log::info("CanvasAPI::createUser was started for:".$netid);
        if (strpos($email, '@cornell.edu') !== false) {
            $integration_id = $netid . "-cornell-canvastools";
            $login_id = $netid;
            $user_id=$netid;
            $authentication_provider_id=5;
        }else {
            if (strpos($email, '@med.cornell.edu') !== false) {
                $integration_id = $netid . "-cu_weill-canvastools";
                $login_id = $email;
                //$user_id=$netid."@cumed";
                $user_id=$netid;
                $authentication_provider_id=41;
            }
        }

        $client = new Client();
        try {
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
                'pseudonym[integration_id]'=> $integration_id,
                'user[status]'        => "active",
                'pseudonym[authenication_provider_id]' => $authentication_provider_id,
                'pseudonym[unique_id]' => $user_id,

            ]
        ]);
        $results = json_decode($response->getBody(), true);
        } catch(Exception $e) {
          Log::error("Canvas failure in account creation");
          return false;
        }
        \Log::info("CanvasAPI::createUser: ".$netid." was created successfully ");
        return true;
    }


    /**
     * createCourse() - function to create course
     *
     * @param $courseId
     * @param $courseName
     * @param $netid
     *
     * @throws
     * @return boolean
     */
    public static function createCourse($courseId,$courseName,$netid) {
        $token = env("CVS_WS_TOKEN");
        $apiHost = env("CVS_WS_URL");
        $client = new Client();
        try {
            $response = $client->request("POST", $apiHost."accounts/51/courses", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept'        => 'application/json',
                    'http_errors' => true,
                ],
                'form_params' => [
                    'course[name]'    => $courseName,
                    'course[course_code]' => $courseId,
                    'course[integration_id]'   => $courseId."-canvastools",
                    'course[term_id]'      => 46,
                    'course[is_public]'       => "false",
                    'blueprint_course_id'=> "user-created-course-blueprint",
                ]
            ]);
            $results = json_decode($response->getBody(), true);
        } catch(Exception $e) {
            Log::error("Canvas failure in course creation");
            return false;
        }
        try {
            $enrolled = (new self)->enrollUser($netid, $results["id"]);
        } catch(Exception $e) {
            Log::error("Canvas failure in teacher enrollment");
        }
        \Log::info("CanvasAPI::createCourse: ".$courseName." was created successfully ");
        return true;
    }

    /**
     * enrollUser() - function to enroll a given user in a given course
     *
     * @param $netid
     * @param $courseId
     *
     * @throws
     * @return boolean
     */
    public static function enrollUser($netid, $courseId) {
        $token = env("CVS_WS_TOKEN");
        $apiHost = env("CVS_WS_URL");
        $client = new Client();
        $userID=(new self)->getUserID($netid);
        try {
            $response = $client->request("POST", $apiHost."courses/".$courseId."/enrollments", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept'        => 'application/json',
                    'http_errors' => true,
                ],
                'form_params' => [
                    'enrollment[user_id]'    => $userID,
                    'enrollment[type]' =>  "TeacherEnrollment",
                    'enrollment[enrollment_state]'   =>  "active",
                    'enrollment[limit_privileges_to_course_section]'      =>  "false",
                    'enrollment[notify]'       => "false",
                ]
            ]);
            $results = json_decode($response->getBody(), true);
        } catch(Exception $e) {
            Log::error("Canvas failure in user enrollment");
            return false;
        }
        \Log::info("User $netid was enrolled as a teacher in course $courseId.");
        return true;

    }


}
<?php
namespace App\Helpers;

use GuzzleHttp\Client;
use \App\Helpers\LDAP;

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
        $userCheck=false;
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
        
// Checking login id to see if it matches the given netid since the API calls returns partial matches too so we need to confirm that it is an exact match.
// Login ids for Weill are emails so we need to match the login id and netid@med.cornell.edu
    for($i = 0; $i < count($results); $i++) {
        if(isset($results[$i]["login_id"]) && ($results[$i]["login_id"] ==$netid || $results[$i]["login_id"]==str_replace("@cumed", "", $netid).'@med.cornell.edu' || $results[$i]["login_id"]==str_replace("@cumed", "", $netid).'@qatar-med.cornell.edu')) {
            $userCheck=true;
        }
    }
    if($userCheck){
        \Log::info("CanvasAPI::findUser - this method was called and $netid exists in Canvas");
            return true;
        }
        else {
        \Log::info("CanvasAPI::findUser - this method was called and $netid does not exist in Canvas");
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
        $userID=0;
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
        
// Checking login id to see if it matches the given netid since the API calls returns partial matches too so we need to confirm that it is an exact match.
// Login ids for Weill are emails so we need to match the login id and netid@med.cornell.edu
        for($i = 0; $i < count($results); $i++) {
            if(isset($results[$i]["login_id"]) && ($results[$i]["login_id"] ==$netid || $results[$i]["login_id"]==str_replace("@cumed", "", $netid).'@med.cornell.edu' || $results[$i]["login_id"]==str_replace("@cumed", "", $netid).'@qatar-med.cornell.edu')) {
                $userID=$results[0]["id"];
            }
        }
        if($userID!=0){
                return $results[0]["id"];
            }
            else {
                return 0;
            }
    }

    /**
     * getCourseID() - function to get Canvas course ID given a course code
     *
     * @param $course_code
     *
     * @throws
     * @return int
     */
    public static function getCourseID($course_code) {
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
                'search_term'   => $course_code,
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
            \Log::info("CanvasAPI::findCourse - this method was called and $courseId exists in Canvas");
            return true;
        }
        else {
            \Log::info("CanvasAPI::findCourse - this method was called and $courseId does not exist in Canvas");
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
        //\Log::info("CanvasAPI::createUser was started for:".$netid);
        if (strpos($email, 'med.cornell.edu') !== false) {
            $integration_id = $netid . "-cu_weill-canvastools";
            $login_id = $email;
            $user_id=$netid;
            $authentication_provider_id=41;
            $realm="CIT.CORNELL.EDU";
        }else {
            $integration_id = $netid . "-cornell-canvastools";
            $login_id = $netid;
            $user_id=$netid;
            $authentication_provider_id=5;
            $realm="A.WCMC-AD.NET";
        }
        $data = LDAP::data($netid, $realm);
        $emplid = $data["emplid"];
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
                'pseudonym[sis_user_id]'      => $emplid,
                'pseudonym[integration_id]'=> $integration_id,
                'user[status]'        => "active",
                'pseudonym[authentication_provider_id]' => $authentication_provider_id,
                'pseudonym[unique_id]' => $login_id,

            ]
        ]);
        $results = json_decode($response->getBody(), true);
        } catch(Exception $e) {
          Log::error("Canvas failure in account creation");
          return false;
        }
        //\Log::info("CanvasAPI::createUser: ".$netid." was created successfully ");
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
        try {
            $blueprinted = (new self)->blueprintCourse($courseId);
        } catch(Exception $e) {
            Log::error("Canvas failure in associating $courseId with blueprint course");
        }
        //\Log::info("CanvasAPI::createCourse: ".$courseName." was created successfully ");
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

    /**
     * blueprintCourse() - function to enroll a given user in a given course
     *
     * @param $courseId
     *
     * @throws
     * @return boolean
     */
    public static function blueprintCourse($courseId) {
        $token = env("CVS_WS_TOKEN");
        $apiHost = env("CVS_WS_URL");
        $client = new Client();
        $id = (new self)->getCourseID($courseId);
        try {
            $response = $client->request("PUT", $apiHost."courses/541/blueprint_templates/default/update_associations", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept'        => 'application/json',
                    'http_errors' => true,
                ],
                'form_params' => [
                    'course_ids_to_add'    => $id,
                ]
            ]);
            $results = json_decode($response->getBody(), true);
        } catch(Exception $e) {
            Log::error("Canvas failure in blueprint course association");
            return false;
        }
        \Log::info("Course $courseId was associated as a blueprint course.");
        return true;

    }


}
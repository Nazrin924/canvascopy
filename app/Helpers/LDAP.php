<?PHP

namespace App\Helpers;

use Log;

class LDAP

{

    public static function data($NetID, $realm)
    {
        //ldapsearch -x -H ldaps://140.251.11.85:636 -D "cn=bbtools ithaca,ou=binds,dc=weill,dc=cornell,dc=edu" -w "veew1Nirah_Sha*el5" -b "ou=people,dc=weill,dc=cornell,dc=edu"  "(weillCornellEduCWID=lur2015)"
        $ldappass = env("LDAP_PASS", "");
        $weillpass = env("WEILL_PASS", "");
        $ldaprdn = 'uid='.env("LDAP_USER","").',ou=Directory Administrators,o=Cornell University,c=US';
        $ldapweill="cn=bbtools ithaca,ou=binds,dc=weill,dc=cornell,dc=edu";
        //$ldapweill='uid='.env("LDAP_USER","").',o=med.cornell.edu,c=US';
        $ds=ldap_connect(env("LDAP_SERVER",""));
        ldap_set_option($ds, LDAP_OPT_NETWORK_TIMEOUT, 30); 
	$weill=ldap_connect(env("WEILL_LDAP",""));
	ldap_set_option($weill, LDAP_OPT_NETWORK_TIMEOUT, 30); 
        $result = array(
            'firstname'=>"",
            'lastname'=>"",
            'email'=>"",
            'canCreateSite'=>"",
            'emplid'=>""
        );
        $Email = "";
        if ($ds && $realm == env('CU_REALM'))
        {
            if ($ldappass == "") {
                $r = ldap_bind($ds);  // anonymous
            } else {
                $r = ldap_bind($ds, $ldaprdn, $ldappass);  // with bind id
            }
            $sr = ldap_search($ds,"ou=People,o=Cornell University,c=US","uid=$NetID");
            $info = ldap_get_entries($ds, $sr);
            if(isset($info[0]) && isset($info[0]["givenname"])) {
                $FirstName = $info[0]["givenname"][0];
            }
            else {
                $FirstName = "";
            }
            if(isset($info[0]) && isset($info[0]["sn"])) {
                $LastName = $info[0]["sn"][0];
            }
            else {
                $LastName = "";
            }
            if(isset($info[0]) && isset($info[0]["mail"])) {
                $Email = $info[0]["mail"][0];
            }
            else {
                $Email = "";
            }
            if(isset($info[0]) && isset($info[0]["cornelleduemplid"])) {
                $emplid = $info[0]["cornelleduemplid"][0];
            }
            else {
                $emplid = "";
            }
            if(isset($info[0]) && isset($info[0]["edupersonprimaryaffiliation"])) {
                $primary = $info[0]["edupersonprimaryaffiliation"][0];
            }
            else {
                $primary = "";
            }

            if($primary == "student") {
                $college = $info[0]["cornelleduacadcollege"][0];
            }
            else {
                $college = "";
            }
            //Determine if they're worthy of creating a course
            $canCreateCourse = $primary != ""
                && ($primary != "student"
                    || $college == "GR"
                    || $college == "LA"
                    || $college == "GM"
                    || $college == "VM");
            // Student may have exercised FERPA right to suppress name
            if (($FirstName == "") && ($LastName == "")) {
                $FirstName = "Cornell";
                $LastName  = "Student";
            }

        }
        if ($weill && $realm == "A.WCMC-AD.NET")
        {
            if ($weillpass == "") {
                $r = ldap_bind($weill);  // anonymous
            } else {
                $r=ldap_bind($weill,$ldapweill,$weillpass);  // with bind id
            }
            //$sr = ldap_search($weill,"ou=people,o=med.cornell.edu","uid=$NetID");
            $sr = ldap_search($weill,"ou=people,dc=weill,dc=cornell,dc=edu","weillCornellEduCWID=$NetID");
            $info = ldap_get_entries($weill, $sr);
            //$EmplID = $info[0]["cornelleduemplid"][0];
            if(isset($info[0]) && isset($info[0]["givenname"])) {
                $FirstName = $info[0]["givenname"][0];
            }
            else {
                $FirstName = "";
            }
            if(isset($info[0]) && isset($info[0]["sn"])) {
                $LastName = $info[0]["sn"][0];
            }
            else {
                $LastName = "";
            }
            if(isset($info[0]) && isset($info[0]["mail"])) {
                $Email = $info[0]["mail"][0];
            }
            else {
                $Email = "";
            }
            if(isset($info[0]) && isset($info[0]["cornelleduemplid"])) {
                $emplid = $info[0]["cornelleduemplid"][0];
            }
            else {
                $emplid= "";
            }

            $college = "";
            //Determine if they're worthy of creating a course
            $canCreateCourse = true;
            // Student may have exercised FERPA right to suppress name
            if (($FirstName == "") && ($LastName == "") && ($Email != null)) {
                $FirstName = "Weill";
                $LastName  = "Cornell";
            }

            Log::info("According to LDAP, the Weill user's name is $FirstName $LastName with email $Email");

        }

        if($Email != null) {
            $result = array(
                'firstname'=>$FirstName,
                'lastname'=>$LastName,
                'email'=>$Email,
                'canCreateSite'=>$canCreateCourse,
                'emplid'=>$emplid
            );
        }
        return $result;
    }

    public static function getADGroups($NetID, $realm)
    {
        try {
            $server = env("LDS_AD_HOST");
            $port = env("LDS_AD_PORT",636);

            $username = env("LDS_AD_USERNAME","");
            $password = env("LDS_AD_PASSWORD","");

            $ldaprdn = "cn=$username,ou=BindIDs,o=Cornell University,c=US";

            $ds = ldap_connect($server,$port);
	    ldap_set_option($ds, LDAP_OPT_NETWORK_TIMEOUT, 30); 
            if(!$ds) {
                return [];
            }

            $r = ldap_bind($ds, $ldaprdn, $password);
            if(!$r) {
                return [];
            }

            $sr = ldap_search($ds,"o=Cornell University, c=US","name=$NetID");
            $info = ldap_get_entries($ds, $sr);

            $shortGroupNames = array();

            if($info) {
                if(isset($info['count']) && $info['count'] == 0) {
                    //Log::info("NetID not found in Active Directory");
                    return $shortGroupNames;
                }

                if(isset($info[0]) && isset($info[0]["memberof"])) {
                    for ($i = 0; $i < $info[0]['memberof']['count']; $i++)
                    {
                        $x = $info[0]['memberof'][$i];
                        $x= substr($x, 0, strpos($x, ","));
                        if(substr($x,0,3) == "CN=") {
                            $x = substr($x,3);
                        }
                        $shortGroupNames[$i] = $x;
                    }
                }
            }
            return $shortGroupNames;
        } catch(Exception $e) {
            return [];
        }
    }
}

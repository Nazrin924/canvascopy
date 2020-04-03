<?php namespace App\Helpers;

class Testers{

	public static function check($netID, $realm){
        try {
            $groups = LDAP::getADGroups($netID ,$realm);
        } catch(Exception $e) {
            return Redirect::route('ldapError');
        }
        $inGroup = in_array(env("AD_GROUP"),$groups);
        $inGroup = $inGroup || ($netID == env('TEST_NETID'));
        return $inGroup;


		if(file_exists(storage_path()."/testers.json")) {
			$info = fopen(storage_path()."/testers.json", "r");
			$testers = json_decode(fread($info, filesize(storage_path()."/testers.json")));
			fclose($info);

			return in_array($netID , $testers);
		}
	}

	public static function add($netID){
		if(file_exists(storage_path()."/testers.json")) {
			$info = fopen(storage_path()."/testers.json", "r");
			$testers = json_decode(fread($info, filesize(storage_path()."/testers.json")));
			fclose($info);

			if(!in_array($netID , $testers)) {
				array_push($testers,$netID);
      }

			$info = fopen(storage_path()."/testers.json", "w");
			fwrite($info,json_encode($testers));
			fclose($info);

			return $testers;
		}
	}

	public static function remove($netID){
		if(file_exists(storage_path()."/testers.json")) {
			$info = fopen(storage_path()."/testers.json", "r");
			$testers = json_decode(fread($info, filesize(storage_path()."/testers.json")));
			fclose($info);

			if(in_array($netID, $testers)) {
				array_splice($testers, array_search($netID,$testers),1);
      }

			$info = fopen(storage_path()."/testers.json", "w");
			fwrite($info,json_encode($testers));
			fclose($info);

			return $testers;
		}
	}

	public static function getTesters(){
		if(file_exists(storage_path()."/testers.json")) {
			$info = fopen(storage_path()."/testers.json", "r");
			$testers = json_decode(fread($info, filesize(storage_path()."/testers.json")));
			fclose($info);

			return $testers;
		}
	}
}

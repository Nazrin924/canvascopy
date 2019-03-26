<?PHP
namespace App\Helpers;

require 'services/Service.php';
require 'services/Context.php';
use services\Service;
use services\Announcement;
use services\Calendar;
use services\Content;
use services\Context;
use services\Course;
use services\Coursemembership;
use services\Gradebook;
use services\User;
use services\Util;

use Monolog;

class BbPhp
{

	/*
	 * BbPHP: Blackboard Web Services Library for PHP
	 * Copyright (C) 2011 by St. Edward's University (www.stedwards.edu)
	 *
	 * Licensed under The MIT License
	 * Redistributions of files must retain the above copyright notice.
	 */

	private $session_id = null;
	private $services = array(
    'Announcement',
    'Calendar',
    'Content',
    'Context',
    'Course',
    'CourseMembership',
    'Gradebook',
    'NotificationDistributorOperations',
    'User',
    'Util');
	public $url = null;
	public $use_curl = true;

	public function __construct($url = null, $use_curl = true) {
		$this->url = $url;
		$this->use_curl = $use_curl;
		$this->session_id = $this->Context("initialize");
    $var = $this->Context("login");
	}

	public function getSessionID() {
		return $this->session_id;
	}

	private function buildHeader() {
		if ($this->session_id == null) {
			$password = 'nosession';
		} else {
			$password = $this->session_id;
		}

	  $username = "session";
	  $time = gmdate("Y-m-d\TH:i:s\Z");
	  $timeplusone = gmdate("Y-m-d\TH:i:s\Z", time() + 10);

		/**
		 * This header is sensitive to line breaks and you should avoid
		 * letting things like Eclipse do auto-formatting on it.
		 *
		 **/
		$header = <<<END
		<SOAP-ENV:Header>
	  	<wsse:Security SOAP-ENV:mustunderstand="true" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" >
	  		<wsu:Timestamp>
	  		<wsu:Created>$time</wsu:Created>
	  		<wsu:Expires>$timeplusone</wsu:Expires>
	  		</wsu:Timestamp>
	  		<wsse:UsernameToken>
	  		<wsse:Username>$username</wsse:Username>
	  		<wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText" >$password</wsse:Password>
	  		</wsse:UsernameToken>
	  	</wsse:Security>
	  </SOAP-ENV:Header>
END;
		return $header;
	}

	private function buildRequest($method = null, $service, $args = null) {
		$header = $this->buildHeader();
		$service = "\App\Helpers\services\\".$service;

		$serviceObject = new $service();

		$body = $serviceObject->$method($args);

		$request = '<?xml version="1.0" encoding="UTF-8"?>
    <SOAP-ENV:Envelope
     xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
     xmlns:xsd="http://www.w3.org/2001/XMLSchema"
     xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
		$request .= $header.$body;
		$request .= '</SOAP-ENV:Envelope>';

		return $request;
	}

	/**
	 * The call() magic method is used here to access
   * methods from BB that have not been defined in the class.
	 * In theory you can access any method that the BB
   * object provides if you have added it to your "Context".
	 */
	public function __call($method, $args) {
		if (!isset($args[1])) {
			$args[1] = null;
		}

		if (in_array($method, $this->services)) {
			if(isset($args[2])) return $this->doCall($args[0], $method, $args[1], $args[2]);
			return $this->doCall($args[0], $method, $args[1]);
		} else {
			if(isset($args[2])) return $this->doCall($args[0], $method, $args[1], $args[2]);
			return $this->doCall($method, $args[0], $args[1]);
		}
	}

	public function doCall($method = null, $service = "Context", $args = null, $netID = "") {

		$request = $this->buildRequest($method, $service, $args);

    // if($service == "Course") {
  	// 	\Log::info("Request is $request");
  	// 	\Log::info("NetID being passed in is $netID");
    // }

		if ($this->use_curl) {
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, "$this->url/webapps/ws/services/$service.WS");
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_HTTPHEADER,
      array('Content-type: text/xml; charset=utf-8', 'SOAPAction: "' . $method . '"'));
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_COOKIEJAR,
		    storage_path().'/'.$netID.'_'.'cookies.txt');
		  curl_setopt($ch, CURLOPT_COOKIEFILE,
		    storage_path().'/'.$netID.'_'.'cookies.txt');

			$result = curl_exec($ch);

			curl_close($ch);
		}
    else {
			$result = $this->doPostRequest("$this->url/webapps/ws/services/$service.WS",
       $request,
       "Content-type: text/xml; charset=utf-8\nSOAPAction: \"" . $method . "\"");
    }

    if(isset($result)) {
  		$result_array = $this->xmlstr_to_array($result);
    }
    else $result_array = [];

    // if($service == "Course") {
    //   \Log::info("Result: ".print_r($result, true));
    // }

		if(isset($result_array['Body']['Fault']))
		{
			if(isset($result_array['Body']['Fault']['faultstring']))
				\Log::info($result_array['Body']['Fault']['faultstring']);
		}
		$final_result
			= (isset($result_array['Body'][$method . 'Response']['return'])) ?
			$result_array['Body'][$method . 'Response']['return'] : null;
		return $final_result;
	}

	/*
	 * This function can be found here:
	 * http://wezfurlong.org/blog/2006/nov/http-post-from-php-without-curl/
	 */
	private function doPostRequest($url, $data, $optional_headers = null) {
		$params = array('http' => array(
		            'method' => 'POST',
		            'content' => $data
		          ));
		if ($optional_headers !== null) {
			$params['http']['header'] = $optional_headers;
		}
		$ctx = stream_context_create($params);
		$fp = @fopen($url, 'rb', false, $ctx);
		if (!$fp) {
			throw new Exception("Problem with $url, $php_errormsg");
		}
		$response = @stream_get_contents($fp);
		if ($response === false) {
			throw new Exception("Problem reading data from $url, $php_errormsg");
		}
		return $response;
	}

	function xmlstr_to_array($xmlstr) {
		$doc = new \DOMDocument();
		if($xmlstr != "") $doc->loadXML($xmlstr);
		return $this->domnode_to_array($doc->documentElement);
	}

	function domnode_to_array($node) {
		$output = array();
    if($node == null) return $output;

		switch ($node->nodeType) {
			case XML_CDATA_SECTION_NODE:
			case XML_TEXT_NODE:
				$output = trim($node->textContent);
				break;
			default:
				for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
					$child = $node->childNodes->item($i);
					$v = $this->domnode_to_array($child);
					if(isset($child->tagName)) {
						$t = $child->tagName;
						if (strpos($t, ':') !== false) {
							$temp = explode(':', $t);
							$key = $temp[1];
						} else {
							$key = $t;
						}
						$test = substr($t, strpos($t, ':') + 1, strlen($t) - strpos($t, ':') + 1);
						if(!isset($output[$key])) {
							$output[$key] = array();
						}
						$output[$key][] = $v;
					} elseif($v) {
						$output = (string) $v;
					}
				}

				if(is_array($output)) {
					if($node->attributes->length) {
						$a = array();
						foreach($node->attributes as $attrName => $attrNode) {
							$a[$attrName] = (string) $attrNode->value;
						}
						$output['@attributes'] = $a;
					}
					foreach ($output as $t => $v) {
						if(is_array($v) && count($v)==1 && $t!='@attributes') {
							$output[$t] = $v[0];
						}
					}
				}
			break;
		}
		return $output;
	}

}
?>

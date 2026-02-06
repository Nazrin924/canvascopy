<?php

$username = 'session';
$password = 'nosession';
$time = gmdate("Y-m-d\TH:i:s\Z");
$timeplusone = gmdate("Y-m-d\TH:i:s\Z", time() + 10);

$request = <<<REQUEST_BODY
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
   <soapenv:Header>
    <wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd"
               xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
      <wsu:Timestamp>
        <wsu:Created>$time</wsu:Created>
        <wsu:Expires>$timeplusone</wsu:Expires>
      </wsu:Timestamp>
      <wsse:UsernameToken>
        <wsse:Username>$username</wsse:Username>
        <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText" >$password</wsse:Password>
      </wsse:UsernameToken>
    </wsse:Security>
</soapenv:Header>
   <soapenv:Body/>
</soapenv:Envelope>
REQUEST_BODY;

echo 'REQUEST:<br>';
echo $request;

// $url = Config::get('app.ps_url');
$url = 'https://cornell-test.blackboard.com:443/webapps/ws/services/Context.WS';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: text/xml',
    'Content-Length: '.strlen($request),
    'SOAPAction: initialize']);
$result = curl_exec($ch);
echo '<br>'.$url.'<br>';
// print "<br>".$result."<br>";

$doc = new SimpleXMLElement(strstr($result, '<?xml'));
$doc->registerXPathNamespace('ns', 'http://context.ws.blackboard');

$dom = new DOMDocument('1.0');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->loadXML($doc->asXML());
$xmlstring = $dom->saveXML(); // formatted xml string
echo 'RESPONSE:<br>';
echo '<br>'.$xmlstring.'<br>';

$response['xmlstring'] = $xmlstring;

$response = (string) $doc->xpath('//ns:return')[0];
$sessionid = $response;

// /               END OF PART ONE

// Now, create a login object.
$username = 'session';
$password = $sessionid;
$time = gmdate("Y-m-d\TH:i:s\Z");
$timeplusone = gmdate("Y-m-d\TH:i:s\Z", time() + 10);
echo 'Time is '.$time.'<br>';

$loginusername = 'cuwsguest';
$loginpassword = 'cuwsguest';

$request = <<<REQUEST_BODY
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:con="http://context.ws.blackboard">
  <soapenv:Header>
    <wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd"
               xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
      <wsu:Timestamp>
        <wsu:Created>$time</wsu:Created>
        <wsu:Expires>$timeplusone</wsu:Expires>
      </wsu:Timestamp>
      <wsse:UsernameToken>
        <wsse:Username>$username</wsse:Username>
        <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText" >$password</wsse:Password>
      </wsse:UsernameToken>
    </wsse:Security>
</soapenv:Header>
   <soapenv:Body>
      <con:login>
         <!--Optional:-->
         <con:userid>$loginusername</con:userid>
         <!--Optional:-->
         <con:password>$loginpassword</con:password>
         <!--Optional:-->
         <con:clientVendorId>blackboard</con:clientVendorId>
         <!--Optional:-->
         <con:clientProgramId>Blackboard Inc.</con:clientProgramId>
         <!--Optional:-->
         <con:loginExtraInfo>true</con:loginExtraInfo>
         <!--Optional:-->
         <con:expectedLifeSeconds>140000</con:expectedLifeSeconds>
      </con:login>
   </soapenv:Body>
</soapenv:Envelope>
REQUEST_BODY;

echo 'REQUEST:<br>';
echo '<br>'.$request.'<br>';

// $url = Config::get('app.ps_url');
$url = 'https://cornell-test.blackboard.com:443/webapps/ws/services/Context.WS';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: text/xml',
    'Content-Length: '.strlen($request),
    'SOAPAction: login']);
$result = curl_exec($ch);
echo '<br>'.$url.'<br>';
// print "<br>".$result."<br>";

$doc = new SimpleXMLElement(strstr($result, '<?xml'));
$doc->registerXPathNamespace('ns', 'http://context.ws.blackboard');

$dom = new DOMDocument('1.0');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->loadXML($doc->asXML());
$xmlstring = $dom->saveXML(); // formatted xml string
echo 'RESPONSE:<br>';
echo '<br>'.$xmlstring.'<br>';

$loggedin = $doc->xpath('//ns:return')[0];

echo '<br>Logged in: '.$loggedin.'<br>';

// OK, now I'm logged in
// Let's try to get a course:

$url = 'https://cornell-test.blackboard.com:443/webapps/ws/services/Course.WS';
$time = gmdate("Y-m-d\TH:i:s\Z");
$timeplusone = gmdate("Y-m-d\TH:i:s\Z", time() + 10);

$request = <<< REQUEST_BODY
	<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:cour="http://course.ws.blackboard" xmlns:xsd="http://course.ws.blackboard/xsd">
<soapenv:Header>
	<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd"
	       xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
		<wsu:Timestamp>
		<wsu:Created>$time</wsu:Created>
		<wsu:Expires>$timeplusone</wsu:Expires>
		</wsu:Timestamp>
		<wsse:UsernameToken>
		<wsse:Username>$username</wsse:Username>
		<wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText" >$password</wsse:Password>
		</wsse:UsernameToken>
	</wsse:Security>
</soapenv:Header>
	   <soapenv:Body>
	      <cour:getCourse>
	         <!--Optional:-->
	         <cour:filter>
	            <xsd:courseIds>testing-NazrinTesting-Spring2015</xsd:courseIds>
	            <xsd:filterType>1</xsd:filterType>
	         </cour:filter>
	      </cour:getCourse>
	   </soapenv:Body>
	</soapenv:Envelope>
REQUEST_BODY;

echo 'REQUEST:<br>';
echo "<br>$request<br>";
// systemRoles was USER
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: text/xml',
    'Content-Length: '.strlen($request),
    'SOAPAction: getCourse']);
$result = curl_exec($ch);
echo '<br>'.$url.'<br>';

$doc = new SimpleXMLElement($result);
$doc->registerXPathNamespace('userdata', 'http://user.ws.blackboard/xsd');
$dom = new DOMDocument('1.0');
$dom->preserveWhiteSpace = true;
$dom->formatOutput = true;
$dom->loadXML($doc->asXML());
$xmlstring = $dom->saveXML(); // formatted xml string
echo 'RESPONSE:<br>';
echo '<br>'.$xmlstring.'<br>';

//  END OF GETCOURSE TEST

$return = $doc->xpath('//userdata:name');
if (strlen($xmlstring) > 0) {
    echo "Course $name is found<br>";
} else {
    echo 'Course not found<br>';
}

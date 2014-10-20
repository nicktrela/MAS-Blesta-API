<?php

$primary_id = $_POST["domain_id"];

echo 'variable primary id '.$primary_id.' <br />';

require 'soap_config.php';


$client = new SoapClient(null, array('location' => $soap_location,
		'uri'      => $soap_uri,
		'trace' => 1,
		'exceptions' => 1));


try {
	if($session_id = $client->login($username, $password)) {
		echo 'Login successful. Session ID:'.$session_id.'<br />';
	}

	//* Set the function parameters.
	$status = 'inactive';
// 	$primary_id = 50;

	$record_record = $client->sites_web_domain_set_status($session_id, $primary_id, $status);

	print_r($record_record);
	echo "<br>";

	if($client->logout($session_id)) {
		echo 'Logged out.<br />';
	}


} catch (SoapFault $e) {
	echo $client->__getLastResponse();
	die('SOAP Error: '.$e->getMessage());
}

?>

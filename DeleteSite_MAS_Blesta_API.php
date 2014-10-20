<?php

$domain_id = $_POST["domain_id"];
$database_id = $_POST["database_id"];
$database_user_id = $_POST["database_user_id"];

require 'soap_config.php';


$client = new SoapClient(null, array('location' => $soap_location,
		'uri'      => $soap_uri,
		'trace' => 1,
		'exceptions' => 1));


try {
	if($session_id = $client->login($username, $password)) {
		echo 'Login successful. Session ID:'.$session_id.'<br />';
	}

	//* Parameters
// 	$domain_id = 2;


	//* Delete the web domain record
	$affected_rows = $client->sites_web_domain_delete($session_id, $domain_id);

	echo "Number of sites that have been deleted: ".$affected_rows."<br>";
	
	//* Parameters
// 	$database_id = 1;


	//* Get the database record
	$affected_rows = $client->sites_database_delete($session_id, $database_id);

	echo "Number of databases that have been deleted: ".$affected_rows."<br>";

	//* Parameters
// 	$database_user_id = 1;


	//* Get the database record
	$affected_rows = $client->sites_database_user_delete($session_id, $database_user_id);

	echo "Number of records that have been deleted: ".$affected_rows."<br>";


	if($client->logout($session_id)) {
		echo 'Logged out.<br />';
	}


} catch (SoapFault $e) {
	echo $client->__getLastResponse();
	die('SOAP Error: '.$e->getMessage());
}

?>
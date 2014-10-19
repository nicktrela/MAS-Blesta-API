<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// $domain_variable = "'"+$_POST['domain_name']+"'";

$domain_variable = $_POST['domain_name'];

$db_user = str_replace('.com', '_com_u', $domain_variable); 

$db_user = "'"+$db_user+"'";

$db_name = str_replace('.com', '_com_db', $domain_variable); 

$db_name = "'"+$db_name+"'";

$domainName = "'"+$domain_variable+"'";

$domain_id = 1;

function generatePassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $count = mb_strlen($chars);

    for ($i = 0, $result = ''; $i < $length; $i++) {
        $index = rand(0, $count - 1);
        $result .= mb_substr($chars, $index, 1);
    }

    return $result;
}

$db_pass = generatePassword();

echo 'db pass '.$db_pass.' <br />';

require 'soap_config.php';

$client = new SoapClient(null, array('location' => $soap_location,
		'uri'      => $soap_uri,
		'trace' => 1,
		'exceptions' => 1));


try {
	if($session_id = $client->login($username, $password)) {
		echo 'Login successful. Session ID: '.$session_id.'<br />';
	}

	//* Set the function parameters.
	$client_id = 1;
	$params_website = array('server_id' => 1,  
                            'ip_address' => '*',  
                            'domain' => $domain_variable,  
                            'type' => 'vhost',  
                            'parent_domain_id' => '',  
                            'vhost_type' => 'name',  
                            'hd_quota' => -1,  
                            'traffic_quota' => '-1',  
                            'cgi' =>'n',  
                            'ssi' =>'n',  
                            'suexec' =>'y',  
                            'errordocs' =>'1',  
                            'subdomain' =>'none',  
                            'ssl' =>'y',  
                            'php' =>"mod",  
                            'ruby' =>'n',  
                            'active' =>'y',  
                            'redirect_type' =>'no',  
                            'redirect_path' =>'',  
                            'ssl_state' =>'California',  
                            'ssl_organisation' =>'Questa Volta',  
                            'ssl_organisation_unit' =>'Web',  
                            'ssl_country' =>'United States',  
                            'ssl_domain' => $domain_variable,  
                            'ssl_request' =>'',  
                            'ssl_cert' =>'',  
                            'ssl_bundle' =>'',  
                            'ssl_action' =>'create',    
                            'stats_password' =>'',  
                            'stats_type' =>'webalizer',  
                            'backup_interval' =>'daily',  
                            'backup_copies' =>'7',  
                            'document_root' => '/var/www/clients/client'.$client_id.'/web'.$domain_id,
                            'system_user' =>'web1',  
                            'system_group' =>'client2',  
                            'allow_override' =>'All',  
			    			'pm' => 'dynamic',
			    			'pm.min_spare_servers' => 1,
		            		'pm.max_spare_servers' => 5,
			    			'pm_process_idle_timeout' => 10,
		 	    			'pm_max_requests' => 0,
			    		    'pm.start_servers' => 2,
                            'php_open_basedir' => '/var/www/clients/client'.$client_id.'/web'.$domain_id.'/web:/var/www/clients/client'.$client_id.'/web'.$domain_id.'/private:/var/www/clients/client'.$client_id.'/web'.$domain_id.'/tmp:/var/www/hostname/web:/srv/www/hostname/web:/usr/share/php5:/usr/share/php:/tmp:/usr/share/phpmyadmin:/etc/phpmyadmin:/var/lib/phpmyadmin',
                            'custom_php_ini' =>'',   
                            'apache_directives' => '<Directory /> 
                                        Options FollowSymLinks 
                                        AllowOverride All 
                                        Order allow,deny 
                                        Allow from all 
                                        </Directory>',  
                            'client_group_id' =>$client_id +1 
                            );  
     
    $website_id = $client->sites_web_domain_add($session_id, $client_id, $params_website);  
	echo "Web Domain ID: ".$website_id."<br>";

// Add db user //////

		$params = array(
		'server_id' => 1,
		'database_user' => $db_user,
		'database_password' => $db_pass
	);

	$database_user_id = $client->sites_database_user_add($session_id, $client_id, $params);
	echo "Database User ID: ".$database_user_id."<br>";

// Add db /////

	$params = null; // Clear params
	
		$params = array(
		'server_id' => '1',
		'type' => 'mysql',
		'parent_domain_id' => $website_id,
		'database_name' => $db_name,
		'database_user_id' => $database_user_id,
		'database_ro_user_id' => '0',
		'database_charset' => 'UTF8',
		'remote_access' => 'n',
		'remote_ips' => '',
		'backup_interval' => 'none',
		'backup_copies' => 1,
		'active' => 'y'
	);

	$database_id = $client->sites_database_add($session_id, $client_id, $params);
	
		
	echo "Database ID: ".$database_id."<br>";

	
	if($client->logout($session_id)) {
		echo 'Logged out.<br />';
	}


} catch (SoapFault $e) {
	echo $client->__getLastResponse();
	die('SOAP Error: '.$e->getMessage());
}
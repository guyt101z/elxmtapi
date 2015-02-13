<?php

date_default_timezone_set('America/Argentina/Buenos_Aires');

// Invokes libraries
include_once "libs/nusoap.php";
include_once "libs/JSON.php";

$see_results    = true;
$production     = false;

if($production){
    $uri = 'http://191.167.176.161/elxmtapi/elxmtapi.php?wsdl';
}else{
    $uri = 'http://127.0.0.1/elxmtapi/elxmtapi.php?wsdl';
}

// Instantiate an object JSON
$json = new Services_JSON();

// Create customer interface
$client = new nusoap_client($uri, true, '', '', '', '', '');

// Method 1.- Login
$result  = $client->call('Login', array('user' => 'fpereira', 'password' => 'iperfex') );
$oResult = $json->decode($result);

if($see_results){
    echo "Result Login:\n";
    print_r($oResult);
}

// Wonder if you did Login
if(isset($oResult->chain_session) && $oResult->chain_session != ''){
    $chain_session = $oResult->chain_session;
}else{
    echo "Unable to login.\n";
    exit();
}

// Method 5.- CreateOrganization
$result  = $client->call('CreateOrganization', array('chain_session' => $chain_session,
                                                        'name'           => 'Elastix', // Organization
                                                        'domain'         => 'elastix.org', // Domain Name
                                                        'country'        => 'Ecuador', // Country
                                                        'city'           => 'Guayaquil', // City
                                                        'address'        => 'Doctor Teodoro Maldonado Carbo 222', // Address
                                                        'country_code'   => 593,// Country Code. More info http://countrycode.org
                                                        'area_code'      => 4, // Area Code. More info http://www.howtocallabroad.com/ecuador
                                                        'quota'          => 30, // Email Quota By User(MB). By default 30MB.
                                                        'email_contact'  => 'fpereira@elastix.org', // Email Contact
                                                        'max_num_user'   => 0, // Max. # of User Accounts. The value 0 equals unlimited
                                                        'max_num_exten'  => 0, // Max. # of extensions. The value 0 equals unlimited.
                                                        'max_num_queues' => 0, // Max. # of queues. The value 0 equals unlimited.
                                                        'admin_password' => 'PAkaPata822Ozu') // Admin Password.

                        );

$oResult = $json->decode($result);

if($see_results){
    echo "Result CreateOrganization:\n";
    print_r($oResult);
}


// Method 4.- Logout
$result = $client->call('Logout', array('chain_session' => $chain_session));

if($see_results){
    echo "Result Logout:\n";
    print_r($json->decode($result));
}

?>

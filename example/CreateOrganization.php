<?php
  /* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4:
   * @name        ElastixMT API
   *
   * @copyright   Copyright (C) 2012-2015 - Federico Pereira - LordBaseX
   * @author      Federico Pereira <lord.basex@gmail.com>
   * @license     http://opensource.org/licenses/GPL-3.0
   * @package     CreateOrganization.php
   * @version     1.0
   *
  +----------------------------------------------------------------------+
  | Elastix version 2.2.0-29                                             |
  | http://www.elastix.org                                               |
  +----------------------------------------------------------------------+
  | Copyright (c) 2006 Palosanto Solutions S. A.                         |
  +----------------------------------------------------------------------+
  | Cdla. Nueva Kennedy Calle E 222 y 9na. Este                          |
  | Telfs. 2283-268, 2294-440, 2284-356                                  |
  | Guayaquil - Ecuador                                                  |
  | http://www.palosanto.com                                             |
  +----------------------------------------------------------------------+
  | The contents of this file are subject to the General Public License  |
  | (GPL) Version 2 (the "License"); you may not use this file except in |
  | compliance with the License. You may obtain a copy of the License at |
  | http://www.opensource.org/licenses/gpl-license.php                   |
  |                                                                      |
  | Software distributed under the License is distributed on an "AS IS"  |
  | basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See  |
  | the License for the specific language governing rights and           |
  | limitations under the License.                                       |
  +----------------------------------------------------------------------+
  | The Original Code is: Elastix Open Source.                           |
  | The Initial Developer of the Original Code is PaloSanto Solutions    |
  +----------------------------------------------------------------------+
  $Id: CreateOrganization.php,v 1.1 2015-02-13 14:05:13 Federico Pereira fpereira@iperfex.com Exp $ */

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
$result  = $client->call('CreateOrganization', array('name'           => 'Elastix', // Organization
                                                     'domain'         => 'elastix.org', // Domain Name
                                                     'country'        => 'Ecuador', // Country
                                                     'city'           => 'Guayaquil', // City
                                                     'address'        => 'Doctor Teodoro Maldonado Carbo 222', // Address
                                                     'country_code'   => 593,// Country Code. More info http://countrycode.org
                                                     'area_code'      => 4, // Area Code. More info http://www.howtocallabroad.com/ecuador
                                                     'quota'          => 30, // Email Quota By User(MB). Default 30MB.
                                                     'email_contact'  => 'lord.basex@gmail.com', // Email Contact
                                                     'max_num_user'   => 0, // Max. # of User Accounts. The value 0 equals unlimited
                                                     'max_num_exten'  => 0, // Max. # of extensions. The value 0 equals unlimited.
                                                     'max_num_queues' => 0) // Max. # of queues. The value 0 equals unlimited.

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

<?php
  /* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4:
   * @name        ElastixMT API
   *
   * @copyright   Copyright (C) 2012-2015 - Federico Pereira - LordBaseX
   * @author      Federico Pereira <lord.basex@gmail.com>
   * @license     http://opensource.org/licenses/GPL-3.0
   * @package     elxmtapi.php
   * @version     1.0
   *

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
  $Id: elxmtapi.php,v 1.1 2015-02-13 20:35:13 Federico Pereira fpereira@iperfex.com Exp $ */

$elxPath="/usr/share/elastix";
ini_set('include_path',"$elxPath:".ini_get('include_path'));

include_once "libs/misc.lib.php";
include_once "configs/default.conf.php";

require_once("libs/JSON.php");
require_once "libs/paloSantoPBX.class.php";
require_once("libs/paloSantoDB.class.php");
require_once "libs/paloSantoOrganization.class.php";

require_once("lib/nusoap.php");

load_default_timezone();

global $json;

$dsn    = generarDSNSistema("asteriskuser","elxpbx");
$pDB    = new paloDB($dsn);
$json   = new Services_JSON();
$server = new soap_server();

// Documentation: http://www.codeproject.com/Articles/140189/PHP-NuSOAP-Tutorial


// Initialize WSDL support
$server->configureWSDL('elxmtapi_wsdl', 'urn:elxmtapi_wsdl');

// Register expose methods


// Method 1.- Login
$server->register('Login',
            array('user'     => 'xsd:string',
                  'password' => 'xsd:string'),          // input parameters
            array('response' => 'xsd:string'),          // output parameters
                  'urn:elxmtapi_wsdl',                  // namespace
                  'urn:elxmtapi_wsdl#Login',            // soapaction
                  'rpc',                                // style
                  'encoded',                            // use
                  'User Login'                          // documentation
);

// Method 2.- UserDisable
$server->register('UserDisable',
            array('user'     => 'xsd:string'),          // input parameters
            array('response' => 'xsd:string'),          // output parameters
                  'urn:elxmtapi_wsdl',                  // namespace
                  'urn:elxmtapi_wsdl#UserDisable',      // soapaction
                  'rpc',                                // style
                  'encoded',                            // use
                  'User Disable'                        // documentation
);

// Method 3.- UserEnable
$server->register('UserEnable',
            array('user'     => 'xsd:string'),          // input parameters
            array('response' => 'xsd:string'),          // output parameters
                  'urn:elxmtapi_wsdl',                  // namespace
                  'urn:elxmtapi_wsdl#UserEnable',       // soapaction
                  'rpc',                                // style
                  'encoded',                            // use
                  'User Enable'                         // documentation
);

// Method 4.- Logout
$server->register('Logout',
            array('chain_session' => 'xsd:string'),
            array('response'      => 'xsd:string'),     // output parameters
                  'urn:elxmtapi_wsdl',                  // namespace
                  'urn:elxmtapi_wsdl#Logout',           // soapaction
                  'rpc',                                // style
                  'encoded',                            // use
                  'Login Logout'                        // documentation
);

// Method 5.- CreateOrganization
$server->register('CreateOrganization',
            array('name'           => 'xsd:string',        // input parameters
                  'domain'         => 'xsd:string',        // input parameters
                  'country'        => 'xsd:string',        // input parameters
                  'city'           => 'xsd:string',        // input parameters
                  'address'        => 'xsd:string',        // input parameters
                  'country_code'   => 'xsd:string',        // input parameters
                  'area_code'      => 'xsd:string',        // input parameters
                  'quota'          => 'xsd:string',        // input parameters
                  'email_contact'  => 'xsd:string',        // input parameters
                  'max_num_user'   => 'xsd:string',        // input parameters
                  'max_num_exten'  => 'xsd:string',        // input parameters
                  'max_num_queues' => 'xsd:string'),       // input parameters
            array('response'       => 'xsd:string'),       // output parameters
                  'urn:elxmtapi_wsdl',                     // namespace
                  'urn:elxmtapi_wsdl#CreateOrganization',  // soapaction
                  'rpc',                                   // style
                  'encoded',                               // use
                  'Create Organization'                    // documentation
);


// Use the request to (try to) invoke the service
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);

// Method 1.- Login
function Login($user, $password)
{
    global $pDB;
    global $json;

    $query = "SELECT id as 'id_user', minute_session as 'minutes', enabled
                FROM api_user
              WHERE username = ?
                AND md5_password = md5(?)";
    $result = $pDB->getFirstRowQuery($query, true, array($user, $password));

    if(sizeof($result) >= 1 && isset($result['id_user']) && $result['enabled'] ==1){
        $chain_session = md5(uniqid());
        $date1 = date("Y-m-d H:i:s", time());
        $date2 = date("Y-m-d H:i:s", time()+$result['minutos']*60);

        $query  = "UPDATE api_session
                      SET status = 'Inactive'
                    WHERE id_api_user = ?
                      AND status = 'Active'";
        $pDB->genQuery($query, array($result['id_user']));

        $query = "INSERT into api_session
                  (chain_session, id_api_user, last_activity, expiration_date, status)
                  VALUES(?, ?, ?, ?, 'Active')";
        $pDB->genQuery($query, array($chain_session, $result['id_user'], $date1, $date2));

        $query = "UPDATE api_user
                     SET last_login = ?
                   WHERE id = ?";
        $pDB->genQuery($query, array($date1, $result['id_user']));

        $arrReturn = array('login'          => 'Yes',
                           'chain_session'  => $chain_session,
                           'code'           => '400',
                           'message'        => 'Authenticated user.');

    }elseif($result['enabled'] == 0){
        $arrReturn = array('login'   => 'No',
                           'code'    => '501',
                           'message' => 'Unregistered user.');
    }else{
        $arrReturn = array('login'   => 'No',
                           'code'    => '502',
                           'message' => 'User not authenticated.');

    }
    return $json->encode($arrReturn);
}

// Method 2.- UserDisable
function UserDisable($user)
{
    global $pDB;
    global $json;

    $query  = "UPDATE api_user
                  SET enabled = 0
                WHERE username = ?";
    $result = $pDB->genQuery($query, array($user));
    $arrReturn = array('userdisable' => 'Yes',
                       'code'        => '401',
                       'message'     => 'User has been disabled.');

    return $json->encode($arrReturn);
}

// Method 3.- UserEnable
function UserEnable($user)
{
    global $pDB;
    global $json;

    $query  = "UPDATE api_user
                  SET enabled = 1
                WHERE username = ?";
    $result = $pDB->genQuery($query, array($user));
    $arrReturn = array('userenable' => 'Yes',
                       'code'       => '402',
                       'message'    => 'User has been enabled.');

    return $json->encode($arrReturn);
}

// Method 4.- Logout
function Logout($chain_session)
{
    global $pDB;
    global $json;
    $query = "SELECT id
                FROM api_session
               WHERE chain_session = ?
                 AND status = ?";

    $result = $pDB->getFirstRowQuery($query, true, array($chain_session, 'Active'));

    if(sizeof($result) >= 1){
        $query = "UPDATE api_session
                     SET status = ?
                   WHERE id = ?";
        $result = $pDB->genQuery($query, array('Inactive', $result['id']));

        $arrReturn = array('logout'  => 'Yes',
                           'code'    => '403',
                           'message' => 'Session completed successfully.');
    }else{
        $arrReturn = array('logout'  => 'No',
                           'code'    => '503',
                           'message' => 'It was not possible to conclude session.');
    }
    return $json->encode($arrReturn);
}

// Method 5.- CreateOrganization
function CreateOrganization($name, $domain, $country, $city, $address, $country_code, $area_code, $quota, $email_contact, $max_num_user, $max_num_exten, $max_num_queues)
{
    global $pDB;
    global $json;

    $pOrganization = new paloSantoOrganization($pDB);
    $admin_password = random_password(10);
    $check_validateParams = validateParams($name, $domain, $country, $city, $address, $country_code, $area_code, $quota, $email_contact, $max_num_user, $max_num_exten, $max_num_queues, $admin_password);

    if($check_validateParams[0] == 'true'){
        $result = $pOrganization->createOrganization($name, $domain, $country, $city, $address, $country_code, $area_code, $quota, $email_contact, $max_num_user, $max_num_exten, $max_num_queues, $admin_password);

        if($result != ""){
                $check_reloadDialplan = AsteriskReloadDialplan($domain);
                if($check_reloadDialplan == 'true'){
                        $arrReturn = array('createorganization' => 'Yes',
                                                         'code' => '404',
                                                     'password' => $admin_password,
                                                      'message' => 'Creation organization completed successfully.');
                }else{
                        $arrReturn = array('createorganization' => 'No',
                                                         'code' => '506',
                                                      'message' => 'Asterisk can\'t be reloaded.');
                }

        }else{
                $arrReturn = array('createorganization' => 'No',
                                                 'code' => '505',
                                              'message' => 'Already exist other organization with the same domain.');
        }
    }else{
                $arrReturn = array('createorganization' => 'No',
                                                 'code' => '504',
                                              'message' => $check_validateParams[1]);
    }

    return $json->encode($arrReturn);

}

/**********************************************************************/
// Functions that are not methods.
/**********************************************************************/

// function to generate password random
function random_password( $length = 10 )
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $password = substr( str_shuffle( $chars ), 0, $length );
    return $password;
}

// function to validate variables
function validateParams($name, $domain, $country, $city, $address, $country_code, $area_code, $quota, $email_contact, $max_num_user, $max_num_exten, $max_num_queues, $admin_password)
{
    $result=array();
    $error=array();
    $req="Required Field: ";
    $bf="Bad Format: ";

    //parametros requeridos
    if(!isset($domain)){
        $error[]="domain";
    }
    if(!isset($email_contact)){
        $error[]="email_contact";
    }
    if(count($error)!=0){
        $errMsg=$req.implode(",",$error);
        $result[0] = 'false';
        $result[1] = $errMsg;
        return $result;
    }else{
        $result[0] = 'true';
    }

    if(!preg_match("/^([a-zA-ZñÑáéíóúÁÉÍÓÚ0-9]+[\s'.]?)+\S$/",$name)){
        $error[]="name";
    }

    if(!preg_match("/^(([[:alnum:]-]+)\.)+([[:alnum:]])+$/",$domain)){
        $error[]="domain";
    }

    if(!preg_match("/^([a-zA-Z]+[\s'.]?)+\S$/",$country)){
        $error[]="country ->$country<-";
    }else{
        $resultCountryName = getCountrySettings($country);
                if(!isset($resultCountryName['code'])){
                    $error[]="The country name is incorrect. Visit http://countrycode.org";
                }
    }

    if(!preg_match("/^([a-zA-ZñÑáéíóúÁÉÍÓÚ0-9]+[\s'.]?)+\S$/",$city)){
        $error[]="city";
    }

    if(!preg_match("/^([a-zA-ZñÑáéíóúÁÉÍÓÚ0-9]+[\s'.]?)+\S$/",$address)){
        $error[]="address";
    }

    if(isset($country_code)){
        if(!ctype_digit($country_code)){
            $error[]="country_code (digit)";
        }else{
            $resultCountryCode = getCountrySettings($country);
            if($resultCountryCode['code'] != $country_code){
                $error[]="The country code is wrong. Visit http://countrycode.org";
            }
        }
    }

    if(isset($area_code)){
        if(!ctype_digit($area_code)){
                $error[]="area_code (digit)";
        }
    }

    if(isset($quota)){
        if(!ctype_digit($quota) || ($quota+0)==0){
                $error[]="quota (digit > 0)";
        }
    }

    if(!preg_match("/^[a-z0-9]+([\._\-]?[a-z0-9]+[_\-]?)*@[a-z0-9]+([\._\-]?[a-z0-9]+)*(\.[a-z0-9]{2,4})+$/",$email_contact)){
        $error[]="email_contact";
    }

    if(isset($max_num_user)){
        if(!ctype_digit($max_num_user)){
                $error[]="max_num_user (digit)";
        }
    }

    if(isset($max_num_exten)){
        if(!ctype_digit($max_num_exten)){
                $error[]="max_num_exten (digit)";
        }elseif(($max_num_exten<$max_num_user && $max_num_user!=0 && $max_num_exten!=0) || ($max_num_user==0 && $max_num_exten!=0)){
                $error[]="max_num_exten (max_num_exten>=max_num_user)";
        }
    }

    if(isset($max_num_queues)){
        if(!ctype_digit($max_num_queues)){
                $error[]="max_num_queues (digit)";
        }
    }

    if(count($error)>0){
        $errMsg=$bf.implode(",",$error);
        $result[0] = 'false';
        $result[1] = $errMsg;
        return $result;
    }else{
        $result[0] = 'true';
        return $result;
    }
}

// function AsteriskReloadDialplan
function AsteriskReloadDialplan($domain)
{
    global $pDB;
    global $json;

    $pAstConf = new paloSantoASteriskConfig($pDB);

    if($pAstConf->generateDialplan($domain)===false){
        $pAstConf->setReloadDialplan($domain,true);
        $result = 'false';
    }else{
        $pAstConf->setReloadDialplan($domain);
        $result = 'true';

    }

    return $result;

}

?>

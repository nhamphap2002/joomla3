<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

define('_JEXEC', 1);
 
// Fix magic quotes.
@ini_set('magic_quotes_runtime', 0);
 
// Maximise error reporting.
@ini_set('zend.ze1_compatibility_mode', '0');
//error_reporting(E_ALL);
ini_set('display_errors', 1);
 
/*
 * Ensure that required path constants are defined.
 */
if (!defined('JPATH_BASE'))
{
	define('JPATH_BASE',  $_SERVER['DOCUMENT_ROOT']  . '/ModaNuovo/');
}

if (!defined('JPATH_ADMINISTRATOR'))
{
	define('JPATH_ADMINISTRATOR',  $_SERVER['DOCUMENT_ROOT']  . '/ModaNuovo/administrator/');
}
 
define('JPATH_LIBRARIES',		JPATH_BASE . '/libraries');
define('DS',		'/');
 require_once(JPATH_BASE.'/includes/defines.php');
/**
 * Import the platform. This file is usually in JPATH_LIBRARIES 
 */

require_once JPATH_LIBRARIES . '/import.php';
 
/**
 * Import the application.
 */
require_once JPATH_BASE.'/webappWO/includes/application.php';

// Instantiate the application administrator to make virtuemart work
$app = JFactory::getApplication('administrator');
//echo '<br/> DEBUG webappWO/index.php linea 56 Debug linea 49 Trying to cereate a session token --> '.$token;
// create a form token
$user = JFactory::getUser(42);
		 $session = JFactory::getSession();
		 $hash = JApplication::getHash($user->get('id', 0) . $session->getToken(true));
// first set user 
//$b = JUser::getInstance();
// create a token
//$a=JSession::getInstance();
//$token = $a->getToken(true);
                 
                
    /*
    Look at the code for the form.php, you'll notice the two form fields, username, password, and you'll see them being sent here too.
    */

   
                 JRequest::setVar('prova', $_REQUEST['sum1'],'POST');
	JRequest::setVar($hash, 1, 'POST');
        $GLOBALS['_JREQUEST'][$hash]['SET.POST']=1;
        $data = JRequest::get('POST');
//         echo '<br/> webappWO/index.php linea 74 stampo ciò che mi ritorna JRequest'.var_dump($data);
//JRequest::setVar('token', $token, 'SERVER');
//echo '<br> webappWO/index.php linea 77 Trying to cereate an hash with user and session  --> '.$hash;

if(!JRequest::checkToken()) {
    
// echo 'webappWO index.php linea 89 <br/>Here<br/>';
    //jexit ( '<br>wow' );
//    include_once 'LoginCurl.php';
    $credentials = array();
$credentials['username'] = 'admin';
$credentials['password'] = 'superciccio';

//preform the login action
$error = $app->login($credentials);
$user = JFactory::getUser();
// echo $hash;
}

echo $hash;
//$directly = true;

echo '<br> webappWO/index.php linea 97 token validato <br/>';
if(!JRequest::checkToken()) jexit ( 'error' ); 
//else    require_once 'index.php';


?>

<?php
session_start();

function myErrorHandler($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return;
    }

    switch ($errno) {
    case E_USER_ERROR:
        $error= "<b>My ERROR</b> [$errno] $errstr<br />\n";
        $error.= "  Fatal error on line $errline in file $errfile";
        $error.= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        $error.= "Aborting...<br />\n";
         $_SESSION['Errors']['Errors'][]=$error;
//        exit(1);
        break;

    case E_USER_WARNING:
        $_SESSION['Errors']['Warning'][] = "<b>My WARNING</b> [$errno] $errstr<br />";
        break;
    case 0:
    case 1:
    case 2:
    case 3:
    case 4:
    case 5:
    case 6:
    case 7:
    case 8: // notice
        if(isset($_REQUEST['ajax']) || isset($_REQUEST['ajaxAccess']) )         {
            $_SESSION['Errors']['Notice'][]="<b>My NOTICE</b> [$errno] $errstr $errfile $errline<br />";
            //json_encode($_SESSION);
            }
            
//        else $error.= "<b>My NOTICE</b> [$errno] $errstr $errfile $errline<br />\n";
        break;

    default:
//        $error.= "Unknown error type: [$errno] $errstr<br />\n";
        break;
    }

    /* Don't execute PHP internal error handler */
    return true;
}

// function to test the error handling
function scale_by_log($vect, $scale)
{
    if (!is_numeric($scale) || $scale <= 0) {
        trigger_error("log(x) for x <= 0 is undefined, you used: scale = $scale", E_USER_ERROR);
    }

    if (!is_array($vect)) {
        trigger_error("Incorrect input vector, array of values expected", E_USER_WARNING);
        return null;
    }

    $temp = array();
    foreach($vect as $pos => $value) {
        if (!is_numeric($value)) {
            trigger_error("Value at position $pos is not a number, using 0 (zero)", E_USER_NOTICE);
            $value = 0;
        }
        $temp[$pos] = log($scale) * $value;
    }

    return $temp;
}

// set to the user defined error handler
$old_error_handler = set_error_handler("myErrorHandler");

//trigger_error("Value at position $pos is not a number, using 0 (zero)", E_USER_NOTICE);

//if(!$a){
//    $error.= 'ciao';
//}

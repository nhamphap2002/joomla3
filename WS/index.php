<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!defined('_JEXEC')) {
    define('_JEXEC', 1);
}
if (!defined('DS')) {
    define('DS', '/');
}
if (!defined('_JDEFINES')) {
    define('JPATH_BASE', dirname(dirname(__FILE__)));
    require_once JPATH_BASE . '/includes/defines.php';
}

require_once JPATH_BASE . '/includes/framework.php';

// Mark afterLoad in the profiler.
JDEBUG ? $_PROFILER->mark('afterLoad') : null;

// Instantiate the application.
$app = JFactory::getApplication('administrator');

// Initialise the application.
$app->initialise();

// Mark afterIntialise in the profiler.
JDEBUG ? $_PROFILER->mark('afterInitialise') : null;

JDEBUG ? $_PROFILER->mark('afterDispatch') : null;


if (!defined('JPATH_VM_ADMINISTRATOR')) {
    define('JPATH_VM_ADMINISTRATOR', JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_virtuemart');
}

if (!class_exists('VmConfig'))
    require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'config.php');
VmConfig::loadConfig();

if (!class_exists('VmModel'))
    require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmmodel.php');

//Start login Admin
$credentials = array();
$credentials['username'] = 'admin';
$credentials['password'] = 'three321!!admin';
//preform the login action
$error = $app->login($credentials);
$user = JFactory::getUser();

function getProductsOrdersListQuery() {
    return ' FROM #__virtuemart_products AS o';
}

if ($user->id <= 0 || $user->id == null) {
    echo 'Username and password to login admin not correct';
    exit();
} else {
    $model = VmModel::getModel('product');
    $select = " o.* ";
    $from = getProductsOrdersListQuery();
    $whereString = '';
    $ordering = ' ';
    //Test simple test use class of VM
    $list_orders = $model->exeSortSearchListQuery(0, $select, $from, $whereString, '', $ordering);
    var_dump($list_orders);
}

exit();

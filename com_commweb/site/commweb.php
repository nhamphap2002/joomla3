<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Commweb
 * @author     Thang Tran <thang.fgc1207@gmail.com>
 * @copyright  2017 Thang Tran
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Commweb', JPATH_COMPONENT);
JLoader::register('CommwebController', JPATH_COMPONENT . '/controller.php');

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

if (!class_exists('VmConfig'))
    require(JPATH_ROOT . '/administrator/components/com_virtuemart/helpers/config.php');
VmConfig::loadConfig();

if (!class_exists('VirtueMartModelOrders')) {
    require(VMPATH_ADMIN . DS . 'models' . DS . 'orders.php');
}
if (!class_exists('VirtueMartModelCurrency')) {
    require(VMPATH_ADMIN . DS . 'models' . DS . 'currency.php');
}
if (!class_exists('VM_COMMWEB_HOSTED_API')) {
    include_once JPATH_COMPONENT . DS . 'models' . DS . 'class-commweb-api.php';
}
if (!class_exists('ShopFunctions')) {
    require(VMPATH_ADMIN . DS . 'helpers' . DS . 'shopfunctions.php');
}
// Execute the task.
$controller = JControllerLegacy::getInstance('Commweb');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

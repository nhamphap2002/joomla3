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


// Execute the task.
$controller = JControllerLegacy::getInstance('Commweb');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

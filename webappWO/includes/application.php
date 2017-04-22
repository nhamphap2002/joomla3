<?php
/**
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('JPATH_PLATFORM') or die;

/**
 * Joomla! Application class
 *
 * Provide many supporting API functions
 *
 * @package	Joomla.MywebappWO
 * @subpackage	Application
 */
final class MywebappWO extends JApplicationWeb
{

	/**
	 * Display the application.
	 */
	public function render()
	{
		echo '<h1>My Web Application</h1>';

		echo 'The current URL is '.JUri::current().'<br/>';
		echo 'The date is '. JFactory::getDate('now');
		
	}
        
        protected function doExecute(){
            
            require_once(JPATH_BASE."/configuration.php");
            $app = JFactory::getApplication();
 
//			$this->setBody(
//				'<h1>My Web Application</h1>'.
//				'The current URL is '.JUri::current().'<br/>'.
//				'The date is '. JFactory::getDate('now')
//                	);
                        
//                        echo '<b>DEBUG webappWOWO/application.php sto effettivamente eseguendo la applicazione</b>';
                        
        }
}

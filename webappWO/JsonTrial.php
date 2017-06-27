<?php

//echo error_reporting();
session_start();

///home/giuseppe/homeProj/ModaFinala/administrator/includes/defines.php
//$a=  json_decode($_REQUEST['JsonPar']);
//echo $Name = $_REQUEST['Name'];
//echo $Email = $_REQUEST['Email'];
//echo $Message = $_REQUEST['Message'];
//var_dump($a);
if (strrpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    $namehost = "/horme/";
} else {
    $namehost = "/";
}

//$namehost = 'ModaFinala';  
if (!defined('JPATH')) {
    define("JPATH", $_SERVER['DOCUMENT_ROOT'] . $namehost);
}
if (!defined('JPATH_PLATFORM')) {
    define("JPATH_PLATFORM", $_SERVER['DOCUMENT_ROOT'] . $namehost . "/libraries/");
}

//require_once(JPATH_PLATFORM.'/administrator/includes/defines.php');
//define("DS",    "/");
if (!defined('JPATH_COMPONENT_ADMINISTRATOR')) {
    define("JPATH_COMPONENT_ADMINISTRATOR", JPATH . "/administrator/components/com_virtuemart/");
}
if (!defined('JPATH_VM_ADMINISTRATOR')) {
    define("JPATH_VM_ADMINISTRATOR", JPATH . "/administrator/components/com_virtuemart/");
}
if (!defined('JPATH_VM_LIBRARIES')) {
    define("JPATH_VM_LIBRARIES", JPATH . '/libraries');
}
//if (!defined('JPATH_VM_LIBRARIES')) { define ("_JEXEC",  1); }
//require_once(JPATH . "/configuration.php");

//require_once(JPATH_PLATFORM . "/import.php");

//class_exists('JLoader') or die;
// Setup the autoloaders.
//JLoader::setup();
//
//
//// need JObject 
//
//require_once(JPATH_PLATFORM.'/libraries/joomla/base/object.php');
//require_once(JPATH_PLATFORM.'/libraries/loader.php');
//
//require_once(JPATH_PLATFORM.'/administrator/components/com_virtuemart/admin.virtuemart.php');
//
//
////if(!class_exists('JModel'))
//echo $_SERVER['DOCUMENT_ROOT'].  "/ModaFinala/" . 'libraries' . "/" . 'joomla' . "/" . 'application' . "/" . 'component' . "/" . 'model.php';
//require_once($_SERVER['DOCUMENT_ROOT'].  "/ModaFinala/" . 'libraries' . "/" . 'joomla' . "/" . 'application' . "/" . 'component' . "/" . 'model.php');
//if(!class_exists('VmModel'))require($_SERVER['DOCUMENT_ROOT'] . "/ModaFinala/administrator/components/com_virtuemart/" . 'helpers' . "/" . 'vmmodel.php');

//if (!class_exists('JModel'))
//    require(JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'application' . DS . 'component' . DS . 'model.php');
//if (!class_exists('VmModel'))
//    require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmmodel.php');

class ProductWebservice extends VmModel {

    //Object to hold old against new ids. We wanna port as when it setup fresh, so no importing of old ids!

    private $_test = false;
    private $_stop = false;

    public function __construct() {

//	require_once(JPATH_PLATFORM . "/joomla/database/table.php");
//            /home/giuseppe/homeProj/ModaFinala/libraries/

//	JTable::addIncludePath(JPATH_VM_ADMINISTRATOR . "/" . 'tables');

	$this->_app = JFactory::getApplication();
	$this->_db = JFactory::getDBO();
	$this->_oldToNew = new stdClass();
	$this->starttime = microtime(true);
	if (strrpos($_SERVER['HTTP_HOST'], 'localhost') !== true)
	    $this->_dev = true;

//		$max_execution_time = ini_get('max_execution_time');
//		$jrmax_execution_time= JRequest::getInt('max_execution_time');
//		if(!empty($jrmax_execution_time)){
//// 			vmdebug('$jrmax_execution_time',$jrmax_execution_time);
//			if($max_execution_time!=$jrmax_execution_time) @ini_set( 'max_execution_time', $jrmax_execution_time );
//		}
//		$this->maxScriptTime = ini_get('max_execution_time')*0.80-1;	//Lets use 5% of the execution time as reserve to store the progress
//		$jrmemory_limit= JRequest::getInt('memory_limit');
//		if(!empty($jrmemory_limit)){
//			@ini_set( 'memory_limit', $jrmemory_limit.'M' );
//		} else {
//			$memory_limit = ini_get('memory_limit');
//			if($memory_limit<128)  @ini_set( 'memory_limit', '128M' );
//		}
//
//		$this->maxMemoryLimit = $this->return_bytes(ini_get('memory_limit')) - (11 * 1024 * 1024)  ;		//Lets use 11MB for joomla
// 		vmdebug('$this->maxMemoryLimit',$this->maxMemoryLimit); //134217728
	//$this->maxMemoryLimit = $this -> return_bytes('20M');
	// 		ini_set('memory_limit','35M');
//		$q = 'SELECT `id` FROM `#__virtuemart_migration_oldtonew_ids` ';
//		$this->_db->setQuery($q);
//		$res = $this->_db->loadResult();
//		if(empty($res)){
//			$q = 'INSERT INTO `#__virtuemart_migration_oldtonew_ids` (`id`) VALUES ("1")';
//			$this->_db->setQuery($q);
//			$this->_db->query();
//			$this->_app->enqueueMessage('Start with a new migration process and setup log maxScriptTime '.$this->maxScriptTime.' maxMemoryLimit '.$this->maxMemoryLimit/(1024*1024));
//		} else {
//			$this->_app->enqueueMessage('Found prior migration process, resume migration maxScriptTime '.$this->maxScriptTime.' maxMemoryLimit '.$this->maxMemoryLimit/(1024*1024));
//		}
//
//		JRequest::setVar('synchronise',true);
    }

    private function return_bytes($val) {
	$val = trim($val);
	$last = strtolower($val[strlen($val) - 1]);
	switch ($last) {
	    // The 'G' modifier is available since PHP 5.1.0
	    case 'g':
		$val *= 1024;
	    case 'm':
		$val *= 1024;
	    case 'k':
		$val *= 1024;
	}

	return $val;
    }

    function getMigrationProgress($group) {

	$q = 'SELECT `' . $group . '` FROM `#__virtuemart_migration_oldtonew_ids` WHERE `id` = "1" ';

	$this->_db->setQuery($q);
	$result = $this->_db->loadResult();
	if (empty($result)) {
	    $result = array();
	} else {
	    $result = unserialize($result);
	    if (!$result) {
		$result = array();
	    }
	}

	return $result;
    }

    function storeMigrationProgress($group, $array) {

	//$q = 'UPDATE `#__virtuemart_migration_oldtonew_ids` SET `'.$group.'`="'.implode(',',$array).'" WHERE `id` = "1"';
	$q = 'UPDATE `#__virtuemart_migration_oldtonew_ids` SET `' . $group . '`="' . serialize($array) . '" WHERE `id` = "1"';


	$this->_db->setQuery($q);
	if (!$this->_db->query()) {
	    $this->_app->enqueueMessage('storeMigrationProgress failed to update query' . $this->_db->getQuery());
	    $this->_app->enqueueMessage('and ErrrorMsg ' . $this->_db->getErrorMsg());
	    return false;
	}

	return true;
    }

    function migrateGeneral() {

	$result = $this->portMedia();
	$result = $this->portShoppergroups();
	$result = $this->portCategories();
	$result = $this->portManufacturerCategories();
	$result = $this->portManufacturers();
	// 		$result = $this->portOrderStatus();

	$time = microtime(true) - $this->starttime;
	vmInfo('Worked on general migration for ' . $time . ' seconds');
	vmRamPeak('Migrate general vm1 info ended ');
	return $result;
    }

    function migrateUsers() {

//		$result = $this->portShoppergroups();
	$result = $this->portUsers();

	$time = microtime(true) - $this->starttime;
	vmInfo('Worked on user migration for ' . $time . ' seconds');
	vmRamPeak('Migrate shoppers ended ');
	return $result;
    }

    function migrateProducts() {

	$result = $this->portMedia();

	$result = $this->portCategories();
	$result = $this->portManufacturerCategories();
	$result = $this->portManufacturers();
	$result = $this->portProducts();

	$time = microtime(true) - $this->starttime;
	$this->_app->enqueueMessage('Worked on general migration for ' . $time . ' seconds');

	return $result;
    }

    function migrateOrders() {

	$result = $this->portMedia();
	$result = $this->portCategories();
	$result = $this->portManufacturerCategories();
	$result = $this->portManufacturers();
	$result = $this->portProducts();

	// 		$result = $this->portOrderStatus();
	$result = $this->portOrders();
	$time = microtime(true) - $this->starttime;
	vmInfo('Worked on migration for ' . $time . ' seconds');

	return $result;
    }

    function migrateAllInOne() {

	$result = $this->portMedia();

	$result = $this->portShoppergroups();
	$result = $this->portUsers();
	$result = $this->portVendor();

	$result = $this->portCategories();
	$result = $this->portManufacturerCategories();
	$result = $this->portManufacturers();
	$result = $this->portProducts();

	//$result = $this->portOrderStatus();
	$result = $this->portOrders();
	$time = microtime(true) - $this->starttime;
	$this->_app->enqueueMessage('Worked on migration for ' . $time . ' seconds');

	vmRamPeak('Migrate all ended ');
	return $result;
    }

    function EvaluateErrors() {
	$mess = JFactory::getApplication()->getMessageQueue();
	foreach ($mess as $key => $message) {
	    if ($message['type'] != 'error') {
		unset($mess[$key]);
	    } else {
		$_SESSION['Errors']['Errors'][] = $message['message'];
	    }
	}

	return $mess;
    }

    public function portMedia() {

	$ok = true;

	//Prevents search field from interfering with syncronization
	JRequest::setVar('searchMedia', '');

	//$imageExtensions = array('jpg','jpeg','gif','png');

	if (!class_exists('VirtueMartModelMedia'))
	    require($JPATH_VM_ADMINISTRATOR . "/" . 'models' . "/" . 'media.php');
	$this->mediaModel = VmModel::getModel('Media');
	//First lets read which files are already stored
	$this->storedMedias = $this->mediaModel->getFiles(false, true);

	//check for entries without file
	foreach ($this->storedMedias as $media) {

	    $media_path = JPATH_ROOT . DS . str_replace('/', DS, $media->file_url);
	    if (!file_exists($media_path)) {
		vmInfo('File for ' . $media->file_url . ' is missing');

		//The idea is here to test if the media with missing data is used somewhere and to display it
		//When it not used, the entry should be deleted then.
		/* 				$q = 'SELECT * FROM `#__virtuemart_category_medias` as cm,
		  `#__virtuemart_product_medias` as pm,
		  `#__virtuemart_manufacturer_medias` as mm,
		  `#__virtuemart_vendor_medias` as vm
		  WHERE cm.`virtuemart_media_id` = "'.$media->virtuemart_media_id.'"
		  OR pm.`virtuemart_media_id` = "'.$media->virtuemart_media_id.'"
		  OR mm.`virtuemart_media_id` = "'.$media->virtuemart_media_id.'"
		  OR vm.`virtuemart_media_id` = "'.$media->virtuemart_media_id.'" ';

		  $this->_db->setQuery($q);
		  $res = $this->_db->loadResultArray();
		  vmdebug('so',$res);
		  if(count($res)>0){
		  vmInfo('File for '.$media->file_url.' is missing, but used ');
		  }
		 */
	    }
	}


	$countTotal = 0;
	//We do it per type
	$url = VmConfig::get('media_product_path');
	$type = 'product';
	$count = $this->_portMediaByType($url, $type);
	$countTotal += $count;
	$this->_app->enqueueMessage(JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT', $count, $type, $url));

	if ((microtime(true) - $this->starttime) >= ($this->maxScriptTime)) {
	    return $msg = JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT_NOT_FINISH', $countTotal);
	}

	$url = VmConfig::get('media_category_path');
	$type = 'category';
	$count = $this->_portMediaByType($url, $type);
	$countTotal += $count;
	$this->_app->enqueueMessage(JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT', $count, $type, $url));

	if ((microtime(true) - $this->starttime) >= ($this->maxScriptTime)) {
	    return $msg = JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT_NOT_FINISH', $countTotal);
	}

	$url = VmConfig::get('media_manufacturer_path');
	$type = 'manufacturer';
	$count = $this->_portMediaByType($url, $type);
	$countTotal += $count;
	$this->_app->enqueueMessage(JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT', $count, $type, $url));

	if ((microtime(true) - $this->starttime) >= ($this->maxScriptTime)) {
	    return $msg = JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT_NOT_FINISH', $countTotal);
	}

	$url = VmConfig::get('media_vendor_path');
	$type = 'vendor';
	$count = $this->_portMediaByType($url, $type);
	$countTotal += $count;
	$this->_app->enqueueMessage(JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT', $count, $type, $url));

	return $msg = JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT_FINISH', $countTotal);
    }

    private function _portMediaByType($url, $type) {

	$knownNames = array();
	//create array of filenames for easier handling
	foreach ($this->storedMedias as $media) {
	    if ($media->file_type == $type) {

		//Somehow we must use here the right char encoding, so that it works below
		// in line 320
		$knownNames[] = $media->file_url;
	    }
	}
// 		vmdebug('my known paths of type'.$type,$knownNames);
	$filesInDir = array();
	$foldersInDir = array();

	$path = str_replace('/', DS, $url);
	//$dir = JPATH_ROOT.DS.$path;
	$foldersInDir = array(JPATH_ROOT . "/" . $path);
	while (!empty($foldersInDir)) {
	    foreach ($foldersInDir as $dir) {
		$subfoldersInDir = null;
		$subfoldersInDir = array();
		$relUrl = str_replace(DS, '/', substr($dir, strlen(JPATH_ROOT . DS)));
		if ($handle = opendir($dir)) {
		    while (false !== ($file = readdir($handle))) {

			//$file != "." && $file != ".." replaced by strpos
			if (!empty($file) && strpos($file, '.') !== 0 && $file != 'index.html') {

			    $filetype = filetype($dir . "/" . $file);
			    $relUrlName = '';
			    $relUrlName = $relUrl . $file;
// 						vmdebug('my relative url ',$relUrlName);
			    //We port all type of media, regardless the extension
			    if ($filetype == 'file') {
				if (!in_array($relUrlName, $knownNames)) {
				    $filesInDir[] = array('filename' => $file, 'url' => $relUrl);
				}
			    } else {
				if ($filetype == 'dir' && $file != 'resized') {
				    $subfoldersInDir[] = $dir . $file . DS;
// 									vmdebug('my sub folder ',$dir.$file);
				}
			    }
			}
		    }
		}
	    }
	    $foldersInDir = $subfoldersInDir;
	    if ((microtime(true) - $this->starttime) >= ($this->maxScriptTime)) {

		break;
	    }
	}
	//echo '<pre>'.print_r($filesInDir,1).'</pre>';
//		die;
	$i = 0;
	foreach ($filesInDir as $file) {

	    $data = null;
	    $data = array('file_title' => $file['filename'],
		'virtuemart_vendor_id' => 1,
		'file_description' => $file['filename'],
		'file_meta' => $file['filename'],
		'file_url' => $file['url'] . $file['filename'],
		'media_published' => 1
	    );
	    if ($type == 'product')
		$data['file_is_product_image'] = 1;
	    $this->mediaModel->setId(0);
	    $success = $this->mediaModel->store($data, $type);

//			$errors = $this->EvaluateErrors();
//                        
//                        if(count($errors)>0)
//			foreach($errors as $error){
//				echo $error['msg'];
//			}
//			$this->mediaModel->resetErrors();
	    if ($success)
		$i++;
	    if ((microtime(true) - $this->starttime) >= ($this->maxScriptTime)) {
		vmError('Attention script time too short, no time left to store the media, please rise script execution time');
		break;
	    }
	}

	return $i;
    }

    private function portShoppergroups() {

	if ($this->_stop || (microtime(true) - $this->starttime) >= ($this->maxScriptTime)) {
	    return;
	}

	$query = 'SHOW TABLES LIKE "%vm_shopper_group%"';
	$this->_db->setQuery($query);
	if (!$this->_db->loadResult()) {
	    vmInfo('No Shoppergroup table found for migration');
	    $this->_stop = true;
	    return false;
	}

	$ok = true;

	$q = 'SELECT * FROM #__vm_shopper_group';
	$this->_db->setQuery($q);
	$oldShopperGroups = $this->_db->loadAssocList();
	if (empty($oldShopperGroups))
	    $oldShopperGroups = array();

	$oldtoNewShoppergroups = array();
	$alreadyKnownIds = $this->getMigrationProgress('shoppergroups');

	$starttime = microtime(true);
	$i = 0;
	foreach ($oldShopperGroups as $oldgroup) {

	    if (!array_key_exists($oldgroup['shopper_group_id'], $alreadyKnownIds)) {
		$sGroups = null;
		$sGroups = array();
		//$category['virtuemart_category_id'] = $oldcategory['category_id'];
		$sGroups['virtuemart_vendor_id'] = $oldgroup['vendor_id'];
		$sGroups['shopper_group_name'] = $oldgroup['shopper_group_name'];

		$sGroups['shopper_group_desc'] = $oldgroup['shopper_group_desc'];
		$sGroups['published'] = 1;
		$sGroups['default'] = $oldgroup['default'];

		$table = $this->getTable('shoppergroups');

		$table->bindChecknStore($sGroups);
		$errors = $table->getErrors();
		if (!empty($errors)) {
		    foreach ($errors as $error) {
			vmError('Migrator portShoppergroups ' . $error);
		    }
		    break;
		}

		$oldtoNewShoppergroups[$oldgroup['shopper_group_id']] = $sGroups['virtuemart_shoppergroup_id'];
		unset($sGroups['virtuemart_shoppergroup_id']);
		$i++;
	    } else {
		$oldtoNewShoppergroups[$oldgroup['shopper_group_id']] = $alreadyKnownIds[$oldgroup['shopper_group_id']];
	    }

	    if ((microtime(true) - $this->starttime) >= ($this->maxScriptTime)) {
		break;
	    }
	}

	$time = microtime(true) - $starttime;
	$this->_app->enqueueMessage('Processed ' . $i . ' vm1 shoppergroups time: ' . $time);

	$this->storeMigrationProgress('shoppergroups', $oldtoNewShoppergroups);
    }

    private function portUsers() {

	if ($this->_stop || (microtime(true) - $this->starttime) >= ($this->maxScriptTime)) {
	    return;
	}

	$query = 'SHOW TABLES LIKE "%vm_user_info%"';
	$this->_db->setQuery($query);
	if (!$this->_db->loadResult()) {
	    vmInfo('No vm_user_info table found for migration');
	    $this->_stop = true;
	    return false;
	}

	$oldToNewShoppergroups = $this->getMigrationProgress('shoppergroups');
	if (empty($oldToNewShoppergroups)) {
	    vmInfo('portUsers getMigrationProgress shoppergroups ' . $this->_db->getErrorMsg());
	    return false;
	}

	if (!class_exists('VirtueMartModelUser'))
	    require($JPATH_VM_ADMINISTRATOR . "/" . 'models' . "/" . 'user.php');
	$userModel = VmModel::getModel('user');

	$ok = true;
	$continue = true;

	//approximatly 110 users take a 1 MB
	$maxItems = $this->_getMaxItems('Users');


	// 		$maxItems = 10;
	$i = 0;
	$startLimit = 0;
	while ($continue) {

	    //Lets load all users from the joomla hmm or vm? VM1 users does NOT exist
	    $q = 'SELECT `p`.*,`ui`.*,`svx`.*,`aug`.*,`ag`.*,`vmu`.virtuemart_user_id FROM #__users AS `p`
								LEFT OUTER JOIN #__vm_user_info AS `ui` ON `ui`.user_id = `p`.id
								LEFT OUTER JOIN #__vm_shopper_vendor_xref AS `svx` ON `svx`.user_id = `p`.id
								LEFT OUTER JOIN #__th_vm_auth_user_group AS `aug` ON `aug`.user_id = `p`.id
								LEFT OUTER JOIN #__vm_auth_group AS `ag` ON `ag`.group_id = `aug`.group_id
								LEFT OUTER JOIN #__virtuemart_vmusers AS `vmu` ON `vmu`.virtuemart_user_id = `p`.id
								WHERE (`vmu`.virtuemart_user_id) IS NULL  LIMIT ' . $startLimit . ',' . $maxItems;

	    $res = self::loadCountListContinue($q, $startLimit, $maxItems, 'port shoppers');
	    $oldUsers = $res[0];
	    $startLimit = $res[1];
	    $continue = $res[2];

	    $starttime = microtime(true);

	    foreach ($oldUsers as $user) {

		$user['virtuemart_country_id'] = $this->getCountryIdByCode($user['country']);
		$user['virtuemart_state_id'] = $this->getCountryIdByCode($user['state']);

		if (!empty($user['shopper_group_id'])) {
		    $user['virtuemart_shoppergroups_id'] = $oldToNewShoppergroups[$user['shopper_group_id']];
		}

		//Solution takes vm1 original values, but is not tested (does not set mainvendor)
		/* 				//if(!empty($user['group_name'])){
		  //    $user['perms'] = $user['group_name'];
		  //
		  //} else {
		  $user['user_is_vendor'] = 0;
		  if($user['gid'] == 25){
		  $user['perms'] = 'admin';
		  // 					$user['user_is_vendor'] = 1;
		  }elseif($user['gid'] == 24){
		  $user['perms'] = 'storeadmin';
		  }else {
		  $user['perms'] = 'shopper';
		  }
		  //} */

		$user['virtuemart_user_id'] = $user['id'];
		//$userModel->setUserId($user['id']);
		$userModel->setId($user['id']);  //Should work with setId, because only administrators are allowed todo the migration
		//Save the VM user stuff
		if (!$user = $userModel->saveUserData($user)) {
		    vmError(JText::_('COM_VIRTUEMART_NOT_ABLE_TO_SAVE_USER_DATA'));
		}

		$userinfo = $this->getTable('userinfos');
		if (!$userinfo->bindChecknStore($user)) {
		    vmError('Migrator portUsers ' . $userinfo->getError());
		}

		if (!empty($user['user_is_vendor']) && $user['user_is_vendor'] === 1) {
		    if (!$userModel->storeVendorData($user)) {
			vmError('Migrator portUsers ' . $userModel->getError());
		    }
		}

		$i++;
		/* 	if($i>24){
		  $continue = false;
		  break;
		  } */
		if ((microtime(true) - $this->starttime) >= ($this->maxScriptTime)) {
		    $continue = false;
		    break;
		}

		$errors = $userModel->getErrors();
		if (!empty($errors)) {
		    foreach ($errors as $error) {
			vmError('Migrator portUsers ' . $error);
		    }
		    $userModel->resetErrors();
		    $continue = false;
		    //break;
		}
	    }
	}
	$time = microtime(true) - $starttime;
	vmInfo('Processed ' . $i . ' vm1 users time: ' . $time);

	//adresses
	$starttime = microtime(true);
	$continue = true;
	$startLimit = 0;
	$i = 0;
	while ($continue) {

	    $q = 'SELECT `ui`.* FROM #__vm_user_info as `ui`
					LEFT OUTER JOIN #__virtuemart_userinfos as `vui` ON `vui`.`virtuemart_user_id` = `ui`.`user_id`
					WHERE `ui`.`address_type` = "ST" AND  ISNULL (`vui`.`virtuemart_user_id`) LIMIT ' . $startLimit . ',' . $maxItems;

	    $res = self::loadCountListContinue($q, $startLimit, $maxItems, 'port ST addresses');
	    $oldUsersAddresses = $res[0];
	    $startLimit = $res[1];
	    $continue = $res[2];


	    if (empty($oldUsersAddresses))
		return $ok;

	    //$alreadyKnownIds = $this->getMigrationProgress('staddress');
	    $oldtonewST = array();

	    foreach ($oldUsersAddresses as $oldUsersAddi) {

		// 			if(!array_key_exists($oldcategory['virtuemart_userinfo_id'],$alreadyKnownIds)){
		$oldUsersAddi['virtuemart_user_id'] = $oldUsersAddi['user_id'];

		$oldUsersAddi['virtuemart_country_id'] = $this->getCountryIdByCode($oldUsersAddi['country']);
		$oldUsersAddi['virtuemart_state_id'] = $this->getCountryIdByCode($oldUsersAddi['state']);

		if (!$virtuemart_userinfo_id = $userModel->storeAddress($oldUsersAddi)) {
		    $userModel->setError(Jtext::_('COM_VIRTUEMART_NOT_ABLE_TO_SAVE_USERINFO_DATA'));
		}

		$errors = $userModel->getErrors();
		if (!empty($errors)) {
		    foreach ($errors as $error) {
			$this->_app->enqueueMessage('Migration: ' . $error);
		    }
		    $userModel->resetErrors();
		    break;
		}
		$i++;
		if ((microtime(true) - $this->starttime) >= ($this->maxScriptTime)) {
		    $continue = false;
		    break;
		}
	    }
	}

	$time = microtime(true) - $starttime;
	vmInfo('Processed ' . $i . ' vm1 users ST adresses time: ' . $time);
	return $ok;
    }

    private function portVendor() {

	if ($this->_stop || (microtime(true) - $this->starttime) >= ($this->maxScriptTime)) {
	    return;
	}

	$query = 'SHOW TABLES LIKE "%_vm_vendor"';
	$this->_db->setQuery($query);
	if (!$this->_db->loadResult()) {
	    vmInfo('No vm_vendor table found for migration');
	    $this->_stop = true;
	    return false;
	}
	$this->_db->setQuery('SELECT *, vendor_id as virtuemart_vendor_id FROM `#__vm_vendor`');
	$vendor = $this->_db->loadAssoc();
	$currency_code_3 = explode(',', $vendor['vendor_accepted_currencies']); //EUR,USD
	$this->_db->query('SELECT currency_id FROM `#__virtuemart_currencies` WHERE `currency_code_3` IN ( "' . implode('","', $currency_code_3) . '" ) ');
	$vendor['vendor_accepted_currencies'] = $this->_db->loadResultArray();

	$vendorModel = VmModel::getModel('vendor');
	$vendorId = $vendorModel->store($vendor);
	vmInfo('vendor ' . $vendorId . ' Stored');
	return true;
    }

    private function portCategories() {

	$query = 'SHOW TABLES LIKE "%vm_category%"';
	$this->_db->setQuery($query);
	if (!$this->_db->loadResult()) {
	    vmInfo('No vm_category table found for migration');
	    $this->_stop = true;
	    return false;
	}

	$catModel = VmModel::getModel('Category');

	$default_category_browse = JRequest::getString('migration_default_category_browse', '');
// 		vmdebug('migration_default_category_browse '.$default_category_browse);

	$default_category_fly = JRequest::getString('migration_default_category_fly', '');

	$portFlypages = JRequest::getInt('migration_default_category_fly', 0);

	if ((microtime(true) - $this->starttime) >= ($this->maxScriptTime)) {
	    return;
	}
	$ok = true;

	$q = 'SELECT * FROM #__vm_category';
	$this->_db->setQuery($q);
	$oldCategories = $this->_db->loadAssocList();

	$alreadyKnownIds = $this->getMigrationProgress('cats');
	$oldtonewCats = array();

	$category = array();
	$i = 0;
	foreach ($oldCategories as $oldcategory) {

	    if (!array_key_exists($oldcategory['category_id'], $alreadyKnownIds)) {

		$category = array();
		//$category['virtuemart_category_id'] = $oldcategory['category_id'];
		$category['virtuemart_vendor_id'] = $oldcategory['vendor_id'];
		$category['category_name'] = $oldcategory['category_name'];

		$category['category_description'] = $oldcategory['category_description'];
		$category['published'] = $oldcategory['category_publish'] == 'Y' ? 1 : 0;
		$category['created_on'] = $oldcategory['cdate'];
		$category['modified_on'] = $oldcategory['mdate'];

// 				if($default_category_browse!=$oldcategory['category_browsepage']){
// 					$browsepage = $oldcategory['category_browsepage'];
// 					if (strcmp($browsepage, 'managed') ==0 ) {
// 						$browsepage="browse_".$oldcategory['products_per_row'];
// 					}
// 					$category['category_layout'] = $browsepage;
// 				}
// 				if($portFlypages && $default_category_fly!=$oldcategory['category_flypage']){
// 					$category['category_product_layout'] = $oldcategory['category_flypage'];
// 				}
		//idea was to do it by the layout, but we store this information additionally for enhanced pagination
		$category['products_per_row'] = $oldcategory['products_per_row'];
		$category['ordering'] = $oldcategory['list_order'];

		if (!empty($oldcategory['category_full_image'])) {
		    $category['virtuemart_media_id'] = $this->_getMediaIdByName($oldcategory['category_full_image'], 'category');
		}

		$catModel->setId(0);
		$category_id = $catModel->store($category);
		$errors = $catModel->getErrors();
		if (!empty($errors)) {
		    foreach ($errors as $error) {
			vmError('Migrator portCategories ' . $error);
			$ok = false;
		    }
		    break;
		}
// 				$table = $this->getTable('categories');
// 				$category = $table->bindChecknStore($category);
// 				$errors = $table->getErrors();
// 				if(!empty($errors)){
// 					foreach($errors as $error){
// 						vmError($error);
// 						$ok = false;
// 					}
// 					break;
// 				}

		$alreadyKnownIds[$oldcategory['category_id']] = $category_id;
		unset($category['virtuemart_category_id']);
		$i++;
	    } else {
		$oldtonewCats[$oldcategory['category_id']] = $alreadyKnownIds[$oldcategory['category_id']];
	    }

	    if ((microtime(true) - $this->starttime) >= ($this->maxScriptTime)) {
		break;
	    }
	}
// here all categories NEW/OLD are Know
	$this->storeMigrationProgress('cats', $alreadyKnownIds);
	if ($ok)
	    $msg = 'Looks everything worked correct, migrated ' . $i . ' categories ';
	else {
	    $msg = 'Seems there was an error porting ' . $i . ' categories ';
	    foreach ($this->getErrors() as $error) {
		$msg .= '<br />' . $error;
	    }
	}
	$this->_app->enqueueMessage($msg);


	$q = 'SELECT * FROM #__vm_category_xref ';
	$this->_db->setQuery($q);
	$oldCategoriesX = $this->_db->loadAssocList();

	// $alreadyKnownIds = $this->getMigrationProgress('catsxref');

	$new_id = 0;
	$oldtonewCatsXref = array();
	$i = 0;
	$j = 0;
	$ok = true;
	if (!empty($oldCategoriesX)) {
	    foreach ($oldCategoriesX as $oldcategoryX) {
		$category = array();
		if (array_key_exists($oldcategoryX['category_parent_id'], $alreadyKnownIds)) {
		    $category['category_parent_id'] = $alreadyKnownIds[$oldcategoryX['category_parent_id']];
		} else {
		    vmError('Port Categories Xref unknow : ID ' . $oldcategoryX['category_parent_id']);
		    $ok = false;
		    $j++;
		    continue;
		}
		if (array_key_exists($oldcategoryX['category_child_id'], $alreadyKnownIds)) {
		    $category['category_child_id'] = $alreadyKnownIds[$oldcategoryX['category_child_id']];
		} else {
		    vmError('Port Categories Xref unknow : ID ' . $oldcategoryX['category_child_id']);
		    $ok = false;
		    $j++;
		    continue;
		}
		if (ok == true) {
		    $table = $this->getTable('category_categories');

		    $table->bindChecknStore($category);
		    $errors = $table->getErrors();
		    if (!empty($errors)) {
			foreach ($errors as $error) {
			    vmError('Migrator portCategories ref ' . $error);
			    $ok = false;
			}
			break;
		    }


		    $i++;
		}

		if ((microtime(true) - $this->starttime) >= ($this->maxScriptTime)) {
		    break;
		}
	    }

	    //$this->storeMigrationProgress('catsxref',$oldtonewCatsXref);
	    if ($ok)
		$msg = 'Looks everything worked correct, migrated ' . $i . ' categories xref ';
	    else {
		$msg = 'Seems there was an error porting ' . $j . ' of ' . $i . ' categories xref ';
		foreach ($this->getErrors() as $error) {
		    $msg .= '<br />' . $error;
		}
	    }
	    $this->_app->enqueueMessage($msg);

	    return $ok;
	} else {
	    $this->_app->enqueueMessage('No categories to import');
	    return $ok;
	}
    }

    private function portManufacturerCategories() {

	if ((microtime(true) - $this->starttime) >= ($this->maxScriptTime)) {
	    return;
	}
	$ok = true;

	$q = 'SELECT * FROM #__vm_manufacturer_category';
	$this->_db->setQuery($q);
	$oldMfCategories = $this->_db->loadAssocList();

	if (!class_exists('TableManufacturercategories'))
	    require($JPATH_VM_ADMINISTRATOR . "/" . 'tables' . "/" . 'manufacturercategories.php');

	$alreadyKnownIds = $this->getMigrationProgress('mfcats');
	$oldtonewMfCats = array();

	$mfcategory = array();
	$i = 0;
	foreach ($oldMfCategories as $oldmfcategory) {

	    if (!array_key_exists($oldmfcategory['mf_category_id'], $alreadyKnownIds)) {
		//$category['virtuemart_category_id'] = $oldcategory['category_id'];
		$mfcategory = null;
		$mfcategory = array();
		$mfcategory['mf_category_name'] = $oldmfcategory['mf_category_name'];
		$mfcategory['mf_category_desc'] = $oldmfcategory['mf_category_desc'];

		$table = $this->getTable('manufacturercategories');

		$table->bindChecknStore($mfcategory);
		$errors = $table->getErrors();
		if (!empty($errors)) {
		    foreach ($errors as $error) {
			vmError('Migrator portManufacturerCategories ' . $error);
			$ok = false;
		    }
		    break;
		}

		$oldtonewMfCats[$oldmfcategory['mf_category_id']] = $mfcategory['virtuemart_manufacturercategories_id'];
		$i++;
	    } else {
		$oldtonewMfCats[$oldmfcategory['mf_category_id']] = $alreadyKnownIds[$oldmfcategory['mf_category_id']];
	    }

	    unset($mfcategory['virtuemart_manufacturercategories_id']);

	    if ((microtime(true) - $this->starttime) >= ($this->maxScriptTime)) {
		break;
	    }
	}
	$this->storeMigrationProgress('mfcats', $oldtonewMfCats);

	if ($ok)
	    $msg = 'Looks everything worked correct, migrated ' . $i . ' manufacturer categories ';
	else {
	    $msg = 'Seems there was an error porting ' . $i . ' manufacturer categories ';
	    $msg .= $this->getErrors();
	}

	$this->_app->enqueueMessage($msg);

	return $ok;
    }

    private function portManufacturers() {

	if ((microtime(true) - $this->starttime) >= ($this->maxScriptTime)) {
	    return;
	}
	$ok = true;

	$q = 'SELECT * FROM #__vm_manufacturer ';
	$this->_db->setQuery($q);
	$oldManus = $this->_db->loadAssocList();

// 		vmdebug('my old manus',$oldManus);
	$oldtonewManus = array();
	$oldtoNewMfcats = $this->getMigrationProgress('mfcats');
	$alreadyKnownIds = $this->getMigrationProgress('manus');

	$i = 0;
	foreach ($oldManus as $oldmanu) {
	    if (!array_key_exists($oldmanu['manufacturer_id'], $alreadyKnownIds)) {
		$manu = null;
		$manu = array();
		$manu['mf_name'] = $oldmanu['mf_name'];
		$manu['mf_email'] = $oldmanu['mf_email'];
		$manu['mf_desc'] = $oldmanu['mf_desc'];
		$manu['virtuemart_manufacturercategories_id'] = $oldtoNewMfcats[$oldmanu['mf_category_id']];
		$manu['mf_url'] = $oldmanu['mf_url'];
		$manu['published'] = 1;

		if (!class_exists('TableManufacturers'))
		    require($JPATH_VM_ADMINISTRATOR . "/" . 'tables' . "/" . 'manufacturers.php');
		$table = $this->getTable('manufacturers');

		$table->bindChecknStore($manu);
		$errors = $table->getErrors();
		if (!empty($errors)) {
		    foreach ($errors as $error) {

			vmError('Migrator portManufacturers ' . $error);
			$ok = false;
		    }
		    break;
		}
		$oldtonewManus[$oldmanu['manufacturer_id']] = $manu['virtuemart_manufacturer_id'];
		//unset($manu['virtuemart_manufacturer_id']);
		$i++;
	    } else {
		$oldtonewManus[$oldmanu['manufacturer_id']] = $alreadyKnownIds[$oldmanu['manufacturer_id']];
	    }

	    if ((microtime(true) - $this->starttime) >= ($this->maxScriptTime)) {
		break;
	    }
	}

	$this->storeMigrationProgress('manus', $oldtonewManus);

	if ($ok)
	    $msg = 'Looks everything worked correct, migrated ' . $i . ' manufacturers ';
	else {
	    $msg = 'Seems there was an error porting ' . $i . ' manufacturers ';
	    $msg .= $this->getErrors();
	}
	$this->_app->enqueueMessage($msg);
    }

    /**
     * Create a New Virtuemart Product with picture and attribute 
     * @param type $product
     * @return boolean 
     */
    public function createProduct($product, $hash, $_ParentId, $childrenArray) {

	if (!$_ParentId)
	    $_ParentId = -1;

	if ($_ParentId > 0) {
	    $product['product_parent_id'] = $_ParentId;
	}

	if (is_array($childrenArrayIds)) {
	    if (count($childrenArrayIds) > 0) {
		// I have some children to put 

		foreach ($childrenArrayIds as $childAr) {
		    $arrayChild = array(
			"slug" => $childAr['slug'],
			"product_name" => $childAr['product_name'],
			"mprices" => array("product_price" => $childAr['product_price']),
			"product_sku" => $childAr['product_sku'],
			"pordering" => 0,
			"published" => 1
		    );

		    $product['product_parent_id'] ["childs"] [$childAr['virtuemart_product_id']] = $arrayChild;
		}
	    }
	}
//		if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
//			return;
//		}

	$mediaIdFilename = array();
	$ok = true;

	//approximatly 100 products take a 1 MB
//		$maxItems = $this->_getMaxItems('Products');

	$startLimit = 0;
	$i = 0;

	//vmdebug('in product migrate $oldProducts ',$oldProducts);
//if(!class_exists('VmModel'))require($_SERVER['DOCUMENT_ROOT'] . "/ModaFinala/administrator/components/com_virtuemart/" . 'helpers' . "/" . 'vmmodel.php');
	$productModel = VmModel::getModel('product');

//			$alreadyKnownIds = $this->getMigrationProgress('products');
//			$oldToNewCats = $this->getMigrationProgress('cats');
	$arrToadd = array();
	
//	$arrToadd['product_pictures_in_desc'] = $pictures;
	// Attributes End --- Start create the $product array to unserialize?
//					$product['categories'] = $productcategories;
//	$product['product_desc'] = $pictures . $product['product_desc'];
	$product['product_desc'] =  $product['product_desc'];

//                                        $this->mine_printOnfile($product, 'product');

	$product['product_currency'] = $this->_ensureUsingCurrencyId($product['product_currency']);
//                                        $arrToadd['product_currency'] = $product['product_currency'];
	// Here we  look for the url product_full_image and check which media has the same
	//@import full_image url   -- Qui si imposta la galleria 

	if ($product['virtuemart_product_id']) {
	    $mediaModel = VmModel::getModel('media');
	    $medias = $mediaModel->getFiles(false, false, $product['virtuemart_product_id']); //, $cat_id=null, $where=array(),$nbr=false);
	    if (count($medias) >= 0) {
                foreach($medias as $media)
		$this->deletePictureFileFromFolder($media->file_url, $media->file_url_thumb);
	    }
	}
        
        $pos = strrpos($product['product_full_image'], "http://www.");
		// put off price from Label
                if ($pos !== false) { 
                    $folderForCalc = str_replace('http://www.easycommercemanager.com/immaginiEbay' , '' ,$product['product_full_image']); 
                    } else { 
                        $folderForCalc = str_replace('http://easycommercemanager.com/immaginiEbay' , '' ,$product['product_full_image']); 
                                
                    }
        
// $fromImmToTheEnd =  substr($prooova, 13);  // should be immaginiEbay/ --> in poi
                    
                    
                   
                                           
                    
                    
              $imgFiln = substr(strrchr($product['product_full_image'], "/"), 1);
		
		$folder = str_replace($imgFiln, "", $folderForCalc);
               $imgs_id = array() ;
//        $pictures = $this->mine_RetrievePicturePerProduct($product['tag_alt'] ); // take all pictures and write them inside description
               $product['virtuemart_media_id'] = array();
	$product['virtuemart_media_ordering'] = array();
        $counter = 0 ;
        
                
               array_unshift($product['pictures'], array('thumb' => $product['product_thumb_image'], 'big'=>$product['product_full_image'] ));
        
        $key = 0;
        foreach($product['pictures'] as $key => $picture){
             // let's save thumb also 
	     if (!is_array($picture)) continue; 
                $filename=  substr(strrchr($picture['thumb'], "/"), 1);
                
             if($key == 0) {  // SPECIAL BEHAVIOUR FOR GALLERY DON'T TOUCH 
                    $filename = str_replace(".jpeg","_thm.jpeg",$filename);
                    $filename= str_replace(".jpg","_thm.jpg",$filename);
                }
		$url_thumb = $this->savePhoto($picture['thumb'], $filename ,$folder, 1);  // it's already resized!
		$arrayMedia['file_url_thumb'] = $url_thumb;
                
               
	    

//						$product['virtuemart_media_id'] = $this->_getMediaIdByName($product['product_full_image'],'product');
	    $arrayMedia['filename'] = $product['product_name'].$counter ; $counter ++;
//             $arrayMedia['file_url_thumb']
            
	    $filename=  substr(strrchr($picture['big'], "/"), 1);
//	    $isbn = str_replace("/", "", $pp);
            
             if($key == 0) {
                    $filename = str_replace(".jpeg","_gll.jpeg",$filename);
                    $filename= str_replace(".jpg","_gll.jpg",$filename);
                }
	    $url = $this->savePhoto($picture['big'],$filename ,$folder, 0);  //images/stories/virtuemart/category/fc2f001413876a374484df36ed9cf775.jpg  --   --images/stories/virtuemart/category/resized/fc2f001413876a374484df36ed9cf775_90x90.jpg
	    $imgId = $this->insertANewImageForGallery($arrayMedia, $url, $hash);
           
            
            $product['virtuemart_media_id'][$key] = $imgId;
            $product['virtuemart_media_ordering'][$imgId] = $key;
            $key++;
                             
        }
        
//        if (!empty($product['product_full_image'])) {
//
//	    // let's save thumb also 
//	    if (!empty($product['product_thumb_image'])) {
//		$pp = strrchr($product['product_thumb_image'], "/");
//		$isbn = str_replace("/", "", $pp);
//                                            $isbn = str_replace(".jpeg","_thm.jpeg",$isbn);
//                                            $isbn = str_replace(".jpg","_thm.jpg",$isbn);
//		$url_thumb = $this->savePhoto($product['product_thumb_image'], $isbn, $folder, $isthumb = true);
//		$arrayMedia['file_url_thumb'] = $url_thumb;
//	    }
//
////						$product['virtuemart_media_id'] = $this->_getMediaIdByName($product['product_full_image'],'product');
//	    $arrayMedia['filename'] = $product['product_name'].$counter ; $counter ++;
//
//
//	    $pp = strrchr($product['product_full_image'], "/");
//	    $isbn = str_replace("/", "", $pp);
//	    $url = $this->savePhoto($product['product_full_image'], $isbn , $folder , 0);  //images/stories/virtuemart/category/fc2f001413876a374484df36ed9cf775.jpg  --   --images/stories/virtuemart/category/resized/fc2f001413876a374484df36ed9cf775_90x90.jpg
//	    $galleryId = $this->insertANewImageForGallery($arrayMedia, $url, $hash);
//	    $this->LinkGalleryToAProduct($url, $galleryId, $product, $arrToadd, $hash);
//	    //$product['active_media_id'] = $product['virtuemart_media_id']; $product['media_action']=0;
//            $product['virtuemart_media_id'][0] = $galleryId;
//	$product['virtuemart_media_ordering'][$galleryId] = 0;
//	}

//                                        exit(1);
	

//					
//                                         $productModel->
	$old_price_ids = $productModel->loadProductPrices($product['virtuemart_product_id'], '', NULL, false);
	if (is_array($old_price_ids) and count($old_price_ids) > 0) {
	    $product['mprices']['virtuemart_product_price_id'] = array($old_price_ids[0]['virtuemart_product_price_id']);
	}
	$product['virtuemart_product_id'] = $productModel->store($product);
	$arrToadd['virtuemart_product_id'] = $product['virtuemart_product_id'];

//					$errors = $productModel->getErrors();
//					if(!empty($errors)){
//						foreach($errors as $error){
//							$_SESSION['vmError'][]='Product with id '.$product['virtuemart_product_id'].' Creation failed in following point: '.$i.' ' . $error;
//						}
////						vmdebug('Product add error',$product);
////						$productModel->resetErrors();
//						$continue = false;
//						break;
//					}

	$errors = $this->EvaluateErrors();

	if ($this->_dev) {
//                                            ($prouct)? $dati = 'arrivati correttamente ': $dati = 'non arrivati correttamente';
	    $_SESSION['vmInfo'][] = 'Product Save ok with id ' . $product['virtuemart_product_id'] . ' in function CreateProduct con host ' . $_SERVER['HTTP_HOST'];
	} else
	    $_SESSION['vmInfo'][] = 'Product Save ok   ';
//					
//		}
//		$this->storeMigrationProgress('products',$alreadyKnownIds);
	vmInfo('Migration: ' . $i . ' products processed ');

	return $arrToadd;
    }
    
    public function createProductGiuseppeTrial($product, $hash, $_ParentId ,$childrenArrayIds) {

		
	//vmdebug('in product migrate $oldProducts ',$oldProducts);
//if(!class_exists('VmModel'))require($_SERVER['DOCUMENT_ROOT'] . "/ModaFinala/administrator/components/com_virtuemart/" . 'helpers' . "/" . 'vmmodel.php');
	$productModel = VmModel::getModel('product');

//			$alreadyKnownIds = $this->getMigrationProgress('products');
//			$oldToNewCats = $this->getMigrationProgress('cats');
	$arrToadd = array();
        
        $mediaModel = VmModel::getModel('media');
        if ($product['virtuemart_product_id']) {
	    
	    $medias = $mediaModel->getFiles(false, false, $product['virtuemart_product_id']); //, $cat_id=null, $where=array(),$nbr=false);
	    if (count($medias) >= 0) {
                foreach($medias as $media){
                    $this->deletePictureFileFromFolder($media->file_url, $media->file_url_thumb);
                    $mediaRemoved = $mediaModel->remove($media->virtuemart_media_id);
                }
		
	    }
	}
        
        $pos = strrpos($product['product_full_image'], "http://www.");
		// put off price from Label
                if ($pos !== false) { 
                    $folderForCalc = str_replace('http://www.easycommercemanager.com/immaginiEbay' , '' ,$product['product_full_image']); 
                    } else { 
                          if (strrpos($product['product_full_image'], "localhost") !== false) {
                                $folderForCalc = str_replace('http://localhost.dev/immaginiEbay' , '' ,$product['product_full_image']); 
                            } else {
                                $folderForCalc = str_replace('http://easycommercemanager.com/immaginiEbay' , '' ,$product['product_full_image']); 
                            }
                    }
                    $imgFiln = substr(strrchr($product['product_full_image'], "/"), 1);
		
		$folder = str_replace($imgFiln, "", $folderForCalc);
               $imgs_id = array() ;
//        $pictures = $this->mine_RetrievePicturePerProduct($product['tag_alt'] ); // take all pictures and write them inside description
               $product['virtuemart_media_id'] = array();
	$product['virtuemart_media_ordering'] = array();
        $counter = 0 ;
        
                
               array_unshift($product['pictures'], array('thumb' => $product['product_thumb_image'], 'big'=>$product['product_full_image'] ));
        
        $key = 0;
        $arrayVirtuemart_media_id = array();
        $arrayVirtuemart_media_ordering= array();
        foreach($product['imagesArray'] as $key => $picture){
             // let's save thumb also 
	     if (!is_array($picture)) continue; 
                $filename=  substr(strrchr($picture['thumb'], "/"), 1);
                
             if($key == 0) {  // SPECIAL BEHAVIOUR FOR GALLERY DON'T TOUCH 
                    $filename = str_replace(".jpeg","_thm.jpeg",$filename);
                    $filename= str_replace(".jpg","_thm.jpg",$filename);
                }
		$url_thumb = $this->savePhoto($picture['thumb'], $filename ,$folder, 1);  // it's already resized!
		$arrayMedia['file_url_thumb'] = $url_thumb;
                
               
	    

//						$product['virtuemart_media_id'] = $this->_getMediaIdByName($product['product_full_image'],'product');
	    $arrayMedia['filename'] = $product['product_name'].$counter ; $counter ++;
//             $arrayMedia['file_url_thumb']
            
	    $filename=  substr(strrchr($picture['big'], "/"), 1);
//	    $isbn = str_replace("/", "", $pp);
            
             if($key == 0) {
                    $filename = str_replace(".jpeg","_gll.jpeg",$filename);
                    $filename= str_replace(".jpg","_gll.jpg",$filename);
                }
	    $url = $this->savePhoto($picture['big'],$filename ,$folder, 0);  //images/stories/virtuemart/category/fc2f001413876a374484df36ed9cf775.jpg  --   --images/stories/virtuemart/category/resized/fc2f001413876a374484df36ed9cf775_90x90.jpg
	    $imgId = $this->insertANewImageForGallery($arrayMedia, $url, $hash);
           
            
            $arrayVirtuemart_media_id[$key] = $imgId;
            $arrayVirtuemart_media_ordering[$imgId] = $key;
            $key++;
                             
        }
        
            $product['virtuemart_media_id']= $arrayVirtuemart_media_id;
            $product['virtuemart_media_ordering'] = $arrayVirtuemart_media_ordering;
        
	
//                                        $this->mine_printOnfile($product, 'product');

        /**
         * *********************************************************
         * Start Commmenting to do A very simple Trial
         * *********************************************************
          if children array is set I am creating father so let's inserti also the children array information
         * 
         * 
         * 'childs' => 
  array (
    71 =>   // children id 
    array (
      'product_name' => 'Product Father - color 1',
      'product_gtin' => '',
      'mprices' => 
      array (
        'product_price' => 
        array (
          0 => '',
        ),
        'virtuemart_product_price_id' => 
        array (
          0 => '',
        ),
      ),
      'pordering' => '0',
      'published' => '1',
    ),
         * 
         */
            
            $childrensArr = array();
            if (isset($childrenArrayIds) && count($childrenArrayIds)>0) {
                $count= 0;
                foreach ($childrenArrayIds as $prodId => $children){
                    $child = array();
                    $count++;
                    $child ['product_name'] = $children['product_name'] ;
                    $child ['product_gtin'] = $children['product_gtin'] ;
                        
                    $child['mprices'] = array ('product_price' => array (0 => '',), 'virtuemart_product_price_id' => array (0 => '',),);
                        $child['pordering'] = $count;
                        $child['published'] = 1;
                    
                        $childrensArr[$children['virtuemart_product_id']] = $child ;
                }
                
                $product['childs']=$childrensArr ;
                var_export($childrensArr);
//                echo '<h1 style="color:purple"> Deactivate Jsontrial line 1363 </h1>';
//                exit(1);
            }
            
            
            
	$product['virtuemart_product_id'] = $productModel->store($product);
	$arrToadd['virtuemart_product_id'] = $product['virtuemart_product_id'];

//					$errors = $productModel->getErrors();
//					if(!empty($errors)){
//						foreach($errors as $error){
//							$_SESSION['vmError'][]='Product with id '.$product['virtuemart_product_id'].' Creation failed in following point: '.$i.' ' . $error;
//						}
////						vmdebug('Product add error',$product);
////						$productModel->resetErrors();
//						$continue = false;
//						break;
//					}

	$errors = $this->EvaluateErrors();

	if ($this->_dev) {
//                                            ($prouct)? $dati = 'arrivati correttamente ': $dati = 'non arrivati correttamente';
	    $_SESSION['vmInfo'][] = 'Product Save ok with id ' . $product['virtuemart_product_id'] . ' in function CreateProduct con host ' . $_SERVER['HTTP_HOST'];
	} else
	    $_SESSION['vmInfo'][] = 'Product Save ok   ';
//					
//		}
//		$this->storeMigrationProgress('products',$alreadyKnownIds);
	vmInfo('Migration: ' . $i . ' products processed ');

	return $arrToadd;
    }
    

    function translateProduct($product, $vmLang) {


	//vmdebug('in product migrate $oldProducts ',$oldProducts);

	$productModel = VmModel::getModel('product');



	$product['virtuemart_product_id'] = $productModel->store($product);


	$errors = $productModel->getErrors();
	if (!empty($errors)) {
	    foreach ($errors as $error) {
		$_SESSION['vmError'][] = 'Product with id ' . $product['virtuemart_product_id'] . ' Creation failed in following point: ' . $i . ' ' . $error;
	    }
//						vmdebug('Product add error',$product);
	    $productModel->resetErrors();
	    $continue = false;
//	    break;
	} elseif ($this->_dev)
	    $_SESSION['vmInfo'][] = 'Product Save ok with id ' . $product['virtuemart_product_id'] . ' in function CreateProduct con host ' . $_SERVER['HTTP_HOST'] . ' e dati' . json_encode($product);
	else
	    $_SESSION['vmInfo'][] = 'Product Save ok with id ' . $product['virtuemart_product_id'] . ' in function CreateProduct con host ' . $_SERVER['HTTP_HOST'] . ' e dati' . json_encode($product);
//				


	return $ok;
    }

    public function translationProduct($data, $lang) {
	$productModel = VmModel::getModel('product');
//            $product_data = $productModel->getTable ('products'); se ci fosse qualche this
	$ok = true;
	if (!class_exists('VmTableData'))
	    require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmtabledata.php');
	$db = JFactory::getDBO();

	$langTable = new VmTableData('#__virtuemart_products_' . $lang, 'virtuemart_product_id', $db);

	$langTable->setPrimaryKey('virtuemart_product_id');

	$langData = array();
	$langData['virtuemart_product_id'] = $data['virtuemart_product_id'];
	$langData['product_name'] = $data['product_name'];
	$langData['product_s_desc'] = $data['product_s_desc'];
	$langData['product_desc'] = $data['product_desc'];
	$langData['metadesc'] = $data['metadesc'];
	$langData['metakey'] = $data['metakey'];
//                        $langData['customtitle'] = $data['customtitle']; 
	$langData['customtitle'] = $data['product_name'];
	$langData['slug'] = $data['slug'];
//  [product_s_desc] => (string) Sales and discounts on Adidas Hooded manufacturer\'s Bayer Leverkusen Season 2011 2012
//  [product_desc] => (string) Sales and discounts on Adidas Hooded manufacturer\'s Bayer Leverkusen Season 2011 2012<br>Season 2011 2012. Main Color: Red and Black<br>The product has no zip pockets. Outer fabric: 70% cotton 30% polyester, hood lining: 95% cotton 5% elastane.<br>Made in China. Adidas logo and emblem sewn Leveerkusen Bayer and adidas red stripes sewn side.<br>The product has a normal wearability. Good for training and running, very comfortable.<br><br><br>Per la scelta delle taglie controllate le misure sottostanti<br />
////                          <img src="http://www.modacalcio.com/immaginiEbay/taglie/adidasFelpaCappuccio.jpg" alt="Tabella taglie e misure Felpa Cappuccio Stagione 2011 2012 BayerLeverkusen"><br /> Codice Fornitore: Adidas U40464 Bayer<br /> Codice ERP Interno: MG-678<br />Tag Ricerca:  leverkusen
//  [metadesc] => (string) Sales and discounts on Adidas Hooded manufacturer\'s Bayer Leverkusen Season 2011 2012
//  [metakey] => (string) Hooded sweatshirt Adidas Bayer Leverkusen Season 2011 2012
//  [customtitle] => (string)
//  [slug] => (string) Hooded_sweatshirt_Adidas_Bayer_Leverkusen_Season_2011_2012
//)
// possiamo fare anche le query utilizzando  JDatabaseMySQLi setQuery
//$this->_db->setQuery($_qry);
//				$this->$tblKey = $this->_db->loadResult();

	/**  anche così in cui $this == JDatabaseMySQLi object 
	 * $this->setQuery(sprintf($statement, implode(',', $fields), implode(',', $values)));
	  if (!$this->execute())
	  {
	  return false;
	  }

	  // Update the primary key if it exists.
	  $id = $this->insertid();
	  if ($key && $id)
	  {
	  $object->$key = $id;
	  }

	 */
	$langTable->setProperties($langData);
	$langTable->_translatable = false;

	if (!$langTable->bind($langData)) {
	    $ok = false;
	    $msg = 'bind';
	    // 			vmdebug('Problem in bind '.get_class($this).' '.$this->_db->getErrorMsg());
	    vmdebug('Problem in bind ' . get_class($this) . ' ');
	}

	if ($ok) {
	    if (!$langTable->store()) {
		$ok = false;
		// $msg .= ' store';
		vmdebug('Problem in store with langtable ' . get_class($langTable) . ' with ' . $tblKey . ' = ' . $this->$tblKey . ' ' . $langTable->_db->getErrorMsg());
	    }
	}

	return $ok;
    }

    public function getProductCategories($product_id) {
	$productModel = VmModel::getModel('product');
//			return $productModel->getProductCategories ($product_id );
	$categories = array();
	if ($product_id > 0) {
	    $q = 'SELECT pc.`virtuemart_category_id` FROM `#__virtuemart_product_categories` as pc';

	    $q .= ' WHERE pc.`virtuemart_product_id` = ' . (int) $product_id;

	    $productModel->_db->setQuery($q);
	    $categories = $productModel->_db->loadResultArray();
	}

	return $categories;
    }

    public function GetChildsId($data) {
	$productModel = VmModel::getModel('product');
	return $productModel->getProductChildIds($data['virtuemart_product_id']);
    }

    public function deleteProduct($data) {
	;
	$productModel = VmModel::getModel('product');
	if ($productModel->remove(array($data['virtuemart_product_id']))) {
	    $_SESSION['vmInfo']['result'] = 'Success';
	    $_SESSION['vmInfo']['msg'] = 'Product with id' . $product['virtuemart_product_id'] . ' deleted correctly ' . $_SERVER['HTTP_HOST'] . ' e dati' . json_encode($data);

	    return false;
	} else {
	    $_SESSION['vmInfo']['result'] = 'Failure';
	    $_SESSION['vmInfo']['msg'] = 'Product can\t be deleted with id' . $product['virtuemart_product_id'] . ' in function delete product con host ' . $_SERVER['HTTP_HOST'] . ' e dati' . json_encode($data);

	    return false;
	}
    }

    public function unpublishProduct($data) {
	$productModel = VmModel::getModel('product');
//		$_REQUEST['virtuemart_product_id']['0'] = $data['virtuemart_product_id'];
	$id = $data['product']['virtuemart_product_id'];
	JRequest::setVar('virtuemart_product_id', array($data['product']['virtuemart_product_id']), 'POST');

	if (!$productModel->toggle('published', 0, 'virtuemart_product_id', $mainTable = 0)) { //0 means go to _products
	    $_SESSION['vmInfo']['result'] = 'Failure';
	    $_SESSION['vmInfo']['msg'] = 'Price can t be unpublished for some problem with id' . $product['virtuemart_product_id'] . ' in function unpublishProduct con host ' . $_SERVER['HTTP_HOST'] . ' e dati' . json_encode($data);

	    return false;
	}

//                else{
//			 $_SESSION['vmInfo']['result']='Success' ;
//                                            $_SESSION['vmInfo']['msg']='Price edit with no problem for product id '.$product['virtuemart_product_id'] .'in function unpublishProduct con host '.$_SERVER['HTTP_HOST']. ' e dati'.json_encode($data) ; 
//		}
//                if (!$productModel->toggle('product_in_stock', 0, 'virtuemart_product_id', $mainTable = 0)) { //0 means go to _products
//			 $_SESSION['vmInfo']['result']='Failure' ;
//                                            $_SESSION['vmInfo']['msg']='Price can t be unpublished for some problem with id'.$product['virtuemart_product_id'].' in function unpublishProduct con host '.$_SERVER['HTTP_HOST']. ' e dati'.json_encode($data) ; 
//		} else{
//			 $_SESSION['vmInfo']['result']='Success' ;
//                                            $_SESSION['vmInfo']['msg']='Price edit with no problem for product id '.$product['virtuemart_product_id'] .'in function unpublishProduct con host '.$_SERVER['HTTP_HOST']. ' e dati'.json_encode($data) ; 
//		}

	$q = 'UPDATE `#__virtuemart_products` SET product_in_stock = 0 WHERE `virtuemart_product_id` = ' . $id;

	$productModel->_db->setQuery($q);
	if ($productModel->_db->query()) {
	    $_SESSION['vmInfo']['result'] = 'Success';
	    $_SESSION['vmInfo']['msg'] = 'Unpublish and setting stock 0 done for product id ' . $product['virtuemart_product_id'] . 'in function unpublishProduct con host ' . $_SERVER['HTTP_HOST'] . ' e dati' . json_encode($data);
	    return true;
	} else {
	    $_SESSION['vmInfo']['result'] = 'Failure';
	    $_SESSION['vmInfo']['msg'] = 'Wasn\'t able to make the product go out of stock with id' . $product['virtuemart_product_id'] . ' in function unpublishProduct con host ' . $_SERVER['HTTP_HOST'] . ' e dati' . json_encode($data);
	    return false;
	}
//                $product_data = $productModel->getTable ('products');
//                $stored = $product_data->bindChecknStore ($data, TRUE);
//		$productModel->updateXrefAndChildTables ($data['product'], 'product_prices');
    }

    public function DetectEventualDuplicate($GridProductId, $language = '_de_de') {
//            SELECT * FROM `aatw2_virtuemart_products_de_de`  1274%' ORDER BY `virtuemart_product_id` ASC
	$productModel = VmModel::getModel('product');
	$q = ' SELECT virtuemart_product_id FROM `#__virtuemart_products' . $language . '` WHERE `slug` LIKE \'%' . $GridProductId . '%\'';

	$productModel->_db->setQuery($q);
	if (!count($virtuemart_id = $productModel->_db->loadAssocList()) > 0) {
	    $_SESSION['vmInfo']['result'] = 'Success';
	    $_SESSION['vmInfo']['msg'] = ' Nessun duplicato trovato in tabella aatw2_virtuemart_products_' . $language . ' ed id incoming dati' . $GridProductId;
	    return true;
	} else {
	    $_SESSION['vmInfo']['result'] = 'Failure';
	    $_SESSION['vmInfo']['msg'] = 'Found one ghost duplicate with id $virtuemart_id:"' . $virtuemart_id['virtuemart_product_id'] . ' in function aatw2_virtuemart_products_' . $language . ' ed id incoming dati' . $GridProductId;
	    return $virtuemart_id[0]['virtuemart_product_id'];
	}
    }

    public function CleanGhostProducts($virtuemart_id) {
	$productModel = VmModel::getModel('product');
	$q = "  DELETE FROM `aatw2_virtuemart_products`  WHERE  `virtuemart_product_id` =$virtuemart_id; ";
//                    DELETE FROM `aatw2_virtuemart_product_customfields`  WHERE  `virtuemart_product_id` =$virtuemart_id;
//                    DELETE FROM `aatw2_virtuemart_product_downloads` WHERE  `virtuemart_product_id` =$virtuemart_id;
//                    DELETE FROM `aatw2_virtuemart_product_manufacturers` WHERE  `virtuemart_product_id` =$virtuemart_id;
//                    DELETE FROM `aatw2_virtuemart_product_medias` WHERE  `virtuemart_product_id` =$virtuemart_id;
//                    DELETE FROM `aatw2_virtuemart_product_prices` WHERE  `virtuemart_product_id` =$virtuemart_id;
//                    DELETE FROM `aatw2_virtuemart_product_relations` WHERE  `virtuemart_product_id` =$virtuemart_id;
//                    DELETE FROM `aatw2_virtuemart_product_shoppergroups` WHERE  `virtuemart_product_id` =$virtuemart_id;
//                    DELETE FROM `aatw2_virtuemart_products_en_gb` WHERE  `virtuemart_product_id` =$virtuemart_id;
//                    DELETE FROM `aatw2_virtuemart_products_fr_fr` WHERE  `virtuemart_product_id` =$virtuemart_id;
//                    DELETE FROM `aatw2_virtuemart_products_de_de` WHERE  `virtuemart_product_id` =$virtuemart_id;
//                    DELETE FROM `aatw2_virtuemart_products_it_it` WHERE  `virtuemart_product_id` =$virtuemart_id; ";

	$productModel->_db->setQuery($q);
	if (!$productModel->_db->loadResult()) {
	    $_SESSION['vmInfo']['result'] = 'Failure';
	    $_SESSION['vmInfo']['msg'] = 'Not able to delete product with id :"' . $virtuemart_id . '" With Query ' . $q;
	    return false;
	}

	$q = "                    DELETE FROM `aatw2_virtuemart_product_categories`  WHERE  `virtuemart_product_id` =" . $virtuemart_id;

	$productModel->_db->setQuery($q);
	if (!$productModel->_db->loadResult()) {
	    $_SESSION['vmInfo']['result'] = 'Failure';
	    $_SESSION['vmInfo']['msg'] = 'Not able to delete product with id :"' . $virtuemart_id . '" With Query ' . $q;
	    return false;
	}

	$q = "DELETE FROM `aatw2_virtuemart_product_downloads` WHERE  `virtuemart_product_id` =" . $virtuemart_id;
	$productModel->_db->setQuery($q);
	if (!$productModel->_db->loadResult()) {
	    $_SESSION['vmInfo']['result'] = 'Failure';
	    $_SESSION['vmInfo']['msg'] = 'Not able to delete product with id :"' . $virtuemart_id . '" With Query ' . $q;
	    return false;
	}

	$q = "DELETE FROM `aatw2_virtuemart_product_manufacturers` WHERE  `virtuemart_product_id` =" . $virtuemart_id;
	$productModel->_db->setQuery($q);
	if (!$productModel->_db->loadResult()) {
	    $_SESSION['vmInfo']['result'] = 'Failure';
	    $_SESSION['vmInfo']['msg'] = 'Not able to delete product with id :"' . $virtuemart_id . '" With Query ' . $q;
	    return false;
	}

	$q = "ELETE FROM `aatw2_virtuemart_product_medias` WHERE  `virtuemart_product_id` =" . $virtuemart_id;
	$productModel->_db->setQuery($q);
	if (!$productModel->_db->loadResult()) {
	    $_SESSION['vmInfo']['result'] = 'Failure';
	    $_SESSION['vmInfo']['msg'] = 'Not able to delete product with id :"' . $virtuemart_id . '" With Query ' . $q;
	    return false;
	}

	$q = "DELETE FROM `aatw2_virtuemart_product_prices` WHERE  `virtuemart_product_id` =" . $virtuemart_id;
	$productModel->_db->setQuery($q);
	if (!$productModel->_db->loadResult()) {
	    $_SESSION['vmInfo']['result'] = 'Failure';
	    $_SESSION['vmInfo']['msg'] = 'Not able to delete product with id :"' . $virtuemart_id . '" With Query ' . $q;
	    return false;
	}

	$q = "DELETE FROM `aatw2_virtuemart_product_shoppergroups` WHERE  `virtuemart_product_id` =" . $virtuemart_id;
	$productModel->_db->setQuery($q);
	if (!$productModel->_db->loadResult()) {
	    $_SESSION['vmInfo']['result'] = 'Failure';
	    $_SESSION['vmInfo']['msg'] = 'Not able to delete product with id :"' . $virtuemart_id . '" With Query ' . $q;
	    return false;
	}

	$q = "DELETE FROM `aatw2_virtuemart_products_en_gb` WHERE  `virtuemart_product_id` =" . $virtuemart_id;
	$productModel->_db->setQuery($q);
	if (!$productModel->_db->loadResult()) {
	    $_SESSION['vmInfo']['result'] = 'Failure';
	    $_SESSION['vmInfo']['msg'] = 'Not able to delete product with id :"' . $virtuemart_id . '" With Query ' . $q;
	    return false;
	}

	$q = " DELETE FROM `aatw2_virtuemart_products_fr_fr` WHERE  `virtuemart_product_id` =" . $virtuemart_id;
	$productModel->_db->setQuery($q);
	if (!$productModel->_db->loadResult()) {
	    $_SESSION['vmInfo']['result'] = 'Failure';
	    $_SESSION['vmInfo']['msg'] = 'Not able to delete product with id :"' . $virtuemart_id . '" With Query ' . $q;
	    return false;
	}

	$q = " DELETE FROM `aatw2_virtuemart_products_de_de` WHERE  `virtuemart_product_id` =" . $virtuemart_id;
	$productModel->_db->setQuery($q);
	if (!$productModel->_db->loadResult()) {
	    $_SESSION['vmInfo']['result'] = 'Failure';
	    $_SESSION['vmInfo']['msg'] = 'Not able to delete product with id :"' . $virtuemart_id . '" With Query ' . $q;
	    return false;
	}

	$q = " DELETE FROM `aatw2_virtuemart_products_it_it` WHERE  `virtuemart_product_id` =" . $virtuemart_id;
	$productModel->_db->setQuery($q);
	if (!$productModel->_db->loadResult()) {
	    $_SESSION['vmInfo']['result'] = 'Failure';
	    $_SESSION['vmInfo']['msg'] = 'Not able to delete product with id :"' . $virtuemart_id . '" With Query ' . $q;
	    return false;
	}
    }

    public function EditPricesVirtuemartCode($data, $virtuemartProductID, $isChild = false) {
	$productModel = VmModel::getModel('product');

//        $old_price_ids =$productModel-> 
	$arrayPricesArrived = $data['mprices']['product_price'];
	foreach ($arrayPricesArrived as $k => $product_price) {
	    $pricesToStore = array();
	    $pricesToStore['virtuemart_product_id'] = $virtuemartProductID;
	    $pricesToStore['virtuemart_product_price_id'] = (int) $data['mprices']['virtuemart_product_price_id'][$k];


	    if (!$isChild) {
		//$pricesToStore['basePrice'] = $data['mprices']['basePrice'][$k];
		$pricesToStore['product_override_price'] = $data['mprices']['product_override_price'][$k];
		$pricesToStore['override'] = (int) $data['mprices']['override'][$k];
		$pricesToStore['virtuemart_shoppergroup_id'] = (int) $data['mprices']['virtuemart_shoppergroup_id'][$k];
		$pricesToStore['product_tax_id'] = (int) $data['mprices']['product_tax_id'][$k];
		$pricesToStore['product_discount_id'] = (int) $data['mprices']['product_discount_id'][$k];
		$pricesToStore['product_currency'] = (int) $data['mprices']['product_currency'][$k];
		$pricesToStore['product_price_publish_up'] = $data['mprices']['product_price_publish_up'][$k];
		$pricesToStore['product_price_publish_down'] = $data['mprices']['product_price_publish_down'][$k];
		$pricesToStore['price_quantity_start'] = (int) $data['mprices']['price_quantity_start'][$k];
		$pricesToStore['price_quantity_end'] = (int) $data['mprices']['price_quantity_end'][$k];
	    }

	    if (!$isChild and isset($data['mprices']['use_desired_price'][$k]) and $data['mprices']['use_desired_price'][$k] == "1") {
		if (!class_exists('calculationHelper')) {
		    require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'calculationh.php');
		}
		$calculator = calculationHelper::getInstance();
		$pricesToStore['salesPrice'] = $data['mprices']['salesPrice'][$k];
		$pricesToStore['product_price'] = $data['mprices']['product_price'][$k] = $calculator->calculateCostprice($virtuemartProductID, $pricesToStore);
		unset($data['mprices']['use_desired_price'][$k]);
	    } else {
		if (isset($data['mprices']['product_price'][$k])) {
		    $pricesToStore['product_price'] = $data['mprices']['product_price'][$k];
		}
	    }

	    if ($isChild)
		$childPrices = $productModel->loadProductPrices($virtuemartProductID, 0, 0, false);

	    if ((isset($pricesToStore['product_price']) and $pricesToStore['product_price'] != '') || (isset($childPrices) and count($childPrices) > 1)) {

		if ($isChild) {
		    //$childPrices = $this->loadProductPrices($pricesToStore['virtuemart_product_price_id'],0,0,false);

		    if (is_array($old_price_ids) and count($old_price_ids) > 1) {

			//We do not touch multiple child prices. Because in the parent list, we see no price, the gui is
			//missing to reflect the information properly.
			$pricesToStore = false;
			$old_price_ids = array();
		    } else {
			unset($data['mprices']['product_override_price'][$k]);
			unset($pricesToStore['product_override_price']);
			unset($data['mprices']['override'][$k]);
			unset($pricesToStore['override']);
		    }
		}

		//$data['mprices'][$k] = $data['virtuemart_product_id'];
		if ($pricesToStore) {
		    $toUnset = array();
		    foreach ($old_price_ids as $key => $oldprice) {
			if (array_search($pricesToStore['virtuemart_product_price_id'], $oldprice)) {
			    $pricesToStore = array_merge($oldprice, $pricesToStore);
			    $toUnset[] = $key;
			}
		    }
		    $productModel->updateXrefAndChildTables($pricesToStore, 'product_prices', $isChild);

		    foreach ($toUnset as $key) {
			unset($old_price_ids[$key]);
		    }
		}
	    }
	}
    }

    public function editProductPrices($data, $isChild = false) {

	$productModel = VmModel::getModel('product');

	$old_price_ids = $productModel->loadProductPrices($data['product']['virtuemart_product_id'], '', NULL, false);
	if (is_array($old_price_ids) and count($old_price_ids) > 0) {
	    $data['product']['mprices']['virtuemart_product_price_id'] = array($old_price_ids[0]['virtuemart_product_price_id']);
	}

//        $data['product']['product_price'] = (float) $data['product']['product_price'] / 1.21;
//			$productModel->updateXrefAndChildTables ($data['product']['mprices'], 'product_prices');
	$this->EditPricesVirtuemartCode($data['product'], $data['product']['virtuemart_product_id'], $isChild);

	if ($data['field']) {
	    $product_id = $data['product']['virtuemart_product_id'];
	    unset($data['product']);
	    $this->editCartAttributes($data, $product_id);
	}

	if (isset($data['categories'])) {
	    if (count($data['categories']) > 0) {
		$this->UpdateCategories($data);
	    }
	}

	$errors = $productModel->getErrors();
	if (!empty($errors)) {
	    foreach ($errors as $error) {
		$_SESSION['vmError'][] = 'Product with id ' . $data['product']['virtuemart_product_id'] . ' Creation failed in following point: ' . $i . ' ' . $error;
	    }
	    $_SESSION['vmInfo']['result'] = 'Failure';
	    $_SESSION['vmInfo']['msg'] = 'Price not edited';
//						vmdebug('Product add error',$product);
	    $productModel->resetErrors();
	    $continue = false;
//	    break;
	} else {

	    $_SESSION['vmInfo']['result'] = 'Success';
	    $_SESSION['vmInfo']['msg'] = 'Price edit with no problem in function EditProductPrices for product id ' . $product['virtuemart_product_id'] . ' con host ' . $_SERVER['HTTP_HOST'] . 'e dati ' . json_encode($data);
	    return true;
	}
    }

    public function editCartAttributes($data, $product_id) {
	$productModel = VmModel::getModel('product');
	if (!class_exists('VirtueMartModelCustomfields')) {
	    require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'customfields.php');
	}

	if (!$product_id)
	    $product_id = $data['virtuemart_product_id'];
	VirtueMartModelCustomfields::storeProductCustomfields('product', $data, $product_id);

	if (isset($data['categories'])) {
	    if (count($data['categories']) > 0) {
		$this->UpdateCategories($data);
	    }
	}


	$errors = $productModel->getErrors();
	if (!empty($errors)) {
	    foreach ($errors as $error) {
		$_SESSION['vmError'][] = 'Product with id ' . $data['virtuemart_product_id'] . ' Creation failed in following point: ' . $i . ' ' . $error;
	    }
	    vmdebug('Product add error', $product);
	    $productModel->resetErrors();
	    $continue = false;
//	    break;
	} elseif ($this->_dev) {
	    $_SESSION['vmInfo']['result'] = 'Success';
	    ($data['field']) ? $dati = 'arrivati correttamente ' : $dati = 'non arrivati correttamente';
	    $_SESSION['vmInfo']['msg'] = 'Product Save ok with id in function edit cart attributes' . $data['virtuemart_product_id'] . 'con host ' . $_SERVER['HTTP_HOST'] . 'e dati' . $dati; //.json_encode($data) ;
	    return true;
	} else {
	    $_SESSION['vmInfo']['result'] = 'Success';
	    $_SESSION['vmInfo'][] = 'Product Save ok with id function edit cart attributes ' . $data['virtuemart_product_id'] . 'con host ' . $_SERVER['HTTP_HOST'] . 'e dati' . json_encode($data);
	    return false;
	}
    }

    function resumeProductWithCustom($data, $product_id) {

	$productModel = VmModel::getModel('product');
//		$_REQUEST['virtuemart_product_id']['0'] = $data['virtuemart_product_id'];
	$id = $data['product']['virtuemart_product_id'];
	JRequest::setVar('virtuemart_product_id', array($id), 'POST');

	if (!$productModel->toggle('published', 1, 'virtuemart_product_id', $mainTable = 0)) { //0 means go to _products
	    $_SESSION['vmInfo']['result'] = 'Failure';
	    $_SESSION['vmInfo']['msg'] = 'Price can t be unpublished for some problem with id' . $product['virtuemart_product_id'] . ' in function unpublishProduct con host ' . $_SERVER['HTTP_HOST'] . ' e dati' . json_encode($data);

	    return false;
	}

	$q = 'UPDATE `#__virtuemart_products` SET product_in_stock = 1 WHERE `virtuemart_product_id` = ' . $id;

	$productModel->_db->setQuery($q);
	if ($productModel->_db->query()) {
	    $_SESSION['vmInfo']['result'] = 'Success';
	    $_SESSION['vmInfo']['msg'] = 'Unpublish and setting stock 0 done for product id ' . $product['virtuemart_product_id'] . 'in function unpublishProduct con host ' . $_SERVER['HTTP_HOST'] . ' e dati' . json_encode($data);
	}

//            else {
//                $_SESSION['vmInfo']['result']='Failure' ;
//                                            $_SESSION['vmInfo']['msg']='Wasn\'t able to make the product go out of stock with id'.$product['virtuemart_product_id'].' in function unpublishProduct con host '.$_SERVER['HTTP_HOST']. ' e dati'.json_encode($data) ; 
//            }
	// now product is online again and we need to update the attributes
//            $productModel = VmModel::getModel('product');
	$productModel->updateXrefAndChildTables($data['product'], 'product_prices');

	if ($data['field']) {
	    $product_id = $data['product']['virtuemart_product_id'];
	    unset($data['product']);
	    if ($this->editCartAttributes($data, $product_id)) {
		return true;
	    } else {
		return false;
	    }
	}

	if (isset($data['categories'])) {
	    if (count($data['categories']) > 0) {
		$this->UpdateCategories($data);
	    }
	}
    }

    function editProduct($product) {


	//vmdebug('in product migrate $oldProducts ',$oldProducts);

	$productModel = VmModel::getModel('product');



	$product['virtuemart_product_id'] = $productModel->store($product);


	$errors = $productModel->getErrors();
	if (!empty($errors)) {
	    foreach ($errors as $error) {
		$_SESSION['vmError'][] = 'Product with id ' . $product['virtuemart_product_id'] . ' Creation failed in following point: ' . $i . ' ' . $error;
	    }
//						vmdebug('Product add error',$product);
	    $productModel->resetErrors();
	    $continue = false;
//	    break;
	} elseif ($this->_dev)
	    $_SESSION['vmInfo'][] = 'Product Save ok with id in editProduct ' . $data['virtuemart_product_id'] . 'con host ' . $_SERVER['HTTP_HOST'] . ' e dati' . json_encode($product);
	else
	    $_SESSION['vmInfo'][] = 'Product Save ok   ';
//				


	return $ok;
    }

    /**
     * Finds the media id in the vm2 table for a given filename
     *
     * @author Max Milbers
     * @author Valerie Isaksen
     *
     */
    var $mediaIdFilename = array();

    function _getMediaIdByName($filename, $type) {
	if (!empty($this->mediaIdFilename[$type][$filename])) {

	    return $this->mediaIdFilename[$type][$filename];
	} else {
	    $q = 'SELECT `virtuemart_media_id` FROM `#__virtuemart_medias`
										WHERE `file_title`="' . $this->_db->getEscaped($filename) . '"
										AND `file_type`="' . $this->_db->getEscaped($type) . '"';
	    $this->_db->setQuery($q);
	    $virtuemart_media_id = $this->_db->loadResult();
	    if ($this->_db->getErrors()) {
		vmError('Error in _getMediaIdByName', $this->_db->getErrorMsg());
	    }
	    if (!empty($virtuemart_media_id)) {
		$this->mediaIdFilename[$type][$filename] = $virtuemart_media_id;
		return $virtuemart_media_id;
	    } else {

		vmdebug('nothing found for ' . $type . ' ' . $filename);
	    }
	}
    }

    function portOrders() {

	if ((microtime(true) - $this->starttime) >= ($this->maxScriptTime)) {
	    return;
	}

	//approximatly 100 products take a 1 MB
	$maxItems = $this->_getMaxItems('Orders');

	$startLimit = 0;
	$i = 0;
	$continue = true;
	while ($continue) {

	    $q = 'SELECT `o`.*, `op`.*, `o`.`order_number` as `vm1_order_number`, `o2`.`order_number` as `nr2` FROM `#__vm_orders` as `o`
				LEFT OUTER JOIN `#__vm_order_payment` as `op` ON `op`.`order_id` = `o`.`order_id`
				LEFT JOIN `#__virtuemart_orders` as `o2` ON `o2`.`order_number` = `o`.`order_number`
				WHERE (o2.order_number) IS NULL LIMIT ' . $startLimit . ',' . $maxItems;

	    $res = self::loadCountListContinue($q, $startLimit, $maxItems, 'port Orders');
	    $oldOrders = $res[0];
	    $startLimit = $res[1];
	    $continue = $res[2];

	    if (!class_exists('VirtueMartModelOrderstatus'))
		require($JPATH_VM_ADMINISTRATOR . "/" . 'models' . "/" . 'orderstatus.php');

	    $oldtonewOrders = array();

	    //Looks like there is a problem, when the data gets tooo big,
	    //solved now with query directly ignoring already ported orders.
	    $alreadyKnownIds = $this->getMigrationProgress('orders');
	    $newproductIds = $this->getMigrationProgress('products');
	    $orderCodeToId = $this->createOrderStatusAssoc();

	    foreach ($oldOrders as $order) {

		if (!array_key_exists($order['order_id'], $alreadyKnownIds)) {
		    $orderData = new stdClass();

		    $orderData->virtuemart_order_id = null;
		    $orderData->virtuemart_user_id = $order['user_id'];
		    $orderData->virtuemart_vendor_id = $order['vendor_id'];
		    $orderData->order_number = $order['vm1_order_number'];
		    $orderData->order_pass = 'p' . substr(md5((string) time() . $order['order_number']), 0, 5);
		    //Note as long we do not have an extra table only storing addresses, the virtuemart_userinfo_id is not needed.
		    //The virtuemart_userinfo_id is just the id of a stored address and is only necessary in the user maintance view or for choosing addresses.
		    //the saved order should be an snapshot with plain data written in it.
		    //		$orderData->virtuemart_userinfo_id = 'TODO'; // $_cart['BT']['virtuemart_userinfo_id']; // TODO; Add it in the cart... but where is this used? Obsolete?
		    $orderData->order_total = $order['order_total'];
		    $orderData->order_subtotal = $order['order_subtotal'];
		    $orderData->order_tax = $order['order_tax'];
		    $orderData->order_shipment = $order['order_shipment'];
		    $orderData->order_shipment_tax = $order['order_shipment_tax'];
		    if (!empty($_cart->couponCode)) {
			$orderData->coupon_code = $order['coupon_code'];
			$orderData->coupon_discount = $order['coupon_discount'];
		    }
		    $orderData->order_discount = $order['order_discount'];

		    $orderData->order_status = $order['order_status'];

		    if (isset($_cart->virtuemart_currency_id)) {
			$orderData->user_currency_id = $this->getCurrencyIdByCode($order['order_currency']);
			//$orderData->user_currency_rate = $order['order_status'];
		    }
		    $orderData->virtuemart_paymentmethod_id = $order['payment_method_id'];
		    $orderData->virtuemart_shipmentmethod_id = $order['ship_method_id'];
		    //$orderData->order_status_id = $oldToNewOrderstates[$order['order_status']]


		    $_filter = JFilterInput::getInstance(array('br', 'i', 'em', 'b', 'strong'), array(), 0, 0, 1);
		    $orderData->customer_note = $_filter->clean($order['customer_note']);
		    $orderData->ip_address = $order['ip_address'];

		    $orderData->created_on = $this->_changeToStamp($order['cdate']);
		    $orderData->modified_on = $this->_changeToStamp($order['mdate']); //we could remove this to set modified_on today

		    $orderTable = $this->getTable('orders');
		    $orderTable->bindChecknStore($orderData);
		    $errors = $orderTable->getErrors();
		    if (!empty($errors)) {
			foreach ($errors as $error) {
			    $this->_app->enqueueMessage('Migration orders: ' . $error);
			}
			$continue = false;
			break;
		    }
		    $i++;
		    $newId = $oldtonewOrders[$order['order_id']] = $orderTable->virtuemart_order_id;

		    $q = 'SELECT * FROM `#__vm_order_item` WHERE `order_id` = "' . $order['order_id'] . '" ';
		    $this->_db->setQuery($q);
		    $oldItems = $this->_db->loadAssocList();
		    //$this->_app->enqueueMessage('Migration orderhistories: ' . $newId);
		    foreach ($oldItems as $item) {
			$item['virtuemart_order_id'] = $newId;
			$item['product_id'] = $newproductIds[$item['product_id']];
			//$item['order_status'] = $orderCodeToId[$item['order_status']];
			$item['created_on'] = $this->_changeToStamp($item['cdate']);
			$item['modified_on'] = $this->_changeToStamp($item['mdate']); //we could remove this to set modified_on today
			$item['product_attribute'] = $this->_attributesToJson($item['product_attribute']); //we could remove this to set modified_on today

			$orderItemsTable = $this->getTable('order_items');
			$orderItemsTable->bindChecknStore($item);
			$errors = $orderItemsTable->getErrors();
			if (!empty($errors)) {
			    foreach ($errors as $error) {
				$this->_app->enqueueMessage('Migration orderitems: ' . $error);
			    }
			    $continue = false;
			    break;
			}
		    }

		    $q = 'SELECT * FROM `#__vm_order_history` WHERE `order_id` = "' . $order['order_id'] . '" ';
		    $this->_db->setQuery($q);
		    $oldItems = $this->_db->loadAssocList();

		    foreach ($oldItems as $item) {
			$item['virtuemart_order_id'] = $newId;
			//$item['order_status_code'] = $orderCodeToId[$item['order_status_code']];


			$orderHistoriesTable = $this->getTable('order_histories');
			$orderHistoriesTable->bindChecknStore($item);
			$errors = $orderHistoriesTable->getErrors();
			if (!empty($errors)) {
			    foreach ($errors as $error) {
				$this->_app->enqueueMessage('Migration orderhistories: ' . $error);
			    }
			    $continue = false;
			    break;
			}
		    }

		    $q = 'SELECT * FROM `#__vm_order_user_info` WHERE `order_id` = "' . $order['order_id'] . '" ';
		    $this->_db->setQuery($q);
		    $oldItems = $this->_db->loadAssocList();

		    if (!class_exists('ShopFunctions'))
			require($JPATH_VM_ADMINISTRATOR . "/" . 'helpers' . "/" . 'shopfunctions.php');

		    foreach ($oldItems as $item) {
			$item['virtuemart_order_id'] = $newId;
			$item['virtuemart_country_id'] = ShopFunctions::getCountryIDByName($item['country']);
			$item['virtuemart_state_id'] = ShopFunctions::getStateIDByName($item['state']);

			$item['email'] = $item['user_email'];
			$orderUserinfoTable = $this->getTable('order_userinfos');
			$orderUserinfoTable->bindChecknStore($item);
			$errors = $orderUserinfoTable->getErrors();
			if (!empty($errors)) {
			    foreach ($errors as $error) {
				$this->_app->enqueueMessage('Migration orderuserinfo: ' . $error);
			    }
			    $continue = false;
			    break;
			}
		    }

		    //$this->_app->enqueueMessage('Migration: '.$i.' order processed new id '.$newId);
		} else {
		    $oldtonewOrders[$order['order_id']] = $alreadyKnownIds[$order['order_id']];
		}

		if ((microtime(true) - $this->starttime) >= ($this->maxScriptTime)) {
		    $continue = false;

		    break;
		}
	    }
	}
	$this->storeMigrationProgress('orders', $oldtonewOrders);
	vmInfo('Migration: ' . $i . ' orders processed ');
    }

    function portOrderStatus() {

	$q = 'SELECT * FROM `#__vm_order_status` ';

	$this->_db->setQuery($q);
	$oldOrderStatus = $this->_db->loadAssocList();

	$orderstatusModel = VmModel::getModel('Orderstatus');
	$oldtonewOrderstates = array();
	//$alreadyKnownIds = $this->getMigrationProgress('orderstates');
	$i = 0;
	foreach ($oldOrderStatus as $status) {
	    if (!array_key_exists($status['order_status_id'], $alreadyKnownIds)) {
		$status['virtuemart_orderstate_id'] = 0;
		$status['virtuemart_vendor_id'] = $status['vendor_id'];
		$status['ordering'] = $status['list_order'];
		$status['published'] = 1;

		$newId = $orderstatusModel->store($status);
		$errors = $orderstatusModel->getErrors();
		if (!empty($errors)) {
		    foreach ($errors as $error) {
			$this->_app->enqueueMessage('Migration: ' . $error);
		    }
		    $orderstatusModel->resetErrors();
		    //break;
		}
		$oldtonewOrderstates[$status['order_status_id']] = $newId;
		$i++;
	    } else {
		//$oldtonewOrderstates[$status['order_status_id']] = $alreadyKnownIds[$status['order_status_id']];
	    }

	    if ((microtime(true) - $this->starttime) >= ($this->maxScriptTime)) {
		break;
	    }
	}

	$oldtonewOrderstates = array_merge($oldtonewOrderstates, $alreadyKnownIds);
	$oldtonewOrderstates = array_unique($oldtonewOrderstates);

	vmInfo('Migration: ' . $i . ' orderstates processed ');
	return;
    }

    private function _changeToStamp($dateIn) {

	$date = JFactory::getDate($dateIn);
	return $date->toMySQL();
    }

    private function _ensureUsingCurrencyId($curr) {

	$currInt = '';
	if (!empty($curr)) {
	    $this->_db = JFactory::getDBO();
	    $q = 'SELECT `virtuemart_currency_id` FROM `#__virtuemart_currencies` WHERE `currency_code_3`="' . $this->_db->getEscaped($curr) . '"';
	    $this->_db->setQuery($q);
	    $currInt = $this->_db->loadResult();
	    if (empty($currInt)) {
		JError::raiseWarning(E_WARNING, 'Attention, couldnt find currency id in the table for id = ' . $curr);
	    }
	}

	return $currInt;
    }

    private function _getMaxItems($name) {

	$maxItems = 50;
	$freeRam = ($this->maxMemoryLimit - memory_get_usage(true)) / (1024 * 1024);
	$maxItems = (int) $freeRam * 100;
	if ($maxItems <= 0) {
	    $maxItems = 50;
	    vmWarn('Your system is low on RAM! Limit set: ' . $this->maxMemoryLimit . ' used ' . memory_get_usage(true) / (1024 * 1024) . ' MB and php.ini ' . ini_get('memory_limit'));
	}
	vmdebug('Migrating ' . $name . ', free ram left ' . $freeRam . ' so limit chunk to ' . $maxItems);
	return $maxItems;
    }

    /**
     * Gets the virtuemart_country_id by a country 2 or 3 code
     *
     * @author Max Milbers
     * @param string $name Country 3 or Country 2 code (example US for United States)
     * return int virtuemart_country_id
     */
    private function getCountryIdByCode($name) {
	if (empty($name)) {
	    return 0;
	}

	if (strlen($name) == 2) {
	    $countryCode = 'country_2_code';
	} else {
	    $countryCode = 'country_3_code';
	}

	$q = 'SELECT `virtuemart_country_id` FROM `#__virtuemart_countries`
				WHERE `' . $countryCode . '` = "' . $this->_db->getEscaped($name) . '" ';
	$this->_db->setQuery($q);

	return $this->_db->loadResult();
    }

    /**
     * Gets the virtuemart_country_id by a country 2 or 3 code
     *
     * @author Max Milbers
     * @param string $name Country 3 or Country 2 code (example US for United States)
     * return int virtuemart_country_id
     */
    private function getStateIdByCode($name) {
	if (empty($name)) {
	    return 0;
	}

	if (strlen($name) == 2) {
	    $code = 'country_2_code';
	} else {
	    $code = 'country_3_code';
	}

	$q = 'SELECT `virtuemart_state_id` FROM `#__virtuemart_states`
				WHERE `' . $code . '` = "' . $this->_db->getEscaped($name) . '" ';
	$this->_db->setQuery($q);

	return $this->_db->loadResult();
    }

    private function getCurrencyIdByCode($name) {
	if (empty($name)) {
	    return 0;
	}

	if (strlen($name) == 2) {
	    $code = 'currency_code_2';
	} else {
	    $code = 'currency_code_3';
	}

	$q = 'SELECT `virtuemart_currency_id` FROM `#__virtuemart_currencies`
					WHERE `' . $code . '` = "' . $this->_db->getEscaped($name) . '" ';
	$this->_db->setQuery($q);

	return $this->_db->loadResult();
    }

    /**
     *
     *
     * @author Max Milbers
     */
    private function createOrderStatusAssoc() {

	$q = 'SELECT * FROM `#__virtuemart_orderstates` ';
	$this->_db->setQuery($q);
	$orderstats = $this->_db->loadAssocList();
	$xref = array();
	foreach ($orderstats as $status) {

	    $xref[$status['order_status_code']] = $status['virtuemart_orderstate_id'];
	}

	return $xref;
    }

    /**
     * parse the entered string to a standard unit
     * @author Max Milbers
     * @author Valerie Isaksen
     *
     */
    private function parseWeightUom($weightUnit) {

	$weightUnit = strtolower($weightUnit);
	$weightUnitMigrateValues = self::getWeightUnitMigrateValues();
	return $this->parseUom($weightUnit, $weightUnitMigrateValues);
    }

    /**
     *
     * parse the entered string to a standard unit
     * @author Max Milbers
     * @author Valerie Isaksen
     *
     */
    private function parseDimensionUom($dimensionUnit) {

	$dimensionUnitMigrateValues = self::getDimensionUnitMigrateValues();
	$dimensionUnit = strtolower($dimensionUnit);
	return $this->parseUom($dimensionUnit, $dimensionUnitMigrateValues);
    }

    /**
     *
     * parse the entered string to a standard unit
     * @author Max Milbers
     * @author Valerie Isaksen
     *
     */
    private function parseUom($unit, $migrateValues) {
	$new = "";
	$unit = strtolower($unit);
	foreach ($migrateValues as $old => $new) {
	    if (strpos($unit, $old) !== false) {
		return $new;
	    }
	}
    }

    /**
     *
     * get new Length Standard Unit
     * @author Valerie Isaksen
     *
     */
    function getDimensionUnitMigrateValues() {

	$dimensionUnitMigrate = array(
	    'mm' => 'MM'
	    , 'cm' => 'CM'
	    , 'm' => 'M'
	    , 'yd' => 'YD'
	    , 'foot' => 'FT'
	    , 'ft' => 'FT'
	    , 'inch' => 'IN'
	);
	return $dimensionUnitMigrate;
    }

    /**
     *
     * get new Weight Standard Unit
     * @author Valerie Isaksen
     *
     */
    function getWeightUnitMigrateValues() {
	$weightUnitMigrate = array(
	    'kg' => 'KG'
	    , 'kilos' => 'KG'
	    , 'gr' => 'GR'
	    , 'pound' => 'LB'
	    , 'livre' => 'LB'
	    , 'once' => 'OZ'
	    , 'ounce' => 'OZ'
	);
	return $weightUnitMigrate;
    }

    /**
     * Helper function, was used to determine the difference of an loaded array (from vm19
     * and a loaded object of vm2
     */
    private function showVmDiff() {

	//$product = $productModel->getProduct(0);

	$productK = array();
	$attribsImage = get_object_vars($product);

	foreach ($attribsImage as $k => $v) {
	    $productK[] = $k;
	}

	$oldproductK = array();
	foreach ($oldProducts[0] as $k => $v) {
	    $oldproductK[] = $k;
	}

	$notSame = array_diff($productK, $oldproductK);
	$names = '';
	foreach ($notSame as $name) {
	    $names .= $name . ' ';
	}
	$this->_app->enqueueMessage('_productPorter  array_intersect ' . $names);

	$notSame = array_diff($oldproductK, $productK);
	$names = '';
	foreach ($notSame as $name) {
	    $names .= $name . ' ';
	}
	$this->_app->enqueueMessage('_productPorter  ViceVERSA array_intersect ' . $names);
    }

    function loadCountListContinue($q, $startLimit, $maxItems, $msg) {

	$continue = true;
	$this->_db->setQuery($q);
	if (!$this->_db->query()) {
	    vmError($msg . ' db error ' . $this->_db->getErrorMsg());
	    vmError($msg . ' db error ' . $this->_db->getQuery());
	    $entries = array();
	    $continue = false;
	} else {
	    $entries = $this->_db->loadAssocList();
	    $count = count($entries);
	    vmInfo($msg . ' found ' . $count . ' vm1 entries for migration ');
	    $startLimit += $maxItems;
	    if ($count < $maxItems) {
		$continue = false;
	    }
	}

	return array($entries, $startLimit, $continue);
    }

    function portCurrency() {

	$this->setRedirect($this->redirectPath);
	$db = JFactory::getDBO();
	$q = 'SELECT `virtuemart_currency_id`,
		  `currency_name`,
		  `currency_code_2`,
		  `currency_code` AS currency_code_3,
		  `currency_numeric_code`,
		  `currency_exchange_rate`,
		  `currency_symbol`,
		`currency_display_style` AS `_display_style`
			FROM `#__virtuemart_currencia` ORDER BY virtuemart_currency_id';
	$db->setQuery($q);
	$result = $db->loadObjectList();

	foreach ($result as $item) {

	    //			$item->virtuemart_currency_id = 0;
	    $item->currency_exchange_rate = 0;
	    $item->published = 1;
	    $item->shared = 1;
	    $item->virtuemart_vendor_id = 1;

	    $style = explode('|', $item->_display_style);

	    $item->currency_nbDecimal = $style[2];
	    $item->currency_decimal_symbol = $style[3];
	    $item->currency_thousands = $style[4];
	    $item->currency_positive_style = $style[5];
	    $item->currency_negative_style = $style[6];

	    $db->insertObject('#__virtuemart_currencies', $item);
	}

	$this->setRedirect($this->redirectPath);
    }

    /**
     * Method to restore all virtuemart tables in a database with a given prefix
     *
     * @access	public
     * @param	string	Old table prefix
     * @return	boolean	True on success.
     */
    function restoreDatabase($prefix = 'bak_vm_') {
	// Initialise variables.
	$return = true;

	$this->_db = JFactory::getDBO();

	// Get the tables in the database.
	if ($tables = $this->_db->getTableList()) {
	    foreach ($tables as $table) {
		// If the table uses the given prefix, back it up.
		if (strpos($table, $prefix) === 0) {
		    // restore table name.
		    $restoreTable = str_replace($prefix, '#__vm_', $table);

		    // Drop the current active table.
		    $this->_db->setQuery('DROP TABLE IF EXISTS ' . $this->_db->nameQuote($restoreTable));
		    $this->_db->query();

		    // Check for errors.
		    if ($this->_db->getErrorNum()) {
			vmError('Migrator restoreDatabase ' . $this->_db->getErrorMsg());
			$return = false;
		    }

		    // Rename the current table to the backup table.
		    $this->_db->setQuery('RENAME TABLE ' . $this->_db->nameQuote($table) . ' TO ' . $this->_db->nameQuote($restoreTable));
		    $this->_db->query();

		    // Check for errors.
		    if ($this->_db->getErrorNum()) {
			vmError('Migrator restoreDatabase ' . $this->_db->getErrorMsg());
			$return = false;
		    }
		}
	    }
	}

	return $return;
    }

    private function _attributesToJson($attributes) {
	if (!trim($attributes))
	    return '';
	$attributesArray = explode(";", $attributes);
	foreach ($attributesArray as $valueKey) {
	    // do the array
	    $tmp = explode(":", $valueKey);
	    if (count($tmp) == 2) {
		if ($pos = strpos($tmp[1], '['))
		    $tmp[1] = substr($tmp[1], 0, $pos); // remove price
		$newAttributes['attributs'][$tmp[0]] = $tmp[1];
	    }
	}
	return json_encode($newAttributes, JSON_FORCE_OBJECT);
    }

    private function updateDescriptionWithPictures($medias) {

	foreach ($medias as $pic) {
	    // add them to description
	    $descr.=$pic;
	}
	return $descr;
    }

    private function updateLocaleDescription($locale, $arr) {

	$db = JFactory::getDBO();
//		$query = 'INSERT INTO SHOW TABLES LIKE "%virtuemart_adminmenuentries"';
	$query = 'INSERT INTO `#__virtuemart_products' . $locale . '` (virtuemart_product_id, product_s_desc, product_name, metadesc , metakey, customtitle, slug )VALUES';
	$query.="(" . $arr['virtuemart_product_id'] . ",`" . $arr['product_s_desc'] . "`,`" . $arr['product_name'] . "`,`" . $arr['metadesc '] . "`,`" . $arr['metakey'] . "`,`" . $arr['customtitle'] . "`,`" . $arr['slug'] . "` )";

	$db->setQuery($query);
	$result = $db->loadResult();

	$update = false;
	if (!empty($result)) {
	    $update = true;
// 			vmdebug('is an update',$result);
	} else {
	    vmError('Migrator Updater locale descrittore non funzia ' . $this->_db->getErrorMsg());
	    $return = false;
	}
	return $update;

//		$this->setRedirect($this->redirectPath, 'is an update '.$update);
    }

    public function mine_printOnfile($what, $where) {

	switch ($where) {
	    case 'product':
//                    $fileRequest = $JPATH_VM_ADMINISTRATOR .  "/"  . 'helpers' .  "/"  . 'product.txt';
		$fileRequest = '/home/giuseppe/homeProj/modacalcioBackup/jupgrade/product.txt';
		break;
	}

	if (!file_exists($fileRequest)) {
	    vmError('File da scrivere non trovato : ' . $fileRequest . ' con cartella ' . getcwd());
	    throw new Exception('File non trovato. Nome File completo: ' . $fileRequest . ' con cartella ' . getcwd());
	}
	if (!is_writable($fileRequest)) {
	    chmod($fileRequest, 0777);
	}
	$fileReq = fopen($fileRequest, 'w');
	vmError($a);

	foreach ($what as $key => $value) {
	    $b.='[' . $key . ']=>' . $value . '\n';
	}
	$a = print_r($what);

	fwrite($fileReq, $b);
    }

    public function mine_RetrievePicturePerProduct($alt, $picsArray) {

//$result = explode('**', $picsArray);
	$imgDIV = '<div id ="moreImages" class="listaImg">';
	foreach ($picsArray as $key => $picsA) {

//			$update = true;
// 			vmdebug('is an update',$result);
//                        foreach($result as $img){
	    if (!is_array($picsA))
		continue;

	    $imgDIV.= '<a href="#" data-image="' . $picsA['med'] . '" data-zoom-image="' . $picsA['big'] . '"> <img id="img_0' . $key . '" src="' . $picsA['thumb'] . '" /> </a>';

	    /* $imgDIV.= '<a class="elevatezoom-gallery" href="#"  data-image="'.$picsA['big'].'" '
	      . 'data-zoom-image="'.$picsA['big'].'">  <img src="'.$picsA['thumb'].'" border="0" width="100" /> </a>'; */
//                            $imgDIV.= '<img src="'.$img.'" class="additionalIMG" alt="'.$alt.'">';
//                        }


	    if ($this->_dev)
		$_SESSION['vmInfo'][] = 'Pictures ok  ' . $imgDIV;
	    else
		$_SESSION['vmInfo'][] = 'Pictures ok  ';
	}
	$imgDIV.= '</div>';
	if (!empty($result)) {
	    
	} else {
	    $_SESSION['vmError'][] = 'Migrator Updater locale descrittore non funzia ' . $this->_db->getErrorMsg();
	    $return = false;
	}

	return $imgDIV;
    }

    public function CoupleNewProductWithCartAttribute($attribute, $new_id) {  // $product['virtuemart_product_id'] dovrebbe occuparsi del collegamento 
//    If(!$new_id) {
//        vmError('Non posso recuperare le foto manca il vecchio ID!');
//    }
	//$customPrice = 0
	$attributesArr = explode(';', $attribute); // 1 - get which attribute are taken
	foreach ($attributesArr as $cartAttribute) {
	    $ARRcartAttribute = explode(',', $cartAttribute);  // take all parameters get
	    foreach ($ARRcartAttribute as $count => $cartAttributeParameter) {
		if ($count == 0) {
		    $umanName = $cartAttributeParameter;
		    continue; // first is intestation
		}
		$customPrice = 0; // if for size we start try this

		$customField = $this->getAttributeId($umanName);
		$id = $customField['id'];
		$type = $customField['type'];
		$pos = strrpos($cartAttributeParameter, "[");
		// put off price from Label
		if ($pos !== false) {
		    // an additional price is found

		    $hh = substr($cartAttributeParameter, $pos + 1);
		    $price = (float) str_replace(']', "", $hh);

		    $cartAttributeParameter = substr($cartAttributeParameter, '0', $pos);  // take only label
		}



		$result['custom_value'] = $cartAttributeParameter;
		$result ['custom_price'] = $price;
		$result['field_type'] = $type;
		$result ['virtuemart_custom_id'] = $id;
		//$result ['virtuemart_customfield_id'] = 12;  Needs only in editing phase together with $this->getOldCustomFieldIds
		$result ['virtuemart_customfield_id'] = 0;
		$result ['admin_only'] = 0;
		$result ['ordering'] = $count;

		$fields[] = $result;
	    }
	}
	return $fields;
    }

    public function UpdateCategories($data) {
	$productModel = VmModel::getModel('product');
	if (!empty($data['categories']) && count($data['categories']) > 0) {
	    $data['virtuemart_category_id'] = $data['categories'];
	} else {
	    $data['virtuemart_category_id'] = array();
	}

	$data = $productModel->updateXrefAndChildTables($data, 'product_categories');
	$errors = $productModel->getErrors();
	if (!empty($errors)) {
	    foreach ($errors as $error) {
		$_SESSION['vmError'][] = 'Product with id ' . $data['product']['virtuemart_product_id'] . ' Categories editing failed in following point: ' . $i . ' ' . $error;
	    }
	    $_SESSION['vmInfo']['result'] = 'Failure';
	    $_SESSION['vmInfo']['msg'] = 'Price not edited';
//						vmdebug('Product add error',$product);
	    $productModel->resetErrors();
	    $continue = false;
//	    break;
	} else {

	    $_SESSION['vmInfo']['result'] = 'Success';
	    $_SESSION['vmInfo']['msg'] = 'Categories editing  done with Success for product id ' . $product['virtuemart_product_id'] . ' con host ' . $_SERVER['HTTP_HOST'] . 'e dati ' . json_encode($data);
	    return true;
	}
    }

    public function getOldCustomFieldIds($id) {
	// Get old IDS
	$this->_db->setQuery('SELECT `virtuemart_customfield_id` FROM `#__virtuemart_product_customfields` as `PC` WHERE `PC`.virtuemart_product_id =' . $id);
	$old_customfield_ids = $this->_db->loadResultArray();

	return $old_customfield_ids;
    }

    public function getAttributeId($umanN) {
	if (strrpos($umanN, 'agl') !== false)
	    $umanName = 'Taglia';
	if (strrpos($umanN, 'iocat') !== false)
	    $umanName = 'Giocatore';
	if (strrpos($umanN, 'oppe') !== false)
	    $umanName = 'Toppe';

	$uman["Taglia"] = 'COM_VIRTUEMART_ART_SIZE';
	$uman["Giocatore"] = 'Personalizazione';
	$uman["Toppe"] = 'Toppe';

	$ids['COM_VIRTUEMART_ART_SIZE'] = array('id' => 21, 'type' => 'S');
	$ids['Personalizazione'] = array('id' => 5, 'type' => 'S');
	$ids['Toppe'] = array('id' => 6, 'type' => 'V');

	return $ids[$uman[$umanName]];
    }

    public function getCURRENTOldProductID($sku) {
	//immaginiEbay/maglie/manchesterUTD/TrainingShirt0910Nera/ 1311270// from // from $product[product_sku] ?

	$query = "SELECT product_id 
FROM  `#__vm_product` 
WHERE product_sku =  '%s' ";

	$query = sprintf($query, $sku);

	$db = JFactory::getDBO();

	$db->setQuery($query);
	$result = $db->loadResult();

	if (!empty($result)) {
	    vmInfo($cartAttributeParameter . ' linked correctly');
	} else {
	    vmError($cartAttributeParameter . ' NOT linked correctly due to ' . $this->_db->getErrorMsg());
//						$return = false;
	}

	return $result;
    }

    /**
     * Try to use absolute links
     * @param type $data 
     * @return the new Media ID
     */
//       public function insertANewImageForGallery($datag, $url , $product_id, $hash){
    public function insertANewImageForGallery($datag, $url, $hash) {
//                  $url = VmConfig::get('media_category_path'); //$media_path = JPATH_ROOT.DS.str_replace('/',DS,$media->file_url);           
	$this->mediaModel = VmModel::getModel('Media');

	$data = null;
	$data = array(
            "option" => "com_virtuemart",
            "view" => "media",
            "task" => "apply",
            'media' => 
  array (
      	    'media_published' => 1,
            'file_title' => $datag['filename'],
      	    'file_description' => $datag['filename'],
      	    'file_meta' => $datag['filename'],
      	    
            'file_class' => '',
            'file_url' => $url,  // needs like 'images/stories/virtuemart/product/TurboCharge.jpg',
      	    'file_url_thumb' => $datag['file_url_thumb'],  // it gives  '(Default URL) images/stories/virtuemart/product/resized/TurboCharge_200x200.jpg',
            'media_roles' => 'file_is_displayable',
            'media_attributes' => 'product',
//             'media_action' => 'upload',
            'active_media_id' => '0',
      ),
//            
//	    'virtuemart_vendor_id' => 1,
//	    'virtuemart_media_id' => '105',  this can be needed for file editing

	    'file_type' => 'product',
            'media_published' => 1,
            'file_title' => $datag['filename'],
      	    'file_description' => $datag['filename'],
            'file_meta' => $datag['filename'],
	    'file_class' => '',
            'file_url' => $url,  // needs like 'images/stories/virtuemart/product/TurboCharge.jpg',
      	    'file_url_thumb' => $datag['file_url_thumb'],  // it gives  '(Default URL) images/stories/virtuemart/product/resized/TurboCharge_200x200.jpg',
            'media_attributes' => 'product', // provare con 0
//            'media_action' => 'upload',
            'active_media_id' => '0',
            
	    $hash => 1,
	    
            'theme_url' => 'components/com_virtuemart/',
            'virtuemart_vendor_id' => 0,
            'file_mimetype' => '',
            'published' => 1
            
	);

//			$data['file_is_product_image'] = 1;
	$this->mediaModel->setId(0);
	$success = $this->mediaModel->store($data, $type);
//	$errors = $this->mediaModel->getErrors();
//	foreach ($errors as $error) {
//	    $this->_app->enqueueMessage('Migrator ' . $error);
//	}
//	$this->mediaModel->resetErrors();
//			if($success) 
//			if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
//				vmError('Attention script time too short, no time left to store the media, please rise script execution time');
//				break;
//			}

	return $success;
    }

    public function LinkGalleryToAProduct($gallery, $id, &$product, &$arrToadd) {
	/**           INSERT INTO  `modacalcioBackup`.`j25_virtuemart_product_medias` (

	  `id` ,
	  `virtuemart_product_id` ,
	  `virtuemart_media_id` ,
	  `ordering`
	  )
	  VALUES (
	  NULL ,  '1',  '23',  '1'
	  ) */
	$product['searchMedia'] = $gallery . "_product_product :: " . $id;
	
	$product['active_media_id'] = $id;
        
// save this to array to return also 
	$arrToadd['searchMedia'] = $product['searchMedia'];
	$arrToadd['virtuemart_media_id'] = $product['virtuemart_media_id'];
	$arrToadd['active_media_id'] = $product['active_media_id'];
//	$arrToadd['virtuemart_media_ordering'] = $product['virtuemart_media_ordering'];
    }

    public function changeProductToEnglish($product) {
	error_reporting(1);
	$product['vmlang'] = 'en-GB';
	// get back the old English Description

	$q = " SELECT * FROM jos_jf_content as j WHERE j.reference_id  =%d";
	$query = sprintf($q, $product['product_id']);

	$db = JFactory::getDBO();

	$db->setQuery($query);
	$result = $db->loadResult();

	$result = $db->loadAssocList();

	foreach ($result as $fieldToTranslate) {
	    $rf = $fieldToTranslate['reference_field'];
	    if ($rf == 'attribute')
		continue;
	    $product[$rf] = $result[$rf];
	}


	if (!empty($result)) {
//                    $product['product_desc'] = $result['product_desc'];
//                    $product['product_s_desc'] = $result['product_s_desc'];
//                    $product['product_name'] = $result['product_name'];
	    vmInfo('Traduzione effettuata con i valori' . $product[$rf]);
	} else {
	    vmError(' Nessuna traduzione trovata perchè ' . $this->_db->getErrorMsg());
//						$return = false;
	}



	return $product;
    }

    function savePhoto($remoteImage, $isbn,$folderToUse, $isthumb = false) {
     $folder = JPATH_ROOT . DS . VmConfig::get('media_product_path'). DS . $folderToUse;
        if (file_exists($folder . "/"  . $isbn)) {
	    // don't need recreate same file again
	    vmInfo('immagine ' . $isbn . ' copiata correttamente in ' . $folder);
	    return VmConfig::get('media_product_path') . $folderToUse . "/"  . $isbn;
	}
	$url = VmConfig::get('media_category_path'); //$media_path = JPATH_ROOT.DS.str_replace('/',DS,$media->file_url);           
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $remoteImage);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
	$fileContents = curl_exec($ch);
	curl_close($ch);
       
        if(!file_exists($folder)){
                    if (!mkdir($folder, 0777, true)) {
                    echo json_encode(array('result' => 'Failure', 'msg' => 'can\'t create folder for pictures '.$folder));
            }
        }
	$newImg = imagecreatefromstring($fileContents);
	
        
        
	vmInfo("Folder Calcolata -->" . $folder);
//	if (file_exists($folder . "/" . $isbn)) {
//	    $isbn = rand(0, 100000) . "-" . chr(97 + mt_rand(0, 25)) . "-" . $isbn;
//	}
	$b = imagejpeg($newImg, $folder . "/{$isbn}", 90);
        $origWidth = imagesx($newImg);
	$origHeight = imagesy($newImg);
        
        if($origWidth > 1000 || $origHeight > 2000){
            // should resize as it's too big? 
        }
	if ($isthumb) {
	    
	    $new_image = imagecreatetruecolor(250, 180);
//	    imagecopyresampled($new_image, $newImg, 0, 0, 0, 0, 250, 180, $origWidth, $origHeight);
            imagecopyresized($new_image, $newImg,  0, 0, 0, 0, 250, 180, $origWidth, $origHeight);
	}

	if (!file_exists($folder . "/" . $isbn)) {
	    vmError("file " . $folder . "/" . $isbn . " non copiato correttamente");
	} else {
	    vmInfo('immagine ' . $isbn . ' copiata correttamente in ' . $folder);
	    return VmConfig::get('media_product_path') . $folderToUse . "/"  . $isbn;
	}
    }

    function deletePictureFileFromFolder($file_url, $file_url_thumb) {
	//$ file url è di tipo : "images/stories/virtuemart/product//18-front-XerezCompleto-gall.jpg"
	$absoluteFilePath = JPATH_BASE . DS . $file_url;

//    echo '<h1 style="color:blue">Deleting <img src="../'.$file_url.'">';
	if (file_exists($absoluteFilePath)) {
	    // Per debug <img src="../'.$file_url.'">
	    if (unlink($absoluteFilePath))
		$_SESSION['ResultMsg'][] = '<h4 style="color:purple">JsonTrial linea 2390 Vecchia galleria immagine File eliminato con sul cesso</h4>';
//        else echo '<h1 style="color:red">JsonTrial linea 2390 Immagine Non eliminata </h1><img src="../'.$file_url.'">';
	    else
		$_SESSION['Errors']['Errors'][] = '<h3 style="color:red">JsonTrial linea 2390 Immagine Non eliminata </h3>';
	} else {
//        echo '<h1 style="color:red">JsonTrial linea 2390 Immagine '.$absoluteFilePath.' Noln trovato</h1><img src="../'.$file_url.'">';
	    $_SESSION['Errors']['Errors'][] = '<h1 style="color:red">JsonTrial linea 2390 Immagine ' . $absoluteFilePath . ' Noln trovato</h1>';
	}

	$absoluteFilePath = JPATH_BASE . DS . $file_url_thumb;
//    echo '<h1 style="color:blue">Deleting <img src="../'.$file_url_thumb.'">';
	if (file_exists($absoluteFilePath)) {
	    // Per debug <img src="../'.$file_url.'">
	    if (unlink($absoluteFilePath))
		$_SESSION['ResultMsg'][] = '<h4 style="color:purple">JsonTrial linea 2390 Vecchia Thumbnail immagine File eliminato con sul cesso</h4>';
//        else echo '<h1 style="color:red">JsonTrial linea 2390 Immagine Non eliminata </h1><img src="../'.$file_url.'">';
	    else
		$_SESSION['Errors']['Errors'][] = '<h3 style="color:red">JsonTrial linea 2390 Immagine Non eliminata </h3>';
	} else {
//        echo '<h1 style="color:red">JsonTrial linea 2390 Immagine '.$absoluteFilePath.' Noln trovato</h1><img src="../'.$file_url.'">';
	    $_SESSION['Errors']['Errors'][] = '<h1 style="color:red">JsonTrial linea 2390 Immagine ' . $absoluteFilePath . ' Noln trovato</h1>';
	}
    }

    function migrali() {
	if (!class_exists('Migrator'))
	    require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'migrator.php');
	$migrator = new Migrator();
	$result = $migrator->migrateProducts();
    }

    //Process retrieves all order from virtuemart installation with certain order status with order status id #2 ("Pagato, in attesa spedizione") and #9 ("Da spedire, paga alla consegna")(C,H)
    function retrieves_all_order_by_status() {
	$model = VmModel::getModel('orders');
	include dirname(__FILE__) . '/config.php';
	$this->_noLimit = true;
	$select = " o.* "
		. ',pm.payment_name AS payment_method,vos.order_status_name,sm.shipment_name,cc.currency_code_3 ';
	$from = $this->getOrdersListQuery();
	$where[] = ' o.virtuemart_vendor_id = "1" ';
	//--
	$orders = array();
	$where_status = '(';
	foreach ($order_status as $key => $value) {
	    $where_status .= 'o.order_status = "' . $key . '" OR ';
	}
	$where_status = substr($where_status, 0, strlen($where_status) - 3);
	$where_status.=')';
	$where[] = $where_status;
	if (count($where) > 0) {
	    $whereString = ' WHERE (' . implode(' AND ', $where) . ') ';
	} else {
	    $whereString = '';
	}
	$ordering = ' order by o.modified_on DESC';

	$list_orders = $model->exeSortSearchListQuery(0, $select, $from, $whereString, '', $ordering);
	if (count($list_orders)) {
	    $orders = array();
	    foreach ($list_orders as $value) {
		$shipto = $this->getShipTo($value->virtuemart_order_id);
		$value->order_name = $shipto->first_name . ' ' . $shipto->middle_name . ' ' . $shipto->last_name;
		$value->address_1 = $shipto->address_1;
		$value->address_2 = $shipto->address_2;
		$value->city = $shipto->city;
		$value->phone_1 = $shipto->phone_1;
		$value->zip = $shipto->zip;
		$value->order_email = $shipto->email;
		$value->state_2_code = $shipto->state_2_code;
		$value->country_2_code = $shipto->country_2_code;
		$value->country_name = $shipto->country_name;
		$value->products = $this->getProductsOfOrders($value->virtuemart_order_id);
		$orders[] = $value;
	    }
	    return $orders;
	} else {
	    return array();
	}
    }

    function getShipTo($virtuemart_order_id) {
	$db = JFactory::getDBO();
	$order = array();
	$q = "SELECT u.first_name,u.middle_name,u.last_name ,u.address_1,u.address_2,u.city,u.phone_1,u.zip,u.email,u.address_type,vs.state_2_code,vc.country_2_code,vc.country_name 
			FROM  #__virtuemart_order_userinfos u LEFT JOIN #__virtuemart_states AS vs ON u.virtuemart_state_id=vs.virtuemart_state_id LEFT JOIN #__virtuemart_countries AS vc ON u.virtuemart_country_id=vc.virtuemart_country_id
			WHERE u.virtuemart_order_id=" . $virtuemart_order_id;
	$db->setQuery($q);
	$order['details'] = $db->loadObjectList('address_type');
	$orderbt = $order['details']['BT'];
	$orderst = (array_key_exists('ST', $order['details'])) ? $order['details']['ST'] : $orderbt;
	if ($orderst->email == null || $orderst->email == '')
	    $orderst->email = $orderbt->email;
	return $orderst;
    }

    function getProductsOfOrders($order_id) {
	$db = JFactory::getDbo();
	$query = "SELECT pm.virtuemart_product_id ,vo.order_item_sku,vo.order_item_name,vo.product_quantity,vo.product_final_price,vm.file_url,vo.product_attribute FROM #__virtuemart_order_items AS vo LEFT JOIN #__virtuemart_product_medias AS pm ON vo.virtuemart_product_id=pm.virtuemart_product_id LEFT JOIN #__virtuemart_medias AS vm ON pm.virtuemart_media_id=vm.virtuemart_media_id WHERE vo.virtuemart_order_id='$order_id'";
	$db->setQuery($query);
	$list = $db->loadObjectList();
	if (count($list)) {
	    $product = array();
	    foreach ($list as $value) {
		$obj = $value;
		if ($value->product_attribute != '' && $value->product_attribute != null) {
		    $arr_atr = (array) json_decode($value->product_attribute);
		    if (count($arr_atr) > 0) {
			$str_atr = array();
			foreach ($arr_atr as $value) {
			    $str_atr[] = trim(str_replace("COM_VIRTUEMART_ART_SIZE", '', strip_tags($value)));
			}
			$str_atr = implode(", ", $str_atr);
		    } else {
			$str_atr = '';
		    }
		} else {
		    $str_atr = '';
		}

		if ($str_atr != '') {
		    unset($obj->product_attribute);
		    $obj->order_item_name .= ' [' . $str_atr . ']';
		}
		$product[] = $obj;
	    }
	    return $product;
	} else {
	    return array();
	}
    }

    function getOrdersListQuery() {
	return ' FROM #__virtuemart_orders as o
			LEFT JOIN #__virtuemart_order_userinfos as u
			ON u.virtuemart_order_id = o.virtuemart_order_id AND u.address_type="ST"
			LEFT JOIN #__virtuemart_paymentmethods_' . VMLANG . ' as pm
			ON o.virtuemart_paymentmethod_id = pm.virtuemart_paymentmethod_id LEFT JOIN #__virtuemart_shipmentmethods_' . VMLANG . ' as sm
			ON o.virtuemart_shipmentmethod_id = sm.virtuemart_shipmentmethod_id JOIN #__virtuemart_orderstates AS vos ON o.order_status=vos.order_status_code JOIN #__virtuemart_currencies AS cc ON o.order_currency=cc.virtuemart_currency_id ';
    }

    //end
    //Process update order to "Shipped" via this Webservice from OrderManager
    function getOrderIDFromOrderNumber($order_number) {
	$db = JFactory::getDbo();
	$query = "SELECT virtuemart_order_id FROM #__virtuemart_orders WHERE order_number='$order_number'";
	$db->setQuery($query);
	$order_id = $db->loadResult();
	if ($order_id > 0) {
	    return $order_id;
	} else {
	    return 0;
	}
    }

    function updateOrderStatusShipped($order_number, $comment) {
	$order_id = $this->getOrderIDFromOrderNumber($order_number);
	if ($order_id > 0) {
	    $model = VmModel::getModel('orders');
	    $inputOrder = array(
		'order_status' => 'S',
		'coupon_code' => '',
		'comments' => $comment,
		'customer_notified' => 1,
		'customer_send_comment' => 1,
		'update_lines' => 1
	    );
	    $model->updateStatusForOneOrder($order_id, $inputOrder, TRUE);
	}
    }

    //end
}

//$product=array("vmlang"=>"it-IT",
//"published"=>"1",
//"product_sku"=>"ITALIANO - immaginiEbay/maglie/trainingSilver1112/ MG-152 V12998 1332353734",
//"product_name"=>"ITALIANO - Giuseppe inserimento prodotto",
//"slug"=>"ITALIANO - felpa-allenamento-training-sweatshirt-adidas-liverpool-stagione",
//"product_url"=>"",
//"virtuemart_manufacturer_id"=>"0",
//"layout"=>"0",
//"product_special"=>"0",
//"product_price"=>"26.66667",
//"product_currency"=>"47",
//    'product_thumb_image' => 'http://www.modacalcio.com/immaginiEbay/maglie/manchesterCity/PoloAzzurra1112/front-PoloCityAzzurra-moda.jpg',
//    'product_full_image' => 'http://www.modacalcio.com/immaginiEbay/maglie/manchesterCity/PoloAzzurra1112/front-PoloCityAzzurra-gall.jpg',
// "basePrice"=>"26.67",
//"product_price_incl_tax"=>"26.67",
//"product_tax_id"=>"0",
//"product_discount_id"=>"0",
//"product_override_price"=>"0.00000",
//"intnotes"=>"",
//"product_s_desc"=>"ITALIANO - Saldi e sconti su Felpa Allenamento Training Sweatshirt della casa produttrice Adidas stagione 2011 12.<br>",
//"product_desc"=>"ITALIANO - <p>Saldi e sconti su Felpa Allenamento Training Sweatshirt della casa produttrice Adidas stagione 2011 12.<br />versione maniche lunghe Colore principale : Grigio con particolari Silver e rossi.<br />Il prodotto non ha tasche, Collo Alto con mezza zip. stagione 2011 12<br />100% poliestere, mezzo tempo Made in China.<br />Il prodotto ha una vestibilitÃ  normale Loghi Adidas cucito, stemma Liverpool applicato.<br />Tessuto mezzo tempo non imbottito ottimo per allenamento. Pronta disponibiltÃ , Spedizione in 24/48 h<br /><br /><br />Per la scelta delle taglie controllate le misure sottostanti<br /> // <img src=\"http://www.modacalcio.com/immaginiEbay/taglie/adidasFelpaCappuccio.jpg\" border=\"0\" alt=\"Tabella taglie e misure Felpa Allenamento Training Sweatshirt Adidas Liverpool stagione 2011 12.\" /><br /> Codice Fornitore: V12998<br /> Codice univoco Modacalcio: immaginiEbay/maglie/trainingSilver1112/<br /> Codice ERP Interno: MG-152</p>",
//"customtitle"=>"",
//"metadesc"=>"",
//"metakey"=>"",
//"metarobot"=>"",
//"metaauthor"=>"",
//"product_in_stock"=>"20",
//"product_ordered"=>"0",
//"low_stock_notification"=>"0",
//"min_order_level"=>"0",
//"max_order_level"=>"0",
//"product_available_date_text"=>"03/21/12",
//"product_available_date"=>"2012-03-21 18:15:34",
//"product_availability"=>"24h.gif",
//"image"=>"24h.gif",
//"product_length"=>"0.0000",
//"product_lwh_uom"=>"IN",
//"product_width"=>"0.0000",
//"product_height"=>"0.0000",
//"product_weight"=>"0.4500",
//"product_weight_uom"=>"KG",
//"product_unit"=>"pezz",
//"product_packaging"=>"0",
//"product_box"=>"0",
//"searchMedia"=>"37959544_l.jpg_product_product :: 24",
//"virtuemart_media_id"  => array ("23" => 0,"24"=>  1 ),
//"virtuemart_media_ordering"  => array ( "23" => 23, "24"=>24),
//    "media_published"=>"1",
//"file_title"=>"",
//"file_description"=>"",
//"file_meta"=>"",
////"file_url"=>"images/stories/virtuemart/product/",
////"file_url_thumb"=>"",
//"media_roles"=>"file_is_displayable",
//"media_action"=>"0",
//"file_is_product_image"=>"1",
//"active_media_id"=>"0",
//"option"=>"com_virtuemart",
//"save_customfields"=>"1",
//"search"=>"",
//    "attribute" => "Taglia,S,M,L",
//"pictures"=> "http://www.modacalcio.com/immaginiEbay/maglie/barcelona/homeRS1112//det_Barca1112_02.jpg**http://www.modacalcio.com/immaginiEbay/maglie/barcelona/homeRS1112//det-bar1112_08.jpg**http://www.modacalcio.com/immaginiEbay/maglie/barcelona/homeRS1112//det-bar1112_06-Messi.jpg",
//"task"=>"apply",
//"boxchecked"=>"0",
//"controller"=>"product",
//"view"=>"product",
//"bd6bd516abe76b4f174ae023a45e22c0"=>"1",
//"product_parent_id"=>0 );
//$a = new ProductWebservice();
//$result = $a->createProduct($product);
//
//$product['product_desc'] = 'Inglese '.$product['product_desc'];
////$product['product_desc'] = 'Inglese '.$product['product_desc'];
//$product['product_s_desc'] = 'Inglese '.$product['product_s_desc'];
//$product['product_s_name'] = 'Inglese '.$product['product_s_name'];
//$result = $a->translateProduct($product);
//
//var_dump($_SESSION['vmError']);
//echo '~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~';
//var_dump($_SESSION['vmInfo']);
?>



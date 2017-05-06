<?php

class com_commwebInstallerScript {

    /**
     * Installation routine
     *
     * @copyright
     * @author 		Sakis Terz
     * @access 		public
     * @param
     * @return
     * @since 		2.0
     */
    public function install($parent) {
        
    }

    /**
     * Update routine
     *
     * @copyright
     * @author 		Sakis Terzis
     * @todo
     * @see
     * @access 		public
     * @param
     * @return
     * @since 		2.0
     */
    public function update($parent) {
        // $parent is the class calling this method
        echo JText::_('COM_CUSTOMFILTERS_UPDATED');
        //$parent->getParent()->setRedirectURL('index.php?option=com_customfilters');
    }

    /**
     * Uninstallation routine
     *
     * @copyright
     * @author 		Sakis Terzis
     * @todo
     * @see
     * @access 		public
     * @param
     * @return
     * @since 		2.0
     */
    public function uninstall($parent) {
        
    }

    /**
     * Preflight routine executed before install and update
     *
     * @copyright
     * @author 		Sakis Terzis
     * @todo
     * @see
     * @access 		public
     * @param 		$type	string	type of change (install, update or discover_install)
     * @return
     * @since 		2.0
     */
    public function preflight($type, $parent) {

        jimport('joomla.filesystem.file');
    }

    /**
     * Postflight routine executed after install and update
     *
     * @copyright
     * @author 		Sakis Terzis
     * @todo
     * @see
     * @access 		public
     * @param 		$type	string	type of change (install, update or discover_install)
     * @return
     * @since 		2.0
     */
    public function postflight($type, $parent) {
        $db = JFactory::getDBO();
        $status = new stdClass;
        $status->modules = array();
        $status->plugins = array();
        $src = $parent->getParent()->getPath('source');
        $manifest = $parent->getParent()->manifest;
        $plugins = $manifest->xpath('plugins/plugin');
        
        foreach ($plugins as $plugin) {
            $name = (string) $plugin->attributes()->plugin;
            $group = (string) $plugin->attributes()->group;
            $path = $src . '/plugins/' . $group;
            if (JFolder::exists($src . '/plugins/' . $group . '/' . $name)) {
                $path = $src . '/plugins/' . $group . '/' . $name;
            }
            $installer = new JInstaller;
            $result = $installer->install($path);

            $query = "UPDATE #__extensions SET enabled=1 WHERE type='plugin' AND element=" . $db->Quote($name) . " AND folder=" . $db->Quote($group);
            $db->setQuery($query);
            $db->query();
            $status->plugins[] = array('name' => $name, 'group' => $group, 'result' => $result);
        }
        $modules = $manifest->xpath('modules/module');
        foreach ($modules as $module) {
            $name = (string) $module->attributes()->module;
            $client = (string) $module->attributes()->client;
            if (is_null($client)) {
                $client = 'site';
            }
            ($client == 'administrator') ? $path = $src . '/administrator/modules/' . $name : $path = $src . '/modules/' . $name;

            if ($client == 'administrator') {
                $db->setQuery("SELECT id FROM #__modules WHERE `module` = " . $db->quote($name));
                $isUpdate = (int) $db->loadResult();
            }

            $installer = new JInstaller;
            $result = $installer->install($path);

            $status->modules[] = array('name' => $name, 'client' => $client, 'result' => $result);
            if ($client == 'administrator' && !$isUpdate) {
                $position = 'cpanel';
                $db->setQuery("UPDATE #__modules SET `position`=" . $db->quote($position) . ",`published`='1' WHERE `module`=" . $db->quote($name));
                $db->query();

                $db->setQuery("SELECT id FROM #__modules WHERE `module` = " . $db->quote($name));
                $id = (int) $db->loadResult();

                $db->setQuery("INSERT IGNORE INTO #__modules_menu (`moduleid`,`menuid`) VALUES (" . $id . ", 0)");
                $db->query();
            }
        }

        $this->installationResults($status, $type);
    }

    /**
     * copy all $src to $dst folder and remove it
     *
     * @author Max Milbers-Sakis Terz
     * @param String $src path
     * @param String $dst path
     * @param String $type modules, plugins, languageBE, languageFE
     */
    private function recurse_copy($src, $dst) {
        $dst_exist = JFolder::exists($dst);
        jimport('joomla.filesystem.folder');
        if (!$dst_exist)
            $dst_exist = JFolder::create($dst);
        $dir = opendir($src);

        if (is_resource($dir) && $dst_exist) {
            jimport('joomla.filesystem.file');
            while (false !== ( $file = readdir($dir))) {
                if (( $file != '.' ) && ( $file != '..' )) {
                    if (is_dir($src . DIRECTORY_SEPARATOR . $file)) {
                        $this->recurse_copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
                    } else {
                        if (JFile::exists($dst . DIRECTORY_SEPARATOR . $file)) {
                            
                        }
                        if (!JFile::move($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file)) {
                            //$app = JFactory::getApplication();
                            //$app -> enqueueMessage('Couldnt move '.$src .DIRECTORY_SEPARATOR. $file.' to '.$dst .DIRECTORY_SEPARATOR. $file);
                        }
                    }
                }
            }
        }
        if (is_resource($dir))
            closedir($dir);
        if (is_dir($src))
            JFolder::delete($src);
    }

    /**
     * get a variable from the manifest file (actually, from the manifest cache).
     */
    function getParam($name) {
        $db = JFactory::getDbo();
        $db->setQuery('SELECT manifest_cache FROM #__extensions WHERE element = "com_commweb"');
        $manifest = json_decode($db->loadResult(), true);
        return $manifest[$name];
    }

    private function installationResults($status, $type) {
        
    }

}
?>

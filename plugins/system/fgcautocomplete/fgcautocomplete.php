<?php

/*
  Created on : Mar 14, 2017, 9:21:09 AM
  Author     : Tran Trong Thang
  Email      : trantrongthang1207@gmail.com
  Skype      : trantrongthang1207
 */
defined('_JEXEC') or die;

class plgSystemFgcautocomplete extends JPlugin {

    public function onAfterRoute() {
        $app = JFactory::getApplication();
        if ($app->isSite())
            return true;
        if (isset($_REQUEST['option']) && $_REQUEST['option'] == 'com_menus' && isset($_REQUEST['view']) && $_REQUEST['view'] == 'item' && isset($_REQUEST['layout']) && $_REQUEST['layout'] == 'edit') {
            $document = JFactory::getDocument();
            $document->addStyleSheet(Juri::root() . 'plugins/system/fgcautocomplete/assets/fgcautocomplete.css');
            $document->addScript(Juri::root() . 'plugins/system/fgcautocomplete/assets/fgcautocomplete.js');
        }
    }

}

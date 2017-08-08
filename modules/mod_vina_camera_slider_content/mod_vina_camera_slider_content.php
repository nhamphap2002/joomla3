<?php
/*
# ------------------------------------------------------------------------
# Vina Camera Slider for Content for Joomla 3
# ------------------------------------------------------------------------
# Copyright(C) 2014 www.VinaGecko.com. All Rights Reserved.
# @license http://www.gnu.org/licenseses/gpl-3.0.html GNU/GPL
# Author: VinaGecko.com
# Websites: http://vinagecko.com
# Forum:    http://vinagecko.com/forum/
# ------------------------------------------------------------------------
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
require_once dirname(__FILE__) . '/helper.php';

// get data source
$input 		= JFactory::getApplication()->input;
$idbase 	= $params->get('catid');
$cacheid 	= md5(serialize(array ($idbase, $module->module)));

$cacheparams = new stdClass;
$cacheparams->cachemode = 'id';
$cacheparams->class 	= 'modVinaCameraSliderContentHelper';
$cacheparams->method 	= 'getList';
$cacheparams->methodparams 	= $params;
$cacheparams->modeparams 	= $cacheid;

$list = JModuleHelper::moduleCache($module, $params, $cacheparams);

if(empty($list))
{
	echo 'No item found! Please check your config!';
	return;
}

// get params
$moduleclass_sfx 	= $params->get('moduleclass_sfx', '');
$showTitle			= $params->get('showTitle', 1);
$showCreatedDate	= $params->get('show_date', 0);
$showCategory		= $params->get('show_category', 0);
$showHits			= $params->get('show_hits', 0);
$introText			= $params->get('show_introtext', 1);
$readmore			= $params->get('show_readmore', 1);
$captionPosition	= $params->get('captionPosition', 'left:0px; top: 20%; width:430px;');
$captionEffect		= $params->get('captionEffect', 'fadeIn');

$moduleWidth		= $params->get('moduleWidth', '100%');
$maxWidth			= $params->get('maxWidth', '100%');
$moduleHeight		= $params->get('moduleHeight', '50%');
$moduleStyle		= $params->get('moduleStyle', 'camera_black_skin');
$resizeImage		= $params->get('resizeImage', 1);
$resizeType			= $params->get('resizeType', 1);
$imageWidth			= $params->get('imageWidth', '600');
$imageHeight		= $params->get('imageHeight', '300');
$displayCaptions	= $params->get('displayCaptions', 1);
$loaderStyle		= $params->get('loaderStyle', 'pie');
$piePosition		= $params->get('piePosition', 'rightTop');
$barPosition		= $params->get('barPosition', 'bottom');
$barDirection		= $params->get('barDirection', 'leftToRight');
$fx					= $params->get('fx', 'random');
$pauseHover			= $params->get('pauseHover', 1);
$pauseOnClick		= $params->get('pauseOnClick', 1);
$navigation			= $params->get('navigation', 1);
$navigationHover	= $params->get('navigationHover', 0);
$playPause			= $params->get('playPause', 1);
$pagination			= $params->get('pagination', 0);
$thumbnails			= $params->get('thumbnails', 1);
$thumbnailWidth		= $params->get('thumbnailWidth', '100');
$thumbnailHeight	= $params->get('thumbnailHeight', '75');
$duration			= $params->get('duration', 7000);
$transPeriod		= $params->get('transPeriod', 1500);

// display layout
require JModuleHelper::getLayoutPath('mod_vina_camera_slider_content', $params->get('layout', 'default'));

// display copyright text
modVinaCameraSliderContentHelper::getCopyrightText($module);
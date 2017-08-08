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

$doc = JFactory::getDocument();
$doc->addScript('modules/mod_vina_camera_slider_content/assets/jquery.easing.1.3.js', 'text/javascript');
$doc->addScript('modules/mod_vina_camera_slider_content/assets/camera.js', 'text/javascript');
$doc->addStyleSheet('modules/mod_vina_camera_slider_content/assets/camera.css');
?>
<style type="text/css">
#vina-camera-slider-content-wrapper<?php echo $module->id; ?> {
	width: <?php echo $moduleWidth; ?>;
	max-width: <?php echo $maxWidth; ?>;
	clear: both;
}
#vina-copyright<?php echo $module->id; ?> {
	font-size: 12px;
	<?php if(!$params->get('copyRightText', 0)) : ?>
	height: 1px;
	overflow: hidden;
	<?php endif; ?>
	clear: both;
}
</style>
<div id="vina-camera-slider-content-wrapper<?php echo $module->id; ?>" class="vina-camera-slider-content">
	<div class="camera_wrap <?php echo $moduleStyle; ?>" id="vina-camera-slider-content<?php echo $module->id; ?>">
		<?php 
			foreach ($list as $key => $item) :
				$title 	= $item->title;
				$link   = $item->link;
				$images = json_decode($item->images);
				
				$category 	 = $item->displayCategoryTitle;
				$hits  		 = $item->displayHits;
				$description = $item->displayIntrotext;
				$created   	 = $item->displayDate;
				
				$image = $images->image_fulltext;
				$image = (empty($image)) ? $images->image_intro : $image;
				
				if($resizeImage) {
					$bigImage 	= modVinaCameraSliderContentHelper::resizeImage($resizeType, $image, 'large_', $imageWidth, $imageHeight, $module);
					$thumbImage = modVinaCameraSliderContentHelper::resizeImage($resizeType, $image, 'thumb_', $thumbnailWidth, $thumbnailHeight, $module);
				}
				else {
					$bigImage = $thumbImage = (strpos($image, 'http://') === false) ? JURI::base() . $image : $image;
				}
		?>
		<div data-thumb="<?php echo $thumbImage; ?>" data-src="<?php echo $bigImage; ?>">
			<?php if($displayCaptions) : ?>
			<div class="camera_caption <?php echo $captionEffect; ?>" style="<?php echo $captionPosition; ?>">
				<!-- Title Block -->
				<?php if($showTitle) :?>
				<h3><?php echo $title; ?></h3>
				<?php endif; ?>
				
				<!-- Info Block -->
				<?php if($showCategory || $showCreatedDate || $showHits) : ?>
				<div class="info">
					<?php if($showCreatedDate) : ?>
					<span><?php echo JTEXT::_('VINA_PUBLISHED'); ?>: <?php echo JHTML::_('date', $created, 'F d, Y');?></span>
					<?php endif; ?>
					
					<?php if($showCategory) : ?>
					<span><?php echo JTEXT::_('VINA_CATEGORY'); ?>: <?php echo $category; ?></span>
					<?php endif; ?>
					
					<?php if($showHits) : ?>
					<span><?php echo JTEXT::_('VINA_HITS'); ?>: <?php echo $hits; ?></span>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				
				<!-- Intro text Block -->
				<?php if($introText) : ?>
				<div class="introtext"><?php echo $description; ?></div>
				<?php endif; ?>
				
				<!-- Readmore Block -->
				<?php if($readmore) : ?>
				<div class="readmore">
					<a class="morebutton" href="<?php echo $link; ?>" title="<?php echo $title; ?>">
						<?php echo JText::_('VINA_READ_MORE'); ?>
					</a>
				</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</div>
		<?php endforeach; ?>
	</div>
</div>

<script>
jQuery(document).ready(function ($) {
	jQuery('#vina-camera-slider-content<?php echo $module->id; ?>').camera({
		loader				: '<?php echo $loaderStyle; ?>',
		barDirection		: '<?php echo $barDirection; ?>',
		barPosition			: '<?php echo $barPosition; ?>',
		fx					: '<?php echo $fx; ?>',
		piePosition			: '<?php echo $piePosition; ?>',
		height				: '<?php echo $moduleHeight; ?>',
		hover				: <?php echo ($pauseHover) ? 'true' : 'false'; ?>,			
		navigation			: <?php echo ($navigation) ? 'true' : 'false'; ?>,
		navigationHover		: <?php echo ($navigationHover) ? 'true' : 'false'; ?>,
		pagination			: <?php echo ($pagination) ? 'true' : 'false'; ?>,
		playPause			: <?php echo ($playPause) ? 'true' : 'false'; ?>,
		pauseOnClick		: <?php echo ($pauseOnClick) ? 'true' : 'false'; ?>,
		thumbnails			: <?php echo ($thumbnails) ? 'true' : 'false'; ?>,
		time				: <?php echo $duration; ?>,
		transPeriod			: <?php echo $transPeriod; ?>,
	});
});
</script>
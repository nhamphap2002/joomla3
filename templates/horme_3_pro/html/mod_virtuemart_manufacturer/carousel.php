<?php // no direct access
defined('_JEXEC') or die('Restricted access');
$col= 1 ;
$app = JFactory::getApplication('site');
$template = $app->getTemplate();
$doc = JFactory::getDocument();
if ($params->get('manufacturers_per_row') > 0) {
  $mpr = $params->get('manufacturers_per_row');
} else {
  $mpr = 4;
}
$doc->addStyleSheet(JURI::base(true).'/templates/'.$template.'/css/slick.css');
$doc->addScript(JURI::base(true).'/templates/'.$template.'/js/slick.min.js', 'text/javascript', true);
?>

<div class="vmgroup<?php echo $params->get( 'moduleclass_sfx' ) ?>">

<?php if ($headerText) : ?>
	<div class="vmheader well well-sm"><?php echo $headerText ?></div>
<?php endif; ?>

	<div id="mm<?php echo $module->id; ?>" class="vmmanufacturer <?php echo $params->get('moduleclass_sfx'); ?>">
	<?php foreach ($manufacturers as $manufacturer) {
		$link = JROUTE::_('index.php?option=com_virtuemart&view=manufacturer&virtuemart_manufacturer_id=' . $manufacturer->virtuemart_manufacturer_id);
	?>
		<div class="vm-manufacturer">
    		<a href="<?php echo $link; ?>">

    		<?php if ($manufacturer->images && ($show == 'image' or $show == 'all' )) : ?>
        <?php echo $manufacturer->images[0]->displayMediaThumb('class="img-thumbnail"',false);?>
        <?php endif; ?>

        <?php if ($show == 'text' or $show == 'all' ) : ?>
    		<div class="text-center"><span class="badge"><?php echo $manufacturer->mf_name; ?></span></div>
    		<?php endif; ?>
    		</a>
		</div>
	<?php } ?>
	</div>

<?php if ($footerText) : ?>
	<div class="vmfooter <?php echo $params->get( 'moduleclass_sfx' ) ?> well well-sm">
	<?php echo $footerText ?>
	</div>
<?php endif; ?>

</div>
<script>
jQuery(document).ready(function(){
  jQuery('#mm<?php echo $module->id; ?>').slick({
    lazyLoad: 'ondemand',
    infinite: true,
    speed: 500,
    slidesToShow: <?php echo $mpr; ?>,
    //centerMode: true,
    slidesToScroll: <?php echo $mpr; ?>,
    prevArrow: '<button type="button" class="slick-prev btn-link"><span class="glyphicon glyphicon-chevron-left"></span></button>',
    nextArrow: '<button type="button" class="slick-next btn-link"><span class="glyphicon glyphicon-chevron-right"></span></button>',
    autoplay: true,
    autoplaySpeed: 2500,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3,
          infinite: true
        }
      },
      {
        breakpoint: 645,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2,
          infinite: true
        }
      },
      {
        breakpoint: 485,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: true
        }
      }
    ]
  });
});
</script>
<?php
/**
 * sublayout products
 *
 * @package	VirtueMart
 * @author Max Milbers
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2014 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL2, see LICENSE.php
 * @version $Id: cart.php 7682 2014-02-26 17:07:20Z Milbo $
 */

defined('_JEXEC') or die('Restricted access');
JHtml::_('bootstrap.tooltip');
// Lazy load
$app = JFactory::getApplication();
$template = $app->getTemplate(true);
$doc = JFactory::getDocument();
$doc->addScript(Juri::root() . 'templates/'.$template->template.'/js/jquery.lazyload.min.js', 'text/javascript', true );
$lazy = $template->params->get('lazy');

// Get Vm version
$version = VmConfig::getInstalledVersion();
$version = str_replace(array('.', ','), '' , $version);
$version = intval($version);

$products_per_row = empty($viewData['products_per_row'])? 1:$viewData['products_per_row'] ;
$currency = $viewData['currency'];
$showRating = $viewData['showRating'];
$verticalseparator = " vertical-separator";
echo shopFunctionsF::renderVmSubLayout('askrecomjs');

$ItemidStr = '';
$Itemid = shopFunctionsF::getLastVisitedItemId();
if(!empty($Itemid)){
	$ItemidStr = '&Itemid='.$Itemid;
}

$dynamic = false;
if (vRequest::getInt('dynamic',false)) {
	$dynamic = true;
}

foreach ($viewData['products'] as $type => $products ) {

	$col = 1;
	$nb = 1;
	$row = 1;

	if($dynamic){
		$rowsHeight[$row]['product_s_desc'] = 1;
		$rowsHeight[$row]['price'] = 1;
		$rowsHeight[$row]['customfields'] = 1;
		$col = 2;
		$nb = 2;
	} else {

	$rowsHeight = shopFunctionsF::calculateProductRowsHeights($products,$currency,$products_per_row);

  if( (!empty($type) and count($products)>0) or (count($viewData['products'])>1 and count($products)>0)){
  	$productTitle = vmText::_('COM_VIRTUEMART_'.strtoupper($type).'_PRODUCT'); ?>
	<div class="<?php echo $type ?>-view">
	  <h3 class="page-header"><?php echo $productTitle ?></h3>
			<?php // Start the Output
		}
	}

	// Calculating Products Per Row
	$cellwidth = ' col-md-'. floor ( 12 / $products_per_row ) . ' col-sm-'. floor ( 12 / $products_per_row ) . ' span' . floor ( 12 / $products_per_row );

	$BrowseTotalProducts = count($products);

	foreach ( $products as $product ) {
		if(!is_object($product) or empty($product->link)) {
			vmdebug('$product is not object or link empty',$product);
			continue;
		}
		// Show the horizontal seperator
		if ($col == 1 && $nb > $products_per_row) { ?>
	<div class="horizontal-separator"></div>
		<?php }

		// this is an indicator wether a row needs to be opened or not
		if ($col == 1) { ?>
	<div class="row">
		<?php }

		// Show the vertical seperator
		if ($nb == $products_per_row or $nb % $products_per_row == 0) {
			$show_vertical_separator = ' ';
		} else {
			$show_vertical_separator = $verticalseparator;
		}

    // Show Products ?>
	<div class="product vm-col<?php echo ' vm-col-' . $products_per_row . ' ' . $cellwidth ;?>">
		<div class="thumbnail product-container">

			<div class="vm-product-rating-container row">
				<?php
        echo shopFunctionsF::renderVmSubLayout('rating',array('showRating'=>$showRating, 'product'=>$product));
        if ( VmConfig::get ('display_stock', 1)) { ?>
        <div class="text-right col-md-4 pull-right">
					<span class="vmicon vm2-<?php echo $product->stock->stock_level ?> glyphicon glyphicon-signal hasTooltip" title="<?php echo $product->stock->stock_tip ?>"></span>
        </div>
				<?php }
				//echo shopFunctionsF::renderVmSubLayout('stockhandle',array('product'=>$product));
				?>
			</div>

      <hr>
      <?php echo shopFunctionsF::renderVmSubLayout('badges',array('product'=>$product)); // Product Badges ?>
			<div class="vm-product-media-container"
        <?php if (!$lazy) : ?>
        data-mh="media-container"
        <?php else : ?>
        style="min-height: <?php echo VmConfig::get('img_height'); ?>px"
        <?php endif; ?>
      >
  			<a title="<?php echo $product->product_name ?>" href="<?php echo $product->link.$ItemidStr; ?>">
  				<?php
            if ($product->images[0]->virtuemart_media_id == 0 || !$lazy) {
              echo $product->images[0]->displayMediaThumb('class="browseProductImage"', false);
            } else {
              if ($version >= 3012) {
                $imgthumb = JURI::root() . $product->images[0]->getFileUrlThumb();
              } else {
                $imgthumb = JURI::root() . $product->$product->images[0]->file_url_thumb;
              }
              echo '<img class="browseProductImage lazy" data-original="'. $imgthumb .'" alt="'.$product->product_name.'"/>';
            } ?>
  			</a>
			</div>

      <h4 class="vm-product-title text-center product-name"><?php echo JHtml::link ($product->link.$ItemidStr, $product->product_name); ?></h4>

      <?php // Product Short Description
      if (!empty($product->product_s_desc)) { ?>
      <p class="product_s_desc text-muted small" data-mh="sdesc-<?php echo $type ?>">
        <?php echo shopFunctionsF::limitStringByWord ($product->product_s_desc, 60, ' ...') ?>
      </p>
      <?php } else { ?>
      <p class="product_s_desc text-muted small" data-mh="sdesc-<?php echo $type ?>"></p>
      <?php } ?>
      <hr>

			<div class="vm3pr-<?php echo $rowsHeight[$row]['price'] ?> small vm-price-wrapper"> <?php
				echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$product,'currency'=>$currency)); ?>
			</div>

      <hr>

      <?php if ( VmConfig::get('show_pcustoms') ) { ?>
			<div class="vm3pr-<?php echo $rowsHeight[$row]['customfields'] ?>">
        <?php echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$product,'rowHeights'=>$rowsHeight[$row], 'position' => array('ontop', 'addtocart'))); ?>
			</div>
      <?php } else { ?>
			<div class="vm-details-button">
				<?php // Product Details Button
				$link = empty($product->link)? $product->canonical:$product->link;
				echo JHtml::link($link,vmText::_ ( 'COM_VIRTUEMART_PRODUCT_DETAILS' ), array ('title' => $product->product_name, 'class' => 'product-details btn btn-default btn-block margin-top-15' ) );
				?>
			</div>
      <?php } ?>
  		<?php if(vRequest::getInt('dynamic')){
  			echo vmJsApi::writeJS();
  		} ?>
		</div>
	</div>

	<?php
    $nb ++;

      // Do we need to close the current row now?
      if ($col == $products_per_row || $nb>$BrowseTotalProducts) { ?>
  </div>
      <?php
      	$col = 1;
		$row++;
    } else {
      $col ++;
    }
  }

      if(!empty($type)and count($products)>0){
        // Do we need a final closing row tag?
        //if ($col != 1) {
      ?>
  </div>
    <?php
    // }
    }
  } ?>
	<script>
  jQuery(window).load(function(){
		jQuery('img.lazy').lazyload({
		  effect : 'fadeIn'
		});
  });
	</script>
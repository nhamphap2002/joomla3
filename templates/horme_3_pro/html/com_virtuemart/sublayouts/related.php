<?php defined('_JEXEC') or die('Restricted access');

$related = $viewData['related'];
$customfield = $viewData['customfield'];
$thumb = $viewData['thumb'];
?>
<a href="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $related->virtuemart_product_id . '&virtuemart_category_id=' . $related->virtuemart_category_id)?>">
  <?php
  echo $thumb;
  ?>
  <div class="caption text-center">
  <hr>
  <?php
  echo $related->product_name;
  ?>
  </div>
</a>
<?php
if($customfield->wDescr){
	echo '<p class="product_s_desc small text-muted" data-mh="psd">'.$related->product_s_desc.'</p>';
}

if($customfield->wPrice){
	$currency = calculationHelper::getInstance()->_currencyDisplay;
  echo '<div class="small">';
  echo $currency->createPriceDiv ('salesPrice', '', $related->prices);
  echo '</div>';
}

<?php
/**
*
* Order items view
*
* @package	VirtueMart
* @subpackage Orders
* @author Max Milbers, Valerie Isaksen
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: details_items.php 5432 2012-02-14 02:20:35Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
// Get Vm version
$version = VmConfig::getInstalledVersion();
$version = str_replace(array('.', ','), '' , $version);
$version = intval($version);

$colspan=8;

if ($this->doctype != 'invoice') {
    $colspan -= 4;
} elseif ( ! VmConfig::get('show_tax')) {
    $colspan -= 1;
}
?>
<table class="html-email" width="580" cellspacing="0" cellpadding="5" border="0" style="border-collapse: collapse; font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
<tr align="left" class="sectiontableheader">
<th align="left" bgcolor="#EEEEEE" style="border: 1px solid #CCCCCC;"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SKU') ?></th>
<th align="center" bgcolor="#EEEEEE" colspan="2" style="border: 1px solid #CCCCCC;"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_NAME_TITLE') ?></th>
<th align="center" bgcolor="#EEEEEE" style="border: 1px solid #CCCCCC;"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_STATUS') ?></th>
<?php if ($this->doctype == 'invoice') { ?>
<th align="center" bgcolor="#EEEEEE" style="border: 1px solid #CCCCCC;"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PRICE') ?></th>
<?php } ?>
<th align="center" bgcolor="#EEEEEE" style="border: 1px solid #CCCCCC;"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_QTY') ?></th>
<?php if ($this->doctype == 'invoice') { ?>
<?php if ( VmConfig::get('show_tax')) { ?>
<th align="center" bgcolor="#EEEEEE" style="border: 1px solid #CCCCCC;"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_TAX') ?></th>
<?php } ?>
<th align="center" bgcolor="#EEEEEE" style="border: 1px solid #CCCCCC;"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SUBTOTAL_DISCOUNT_AMOUNT') ?></th>
<th align="right" bgcolor="#EEEEEE" style="border: 1px solid #CCCCCC;"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') ?></th>
<?php } ?>
</tr>
<?php
	$menuItemID = shopFunctionsF::getMenuItemId($this->orderDetails['details']['BT']->order_language);
  if(!class_exists('VirtueMartModelCustomfields'))require(VMPATH_ADMIN.DS.'models'.DS.'customfields.php');
  if ($version >= 3008) {
  	VirtueMartModelCustomfields::$useAbsUrls = ($this->isMail or $this->isPdf);
  }
	foreach($this->orderDetails['items'] as $item) {
		$qtt = $item->product_quantity ;
		$product_link = JURI::root().'index.php?option=com_virtuemart&amp;view=productdetails&amp;virtuemart_category_id=' . $item->virtuemart_category_id .'&amp;virtuemart_product_id=' . $item->virtuemart_product_id . '&amp;Itemid=' . $menuItemID;
?>
<tr valign="top">
<td align="left" style="border: 1px solid #CCCCCC;">
<?php echo $item->order_item_sku; ?>
</td>
<td align="left" colspan="2" style="border: 1px solid #CCCCCC;">
<p>
<a href="<?php echo $product_link; ?>"><?php echo $item->order_item_name; ?></a>
</p>
<?php
$product_attribute = VirtueMartModelCustomfields::CustomsFieldOrderDisplay($item,'FE');
echo '<p>' . $product_attribute . '</p>';
?>
</td>
<td align="center" style="border: 1px solid #CCCCCC;">
<?php echo $this->orderstatuses[$item->order_status]; ?>
</td>
<?php if ($this->doctype == 'invoice') { ?>
<td align="right"   class="priceCol" style="border: 1px solid #CCCCCC; white-space: nowrap;">
<?php
$item->product_discountedPriceWithoutTax = (float) $item->product_discountedPriceWithoutTax;
if (!empty($item->product_priceWithoutTax) && $item->product_discountedPriceWithoutTax != $item->product_priceWithoutTax) {
  echo '<del>'.$this->currency->priceDisplay($item->product_item_price, $this->user_currency_id) .'</del><br />';
  echo '<span >'.$this->currency->priceDisplay($item->product_discountedPriceWithoutTax, $this->user_currency_id) .'</span><br />';
} else {
  echo '<span >'.$this->currency->priceDisplay($item->product_item_price, $this->user_currency_id) .'</span><br />';
}
?>
</td>
<?php } ?>
<td align="right" style="border: 1px solid #CCCCCC;">
<?php echo $qtt; ?>
</td>
<?php if ($this->doctype == 'invoice') { ?>
<?php if ( VmConfig::get('show_tax')) { ?>
<td align="right" class="priceCol" style="border: 1px solid #CCCCCC; white-space: nowrap;"><?php echo "<span  class='priceColor2'>".$this->currency->priceDisplay($item->product_tax ,$this->user_currency_id, $qtt)."</span>" ?></td>
<?php } ?>
<td align="right" class="priceCol" style="border: 1px solid #CCCCCC; white-space: nowrap;">
<?php echo  $this->currency->priceDisplay( $item->product_subtotal_discount, $this->user_currency_id );  //No quantity is already stored with it ?>
</td>
<td align="right"  class="priceCol" style="border: 1px solid #CCCCCC; white-space: nowrap;">
<?php
$item->product_basePriceWithTax = (float) $item->product_basePriceWithTax;
$class = '';
if(!empty($item->product_basePriceWithTax) && $item->product_basePriceWithTax != $item->product_final_price ) {
echo '<del>'.$this->currency->priceDisplay($item->product_basePriceWithTax,$this->user_currency_id,$qtt) .'</del><br />' ;
}	elseif (empty($item->product_basePriceWithTax) && $item->product_item_price != $item->product_final_price) {
echo '<del>' . $this->currency->priceDisplay($item->product_item_price,$this->user_currency_id,$qtt) . '</del><br />';
}
echo $this->currency->priceDisplay(  $item->product_subtotal_with_tax ,$this->user_currency_id); //No quantity or you must use product_final_price ?>
</td>
<?php } ?>
</tr>
<?php
	}
?>
<?php if ($this->doctype == 'invoice') { ?>
<tr class="sectiontableentry1">
<td colspan="6" align="right" style="border: 1px solid #CCCCCC;"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_PRICES_TOTAL'); ?></td>
<?php if ( VmConfig::get('show_tax')) { ?>
<td align="right" style="border: 1px solid #CCCCCC; white-space: nowrap;"><?php echo "<span  class='priceColor2'>".$this->currency->priceDisplay($this->orderDetails['details']['BT']->order_tax, $this->user_currency_id)."</span>" ?></td>
<?php } ?>
<td align="right" style="border: 1px solid #CCCCCC; white-space: nowrap;"><?php echo "<span  class='priceColor2'>".$this->currency->priceDisplay($this->orderDetails['details']['BT']->order_discountAmount, $this->user_currency_id)."</span>" ?></td>
<td align="right" style="border: 1px solid #CCCCCC; white-space: nowrap;"><?php echo $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_salesPrice, $this->user_currency_id) ?></td>
</tr>
<?php
if ($this->orderDetails['details']['BT']->coupon_discount <> 0.00) {
  $coupon_code=$this->orderDetails['details']['BT']->coupon_code?' ('.$this->orderDetails['details']['BT']->coupon_code.')':'';
?>
<tr>
<td align="right" class="pricePad" colspan="6" style="border: 1px solid #CCCCCC;"><?php echo JText::_('COM_VIRTUEMART_COUPON_DISCOUNT').$coupon_code ?></td>
<?php if ( VmConfig::get('show_tax')) { ?>
<td align="right" style="border: 1px solid #CCCCCC;"></td>
<?php } ?>
<td align="right" style="border: 1px solid #CCCCCC;"></td>
<td align="right" style="border: 1px solid #CCCCCC; white-space: nowrap;"><?php echo $this->currency->priceDisplay($this->orderDetails['details']['BT']->coupon_discount, $this->user_currency_id); ?></td>
</tr>
<?php  } ?>
<?php
foreach($this->orderDetails['calc_rules'] as $rule){
if ($rule->calc_kind == 'DBTaxRulesBill' or $rule->calc_kind == 'DATaxRulesBill') { ?>
<tr >
<td colspan="6" align="right" class="pricePad" style="border: 1px solid #CCCCCC;"><?php echo $rule->calc_rule_name ?></td>
<?php if ( VmConfig::get('show_tax')) { ?>
<td align="right" style="border: 1px solid #CCCCCC;"></td>
<?php } ?>
<td align="right" style="border: 1px solid #CCCCCC; white-space: nowrap;"><?php echo $this->currency->priceDisplay($rule->calc_amount, $this->user_currency_id); ?></td>
<td align="right" style="border: 1px solid #CCCCCC; white-space: nowrap;"><?php echo $this->currency->priceDisplay($rule->calc_amount, $this->user_currency_id); ?></td>
</tr>
<?php
} elseif ($rule->calc_kind == 'taxRulesBill') {
?>
<tr >
<td colspan="6"  align="right" class="pricePad" style="border: 1px solid #CCCCCC;"><?php echo $rule->calc_rule_name ?></td>
<?php if ( VmConfig::get('show_tax')) { ?>
<td align="right" style="border: 1px solid #CCCCCC; white-space: nowrap;"><?php echo $this->currency->priceDisplay($rule->calc_amount, $this->user_currency_id); ?></td>
<?php } ?>
<td align="right" style="border: 1px solid #CCCCCC;"></td>
<td align="right" style="border: 1px solid #CCCCCC; white-space: nowrap;"><?php echo $this->currency->priceDisplay($rule->calc_amount, $this->user_currency_id); ?></td>
</tr>
<?php
	}
}
?>
<tr>
<td align="right" class="pricePad" colspan="6" style="border: 1px solid #CCCCCC;"><?php echo $this->orderDetails['shipmentName'] ?></td>
<?php if ( VmConfig::get('show_tax')) { ?>
<td align="right" style="border: 1px solid #CCCCCC; white-space: nowrap;"><span class='priceColor2'><?php echo $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_shipment_tax, $this->user_currency_id) ?></span></td>
<?php } ?>
<td align="right" style="border: 1px solid #CCCCCC;"></td>
<td align="right" style="border: 1px solid #CCCCCC; white-space: nowrap;"><?php echo $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_shipment + $this->orderDetails['details']['BT']->order_shipment_tax, $this->user_currency_id); ?></td>
</tr>
<tr>
<td align="right" class="pricePad" colspan="6" style="border: 1px solid #CCCCCC;"><?php echo $this->orderDetails['paymentName'] ?></td>
<?php if ( VmConfig::get('show_tax')) { ?>
<td align="right" style="border: 1px solid #CCCCCC; white-space: nowrap;"><span class="priceColor2"><?php echo $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_payment_tax, $this->user_currency_id) ?></span></td>
<?php } ?>
<td align="right" style="border: 1px solid #CCCCCC;"></td>
<td align="right" style="border: 1px solid #CCCCCC; white-space: nowrap;"><?php echo $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_payment + $this->orderDetails['details']['BT']->order_payment_tax, $this->user_currency_id); ?></td>
</tr>
<tr>
<td align="right" class="pricePad" colspan="6" style="border: 1px solid #CCCCCC;"><strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') ?></strong></td>
<?php if ( VmConfig::get('show_tax')) { ?>
<td align="right" style="border: 1px solid #CCCCCC; white-space: nowrap;"><span class="priceColor2"><?php echo $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_billTaxAmount, $this->user_currency_id); ?></span></td>
<?php } ?>
<td align="right" style="border: 1px solid #CCCCCC; white-space: nowrap;"><span class="priceColor2"><?php echo $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_billDiscountAmount, $this->user_currency_id); ?></span></td>
<td align="right" style="border: 1px solid #CCCCCC; white-space: nowrap;"><strong><?php echo $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_total, $this->user_currency_id); ?></strong></td>
</tr>
<?php } ?>
</table>
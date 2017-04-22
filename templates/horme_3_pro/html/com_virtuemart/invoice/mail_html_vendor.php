<?php
/**
 *
 * Define here the Vendor header for order mail
 * @author Spyros Petrakis
 * @link http://www.virtuemarttemplates.eu
 * @copyright Copyright (c) 2015 Spyros Petrakis. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
<table align="center" width="580" border="0" cellpadding="10" cellspacing="0" class="html-email" style="border-collapse: collapse; font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
<tr>
<td align="left" style="border: 1px solid #CCCCCC;">
<?php
  echo JText::sprintf('COM_VIRTUEMART_MAIL_VENDOR_CONTENT',$this->vendor->vendor_store_name,$this->shopperName,$this->currency->priceDisplay($this->orderDetails['details']['BT']->order_total,$this->user_currency_id),$this->orderDetails['details']['BT']->order_number);
if(!empty($this->orderDetails['details']['BT']->customer_note)){
	echo '<br /><br />'.JText::sprintf('COM_VIRTUEMART_CART_MAIL_VENDOR_SHOPPER_QUESTION',$this->orderDetails['details']['BT']->customer_note).'<br />';
}
?>
</td>
</tr>
</table>
<?php
/**
 *
 * Define here the order number, pass and order total !
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
<?php echo JText::_('COM_VIRTUEMART_MAIL_SHOPPER_YOUR_ORDER'); ?><br />
<strong><?php echo $this->orderDetails['details']['BT']->order_number ?></strong>
</td>
<td align="left" style="border: 1px solid #CCCCCC;">
<?php echo JText::_('COM_VIRTUEMART_MAIL_SHOPPER_YOUR_PASSWORD'); ?><br />
<strong><?php echo $this->orderDetails['details']['BT']->order_pass ?></strong>
</td>
<td align="center" style="border: 1px solid #CCCCCC;">
<table border="0" cellpadding="0" cellspacing="0" style="background-color:#505050; border:1px solid #353535; border-radius:5px;">
<tr>
<td align="center" valign="middle" style="color:#FFFFFF; font-family: Helvetica, Arial, sans-serif; font-size:12px; padding-top:10px; padding-right:20px; padding-bottom:10px; padding-left:20px;">
<a target="_blank" href="<?php echo JURI::root().'index.php?option=com_virtuemart&amp;view=orders&amp;layout=details&amp;order_number='.$this->orderDetails['details']['BT']->order_number.'&amp;order_pass='.$this->orderDetails['details']['BT']->order_pass; ?>" style="color:#FFFFFF; text-decoration:none;">
<?php echo JText::_('COM_VIRTUEMART_MAIL_SHOPPER_YOUR_ORDER_LINK'); ?>
</a>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td width="580" colspan="3" style="border: 1px solid #CCCCCC;">
<?php echo JText::sprintf('COM_VIRTUEMART_MAIL_SHOPPER_TOTAL_ORDER',$this->currency->priceDisplay($this->orderDetails['details']['BT']->order_total,$this->user_currency_id) ); ?>
</td>
</tr>
<tr>
<td width="580" colspan="3" style="border: 1px solid #CCCCCC;">
<?php echo JText::sprintf('COM_VIRTUEMART_MAIL_ORDER_STATUS',JText::_($this->orderDetails['details']['BT']->order_status_name)) ; ?>
</td>
</tr>
<?php $nb=count($this->orderDetails['history']);
if($this->orderDetails['history'][$nb-1]->customer_notified && !(empty($this->orderDetails['history'][$nb-1]->comments))) { ?>
<tr>
<td width="580" colspan="3" style="border: 1px solid #CCCCCC;">
<?php echo  nl2br($this->orderDetails['history'][$nb-1]->comments); ?>
</td>
</tr>
<?php } ?>
<?php if(!empty($this->orderDetails['details']['BT']->customer_note)){ ?>
<tr>
<td width="580" colspan="3" style="border: 1px solid #CCCCCC;">
<?php echo JText::sprintf('COM_VIRTUEMART_MAIL_SHOPPER_QUESTION',nl2br($this->orderDetails['details']['BT']->customer_note)) ?>
</td>
</tr>
<?php } ?>
</table>
<?php
/**
 *
 * Define here the Footer for order mail success !
 * @author Spyros Petrakis
 * @link http://www.virtuemarttemplates.eu
 * @copyright Copyright (c) 2015 Spyros Petrakis. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
/* TODO Chnage the footer place in helper or assets ???*/
if (empty($this->vendor)) {
  $vendorModel = VmModel::getModel('vendor');
  $this->vendor = $vendorModel->getVendor();
}
$link = JURI::root().'index.php?option=com_virtuemart';
?>
<?php if ($this->vendor->vendor_letter_footer == 1) { ?>
<table align="center" width="580" align="center" cellpadding="5" cellspacing="0" border="0" bgcolor="#FFFFFF" style="border-collapse: collapse; font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0 auto;">
<tr>
<td>
<?php echo JText::_('COM_VIRTUEMART_MAIL_FOOTER' ) . '<a href="'.$link.'">'.$this->vendor->vendor_name.'</a>.'; ?>
</td>
</tr>
<tr>
<td>
<?php echo $this->vendor->vendor_phone; ?>
</td>
</tr>
<tr>
<td>
<?php echo $this->vendor->vendor_store_name; ?>
</td>
</tr>
<tr>
<td>
<?php echo $this->vendor->vendor_store_desc; ?>
</td>
</tr>
</table>
<?php if ($this->vendor->vendor_letter_footer_line == 1) { ?>
<hr style="height: 1px; border: none; background-color: #CCCCCC;" />
<?php } ?>
<table width="580" id="vmdoc-footer" class="vmdoc-footer" align="center" cellpadding="5" cellspacing="0" border="0" bgcolor="#FFFFFF" style="border-collapse: collapse; font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0 auto;">
<tr>
<td>
<?php echo $this->replaceVendorFields($this->vendor->vendor_letter_footer_html, $this->vendor); ?>
</td>
</tr>
</table>
<?php } // END if footer ?>

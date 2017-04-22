<?php
/**
 *
 * Define here billing and shipping infos
 * @author Spyros Petrakis
 * @link http://www.virtuemarttemplates.eu
 * @copyright Copyright (c) 2015 Spyros Petrakis. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
<table align="center" width="580" class="html-email" cellspacing="0" cellpadding="10" border="0" style="border-collapse: collapse; font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
<tr>
<th width="290" bgcolor="#EEEEEE" style="border: 1px solid #CCCCCC;">
<?php echo JText::_('COM_VIRTUEMART_USER_FORM_BILLTO_LBL'); ?>
</th>
<th width="290" bgcolor="#EEEEEE" style="border: 1px solid #CCCCCC;">
<?php echo JText::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL'); ?>
</th>
</tr>
<tr>
<td valign="top" width="290" style="border: 1px solid #CCCCCC;">
<table style="border-collapse: collapse; font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
<?php
foreach ($this->userfields['fields'] as $field) {
if (!empty($field['value'])) {
?>
<tr>
<td width="290">
<span class="values vm2<?php echo '-' . $field['name'] ?>" ><?php echo $this->escape($field['value']) ?></span>
</td>
</tr>
<?php
  }
}
?>
</table>
</td>
<td valign="top" width="290" style="border: 1px solid #CCCCCC;">
<table style="border-collapse: collapse; font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
<?php
foreach ($this->shipmentfields['fields'] as $field) {
if (!empty($field['value'])) {
?>
<tr>
<td width="290">
<span class="values vm2<?php echo '-' . $field['name'] ?>" ><?php echo $this->escape($field['value']) ?></span>
</td>
</tr>
<?php
  }
}
?>
</table>
</td>
</tr>
</table>
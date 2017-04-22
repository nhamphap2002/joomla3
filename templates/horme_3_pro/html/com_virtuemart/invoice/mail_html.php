<?php
/**
 *
 * Main wrapper for the order email
 * @author Spyros Petrakis
 * @link http://www.virtuemarttemplates.eu
 * @copyright Copyright (c) 2015 Spyros Petrakis. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title></title>
<style type="text/css">
.ReadMsgBody {width: 100%;}
body {}
.ExternalClass table {border-collapse:separate;}
a, a:link, a:visited {text-decoration: none; color: #00788a}
a:hover {text-decoration: underline;}
h2,h2 a,h2 a:visited,h3,h3 a,h3 a:visited,h4,h5,h6,.t_cht {color:#000 !important}
.ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td {line-height: 100%}
.ExternalClass {width: 100%;}
.ExternalClass * {line-height: 100%}
span.yshortcuts { color:#000; background-color:none; border:none;}
span.yshortcuts:hover,
span.yshortcuts:active,
span.yshortcuts:focus {color:#000; background-color:none; border:none;}
</style>
</head>
<body style="margin: 0; padding: 0;">
<table width="100%" cellpadding="10" cellspacing="0" border="0" bgcolor="#CCCCCC" align="center">
<tr>
<td>
<table width="600" align="center" cellpadding="10" cellspacing="0" border="0" bgcolor="#FFFFFF" style="border-collapse: collapse; font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0 auto;">
<?php
// Shop desc for shopper and vendor
if ($this->recipient == 'shopper') { ?>
<tr>
<td>
<?php echo $this->loadTemplate('header'); ?>
</td>
</tr>
<?php } ?>
<tr>
<td>
<?php
// Message for shopper or vendor
echo $this->loadTemplate($this->recipient);
?>
</td>
</tr>
<tr>
<td>
<?php
// render shipto billto adresses
echo $this->loadTemplate('shopperaddresses');
?>
</td>
</tr>
<tr>
<td>
<?php
// render price list
echo $this->loadTemplate('pricelist');
?>
</td>
</tr>
<tr>
<td>
<?php
// end of mail
echo $this->loadTemplate('footer');
?>
</td>
</tr>
</table>
</td>
</tr>
</table>
<style type="text/css">
.ReadMsgBody {width: 100%;}
body {}
.ExternalClass table {border-collapse:separate;}
a, a:link, a:visited {text-decoration: none; color: #00788a}
a:hover {text-decoration: underline;}
h2,h2 a,h2 a:visited,h3,h3 a,h3 a:visited,h4,h5,h6,.t_cht {color:#000 !important}
.ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td {line-height: 100%}
.ExternalClass {width: 100%;}
.ExternalClass * {line-height: 100%}
span.yshortcuts { color:#000; background-color:none; border:none;}
span.yshortcuts:hover,
span.yshortcuts:active,
span.yshortcuts:focus {color:#000; background-color:none; border:none;}
</style>
</body>
</html>
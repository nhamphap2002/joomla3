<?php
/**
 *
 * Paypal payment plugin
 *
 * @author Jeremy Magne
 * @version $Id: paypal.php 7217 2013-09-18 13:42:54Z alatak $
 * @package VirtueMart
 * @subpackage payment
 * Copyright (C) 2004 - 2016 Virtuemart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */
defined('_JEXEC') or die();

$success = $viewData["success"];
$payment_name = $viewData["payment_name"];
$order = $viewData["order"];
$anz_data = $viewData["anz_response_data"];
$payment_currency = $viewData["payment_currency"];
?>
<br />
<table>
    <tr>
        <td>Your transaction has been:</td>
        <td style="width: 75%;"><?php
            if ($anz_data['status'] == 'Transaction Approved') {
                echo 'approved';
            } else {
                echo $anz_data['status'];
            }
            ?></td>
    </tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
        <td>Your receipt number is:</td>
        <td style="width: 75%;"><?php echo $anz_data['order_number']; ?></td>
    </tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <?php if (isset($anz_data['transaction_currency'])) { ?>
        <tr>
            <td>Transaction amount:</td>
            <td style="width: 75%;"><?php echo $anz_data['transaction_amount'] . ' <span style="color: red;">' . $anz_data['currency'] . '</span>'; ?></td>
        </tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr>
            <td><strong>Transaction currency:</strong></td>
            <td style="width: 75%;"><strong style="color: red;"><?php echo $anz_data['transaction_currency']; ?></strong></td>
        </tr>
    <?php } else {
        ?>
        <tr>
            <td>Transaction amount due:</td>
            <td style="width: 75%;"><?php echo $anz_data['transaction_amount'] . ' <span style="color: red;">' . $anz_data['currency'] . '</span>'; ?></td>
        </tr>
        <tr><td colspan="2">&nbsp;</td></tr>
    <?php }
    ?>
    <?php if (isset($anz_data['exchange_rate'])) { ?>
        <tr>
            <td>Exchange rate:</td>
            <td style="width: 75%;"><?php echo $anz_data['exchange_rate']; ?></td>
        </tr>
        <tr><td colspan="2">&nbsp;</td></tr>
    <?php } ?>
    <?php if (isset($anz_data['total_amount_due'])) { ?>
        <tr>
            <td>Total amount due:</td>
            <td style="width: 75%;"><?php echo $anz_data['total_amount_due'] . ' <span style="color: red;">' . $anz_data['dccCurrency'] . '</span>'; ?></td>
        </tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr>
            <td style="font-style: italic;padding-left: 16px;" colspan="2">I accept that I have been given a choice to pay in my card billing currency or in <span style="color: red;"><?php echo $anz_data['currency']; ?></span> and agree to pay the amount due at the exchange rate (which includes a 0.036 commission) in the selected transaction currency <span style="color: red;"><?php echo $anz_data['transaction_currency']; ?></span> shown. I understand that this choice is final and the currency conversion is not being conduct by Visa or MasterCard.</td>
        </tr>
    <?php } else {
        ?>
        <tr>
            <td style="font-style: italic;padding-left: 16px;" colspan="2">I accept that I have been given a choice to pay in my card billing currency or in <span style="color: red;"><?php echo $anz_data['currency']; ?></span> and agree to pay the amount due in the selected transaction currency <span style="color: red;"><?php echo $anz_data['currency']; ?></span> shown. I understand that the choice is final and the currency conversion is provided by my card issuer without consultation.</td>
        </tr>
    <?php }
    ?>
</table>

<?php if ($success) { ?>
    <br />
    <a class="vm-button-correct" href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=orders&layout=details&order_number=' . $viewData["order"]['details']['BT']->order_number . '&order_pass=' . $viewData["order"]['details']['BT']->order_pass, false) ?>"><?php echo vmText::_('COM_VIRTUEMART_ORDER_VIEW_ORDER'); ?></a>
<?php } ?>

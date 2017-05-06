<?php
defined('_JEXEC') or die();

$success = $viewData["success"];
$payment_name = $viewData["payment_name"];
$payment = $viewData["payment"];
$order = $viewData["order"];
$currency = $viewData["currency"];
?>
<br />
<table>
    <tr>
        <td><?php echo vmText::_('VMPAYMENT_STANDARD_PAYMENT_INFO'); ?></td>
        <td><?php echo $payment_name; ?></td>
    </tr>

    <tr>
        <td><?php echo vmText::_('COM_VIRTUEMART_ORDER_NUMBER'); ?></td>
        <td><?php echo $order['details']['BT']->order_number; ?></td>
    </tr>
    <?php if ($success) { ?>
        <tr>
            <td><?php echo vmText::_('VMPAYMENT_COMMWEB_API_AMOUNT'); ?></td>
            <td><?php echo  $viewData['displayTotalInPaymentCurrency']; ?></td>
        </tr>

    <?php } ?>

</table>
<?php if ($success) { ?>
    <br />
    <a class="vm-button-correct" href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=orders&layout=details&order_number=' . $viewData["order"]['details']['BT']->order_number . '&order_pass=' . $viewData["order"]['details']['BT']->order_pass, false) ?>"><?php echo vmText::_('COM_VIRTUEMART_ORDER_VIEW_ORDER'); ?></a>
<?php } ?>

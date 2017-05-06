<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Commweb
 * @author     Thang Tran <thang.fgc1207@gmail.com>
 * @copyright  2017 Thang Tran
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Commweb', JPATH_COMPONENT);
JLoader::register('CommwebController', JPATH_COMPONENT . '/controller.php');

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

if (!class_exists('VmConfig'))
    require(JPATH_ROOT . '/administrator/components/com_virtuemart/helpers/config.php');
VmConfig::loadConfig();

if (!class_exists('VirtueMartModelOrders')) {
    require(VMPATH_ADMIN . DS . 'models' . DS . 'orders.php');
}
if (!class_exists('VirtueMartModelCurrency')) {
    require(VMPATH_ADMIN . DS . 'models' . DS . 'currency.php');
}
if (!class_exists('VM_COMMWEB_HOSTED_API')) {
    include_once JPATH_COMPONENT . DS . 'models' . DS . 'class-commweb-api.php';
}
if (!class_exists('ShopFunctions')) {
    require(VMPATH_ADMIN . DS . 'helpers' . DS . 'shopfunctions.php');
}

function getTotal($total, $CurrencyID) {
    if (!class_exists('vmPSPlugin')) {
        require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
    }
    if (!class_exists('CurrencyDisplay')) {
        require(VMPATH_ADMIN . DS . 'helpers' . DS . 'currencydisplay.php');
    }
    if (!class_exists('shopFunctionsF')) {
        require(VMPATH_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');
    }
    return vmPSPlugin::getAmountValueInCurrency($total, $CurrencyID);
}

$paymentmethod_id = $_REQUEST['pm'];
$order_number = vRequest::getString('on', 0);

$commweb = new VM_COMMWEB_HOSTED_API($paymentmethod_id);
if ($commweb->getCheckoutMethod() == 'part') {

    $virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber($order_number);
    $orderModel = VmModel::getModel('orders');
    $order = $orderModel->getOrder($virtuemart_order_id);

    $street = $order['details']['BT']->address_1;
    $city = $order['details']['BT']->city;
    $billing_postcode = $order['details']['BT']->zip;
    $state = isset($order['details']['BT']->virtuemart_state_id) ? ShopFunctions::getStateByID($order['details']['BT']->virtuemart_state_id, 'state_2_code') : '';
    $country = ShopFunctions::getCountryByID($order['details']['BT']->virtuemart_country_id, 'country_3_code');

    if ($commweb->getDebugCommweb()) {
        $commweb->log('commweb.log', 'start process Commweb \n');
    }

    $image_loading = JURI::root() . '/plugins/vmpayment/commweb/images/loading.gif';


    $payment_method = 'Checkout.showPaymentPage();';

    $order['details']['BT']->order_total_aus = $commweb->getPaymentCurrencyCommweb() ? getTotal($order['details']['BT']->order_total, $commweb->getPaymentCurrencyCommweb()) : $order['details']['BT']->order_total;
    $total = number_format($order['details']['BT']->order_total_aus, 2, '.', '');
    $complete_callback = $commweb->getNotificationUrl($order);
    $order_id = $order['details']['BT']->order_number;
    $successIndicator = $_SESSION['SuccessIndicator'];

    $id_for_commweb = $order_id;
    $_SESSION['id_for_commweb'] = $id_for_commweb;
    $checkout_session_id = $commweb->getCheckoutSession($order, $id_for_commweb);

    $cancel_callback = JURI::root() . 'index.php?option=com_virtuemart&view=vmplg&task=pluginUserPaymentCancel&on=' . $order['details']['BT']->order_number . '&pm=' . $order['details']['BT']->virtuemart_paymentmethod_id . '&Itemid=' . vRequest::getInt('Itemid') . '&lang=' . vRequest::getCmd('lang', '');
    ?>
    <style>
        #loading{
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: url('<?php echo $image_loading;
    ?>') 50% 50% no-repeat;
        }
    </style>

    <script type="text/javascript">
        completeCallback = "<?php echo $complete_callback; ?>";
        cancelCallback = "<?php echo $cancel_callback; ?>";
        function errorCallback(error) {
            console.log(JSON.stringify(error))
            alert(JSON.stringify(error.explanation));
        }
    </script>

    <script src="<?php echo $commweb->_checkout_url_js; ?>" 
            data-error="errorCallback"
            data-complete="completeCallback"
            data-cancel="cancelCallback">
    </script>

    <script type="text/javascript">
        Checkout.configure({
            merchant: "<?php echo $commweb->getMerchantId(); ?>",
            session: {
                id: "<?php echo $checkout_session_id; ?>"
            },
            order: {
                amount: "<?php echo $total; ?>",
                currency: "AUD",
                description: "Commweb Order",
                id: "<?php echo $id_for_commweb; ?>"
            },
            billing: {
                address: {
                    street: "<?php echo $street; ?>",
                    city: "<?php echo $city; ?>",
                    postcodeZip: "<?php echo $billing_postcode; ?>",
                    stateProvince: "<?php echo $state; ?>",
                    country: "<?php echo $country; ?>"
                }
            },
            interaction: {
                merchant: {
                    name: "<?php echo $commweb->getMerchantName(); ?>"
                }
            }
        });
    <?php echo $payment_method; ?>
    </script>
    <div id="loading"></div>
    <?php
    exit();
}
// Execute the task.
$controller = JControllerLegacy::getInstance('Commweb');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

<?php
/*
 * Created on : Apr 22, 2017, 9:54:39 AM
 * Author: Tran Trong Thang
 * Email: trantrongthang1207@gmail.com
 * Skype: trantrongthang1207

 *  */

/* TESTAMBBUICOM201
 * Card number* 5111111111111118
 * Expiry date* 05 / 17
 * Cardholder name* admin test
 * Security code* 100
 * Street address Whiskey St
 * Postcode / Zipcode 4556
 * Country Australia
 * State / Province 
 * http://jl3.trongthang.wdev.fgct.net/index.php/component/virtuemart/cart?wc-api=WC_COMMWEB_HOSTED_CHECKOUT&resultIndicator=da47055a2b0a4b07&sessionVersion=c1832b1406
 * http://jl3.trongthang.wdev.fgct.net/index.php?option=com_virtuemart&view=cart#__hc-action-complete-3743d450ae484b00-ec52485e06
 */

defined('_JEXEC') or die('Restricted access');

if (!class_exists('vmPSPlugin')) {
    require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
}

if (!class_exists('VM_COMMWEB_HOSTED_API')) {
    require(VMPATH_ROOT . DS . 'plugins' . DS . 'vmpayment' . DS . 'commweb' . DS . 'commweb' . DS . 'class-commweb-api.php');
}

class plgVmPaymentCommweb extends vmPSPlugin {

    function __construct(& $subject, $config) {
        parent::__construct($subject, $config);

        $this->_loggable = true;
        $this->tableFields = array_keys($this->getTableSQLFields());
        $this->_tablepkey = 'id';
        $this->_tableId = 'id';
        $varsToPush = array(
            'commweb_title' => array('', 'char'),
            'commweb_description' => array('', 'char'),
            'commweb_merchant_id' => array('', 'char'),
            'commweb_api_password' => array('', 'char'),
            'merchant_name' => array('', 'char'),
            'commweb_checkout_method' => array('', 'char'),
            'secure_3d' => array('', 'char'),
            'debug' => array('', 'int'),
            'commweb_status_pending' => array('', 'char'),
            'commweb_payment_currency' => array('', 'int'),
        );

        $this->setConfigParameterable($this->_configTableFieldName, $varsToPush);
    }

    private function getMerchantId() {
        return $this->_vmpCtable->commweb_merchant_id;
    }

    private function getApiPassword() {
        return $this->_vmpCtable->commweb_api_password;
    }

    private function getMerchantName() {
        return $this->_vmpCtable->merchant_name;
    }

    private function getCheckoutMethod() {
        return $this->_vmpCtable->commweb_checkout_method;
    }

    private function getDebugCommweb() {
        return $this->_vmpCtable->debug;
    }

    private function getSecure3d() {
        return $this->_vmpCtable->secure_3d;
    }

    public function getStatusPendingCommweb() {
        return $this->_vmpCtable->commweb_status_pending;
    }

    public function getPaymentCurrencyCommweb() {
        return $this->_vmpCtable->commweb_payment_currency;
    }

    /**
     * @return string
     */
    public function getVmPluginCreateTableSQL() {

        return $this->createTableSQL('Payment commweb Table');
    }

    /**
     * @return array
     */
    function getTableSQLFields() {

        $SQLfields = array(
            'id' => ' INT(11) unsigned NOT NULL AUTO_INCREMENT ',
            'virtuemart_order_id' => ' int(1) UNSIGNED DEFAULT NULL',
            'order_number' => ' char(32) DEFAULT NULL',
            'virtuemart_paymentmethod_id' => ' mediumint(1) UNSIGNED DEFAULT NULL',
            'payment_name' => 'varchar(5000)',
            'payment_order_total' => 'decimal(15,5) NOT NULL DEFAULT \'0.00000\'',
            'payment_currency' => 'char(3)',
            'email_currency' => 'char(3)',
            'cost_per_transaction' => ' decimal(10,2) DEFAULT NULL ',
            'cost_percent_total' => ' decimal(10,2) DEFAULT NULL ',
            'commweb_raw' => ' varchar(512) DEFAULT NULL',
            'tax_id' => 'smallint(1)'
        );
        return $SQLfields;
    }

    public function onBeforeCompileHead() {

        if (isset($_SESSION['order_number'])) {
            if (!class_exists('VirtueMartModelOrders')) {
                require(VMPATH_ADMIN . DS . 'models' . DS . 'orders.php');
            }
            $order_number = $_SESSION['order_number'];
            $virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber($order_number);
            $orderModel = VmModel::getModel('orders');
            $order = $orderModel->getOrder($virtuemart_order_id);
            $complete_callback = $this->getNotificationUrl($order);
            $cancel_callback = JURI::root() . 'index.php?option=com_virtuemart&view=vmplg&task=pluginUserPaymentCancel&on=' . $order['details']['BT']->order_number . '&pm=' . $order['details']['BT']->virtuemart_paymentmethod_id . '&Itemid=' . vRequest::getInt('Itemid') . '&lang=' . vRequest::getCmd('lang', '');
            ?>
            <script src="https://paymentgateway.commbank.com.au/checkout/version/36/checkout.js" 
                    data-complete="completeCallback"
                    data-error="errorCallback"
                    data-cancel="cancelCallback">
            </script>

            <script type="text/javascript">
                completeCallback = "<?php echo $complete_callback; ?>";
                cancelCallback = "<?php echo $cancel_callback; ?>";
                function errorCallback(error) {
                    alert(JSON.stringify(error.explanation));
                }
            </script>
            <?php
        }
    }

    function plgVmConfirmedOrder($cart, $order) {

        if (!($this->_currentMethod = $this->getVmPluginMethod($order['details']['BT']->virtuemart_paymentmethod_id))) {
            return NULL; // Another method was selected, do nothing
        }
        if (!$this->selectedThisElement($this->_currentMethod->payment_element)) {
            return FALSE;
        }

        if (!class_exists('VirtueMartModelOrders')) {
            require(VMPATH_ADMIN . DS . 'models' . DS . 'orders.php');
        }
        if (!class_exists('VirtueMartModelCurrency')) {
            require(VMPATH_ADMIN . DS . 'models' . DS . 'currency.php');
        }
        $html = '';
        $this->_currentMethod->payment_currency = $order['details']['BT']->user_currency_id;
        $payment_name = $this->renderPluginName($this->_currentMethod, $order);

        $dbValues['order_number'] = $order['details']['BT']->order_number;
        $dbValues['payment_name'] = $payment_name;
        $dbValues['virtuemart_paymentmethod_id'] = $cart->virtuemart_paymentmethod_id;
        $dbValues['cost_per_transaction'] = $this->_currentMethod->cost_per_transaction;
        $dbValues['cost_percent_total'] = $this->_currentMethod->cost_percent_total;
        $dbValues['payment_currency'] = $this->getPaymentCurrencyCommweb() ? $this->getPaymentCurrencyCommweb() : $order['details']['BT']->user_currency_id;
        $dbValues['payment_order_total'] = $this->getPaymentCurrencyCommweb() ? $this->getTotal($order['details']['BT']->order_total) : $order['details']['BT']->order_total;
        $order['details']['BT']->order_total_aus = $this->getPaymentCurrencyCommweb() ? $this->getTotal($order['details']['BT']->order_total, $this->getPaymentCurrencyCommweb()) : $order['details']['BT']->order_total;
        $dbValues['tax_id'] = $this->_currentMethod->tax_id;
        $this->storePSPluginInternalData($dbValues);
        VmConfig::loadJLang('com_virtuemart_orders', TRUE);
        $cart->_confirmDone = FALSE;
        $cart->_dataValidated = FALSE;
        $cart->setCartIntoSession();

        $street = $order['details']['BT']->address_1;
        $city = $order['details']['BT']->city;
        $billing_postcode = $order['details']['BT']->zip;
        $state = isset($order['details']['BT']->virtuemart_state_id) ? ShopFunctions::getStateByID($order['details']['BT']->virtuemart_state_id, 'state_2_code') : '';
        $country = ShopFunctions::getCountryByID($order['details']['BT']->virtuemart_country_id, 'country_3_code');


        $merchant_id = $this->getMerchantId();
        $api_password = $this->getApiPassword();
        $merchant_name = $this->getMerchantName();
        $checkout_method = $this->getCheckoutMethod();
        $debug = $this->getDebugCommweb();
        $paymentmethod_id = $this->_vmpCtable->virtuemart_paymentmethod_id;

        $commweb = new VM_COMMWEB_HOSTED_API($merchant_id, $api_password, $merchant_name, $checkout_method, $debug, $paymentmethod_id);


        if ($this->getDebugCommweb()) {
            $commweb->log('commweb.log', 'start process Commweb \n');
        }

        $image_loading = JURI::root() . '/plugins/vmpayment/commweb/images/loading.gif';

        if ($this->getCheckoutMethod() == 'Lightbox') {
            $payment_method = 'Checkout.showLightbox();';
        } else {
            $payment_method = 'Checkout.showPaymentPage();';
        }

        $total = number_format($order['details']['BT']->order_total, 2, '.', '');
        $complete_callback = $this->getNotificationUrl($order);
        $order_id = $order['details']['BT']->order_number;
        $successIndicator = $_SESSION['SuccessIndicator'];

        $id_for_commweb = $order_id;
        $_SESSION['id_for_commweb'] = $id_for_commweb;
        $checkout_session_id = $commweb->getCheckoutSession($order, $id_for_commweb);

        $cancel_callback = JURI::root() . 'index.php?option=com_virtuemart&view=vmplg&task=pluginUserPaymentCancel&on=' . $order['details']['BT']->order_number . '&pm=' . $order['details']['BT']->virtuemart_paymentmethod_id . '&Itemid=' . vRequest::getInt('Itemid') . '&lang=' . vRequest::getCmd('lang', '');

        unset($_SESSION['order_number']);
        unset($_SESSION['jscommweb']);

        ob_start();
        if ($this->getCheckoutMethod() == 'Lightbox') {
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

            <script src="<?php echo $commweb->_checkout_url_js; ?>" 
                    data-error="errorCallback"
                    data-complete="completeCallback"
                    data-cancel="cancelCallback">
            </script>

            <script type="text/javascript">
                completeCallback = "<?php echo $complete_callback; ?>";
                cancelCallback = "<?php echo $cancel_callback; ?>";
                function errorCallback(error) {
                    alert(JSON.stringify(error.explanation));
                }
            </script>
            <script type="text/javascript">
                Checkout.configure({
                    merchant: "<?php echo $this->getMerchantId(); ?>",
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
                            name: "<?php echo $this->getMerchantName(); ?>"
                        }
                    }
                });
            <?php echo $payment_method; ?>
            </script>
            <div id="loading"></div>
            <?php
            $html = ob_get_contents();
            ob_end_clean();
        } else {
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
                Checkout.configure({
                    merchant: "<?php echo $this->getMerchantId(); ?>",
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
                            name: "<?php echo $this->getMerchantName(); ?>"
                        }
                    }
                });
            <?php echo $payment_method; ?>
            </script>
            <div id="loading"></div>
            <?php
            $html = ob_get_contents();
            ob_end_clean();
            $jscommweb = '';
            if (!isset($_SESSION['order_number'])) {
                ob_start();
                ?>
                <script src="<?php echo $commweb->_checkout_url_js; ?>" 
                        data-return="completeCallback"
                        data-complete="completeCallback"
                        data-cancel="cancelCallback">
                </script>

                <script type="text/javascript">
                    completeCallback = "<?php echo $complete_callback; ?>";
                    cancelCallback = "<?php echo $cancel_callback; ?>";
                </script>
                <?php
                $jscommweb = ob_get_contents();
                ob_end_clean();
            }
            $_SESSION['order_number'] = $order['details']['BT']->order_number;
            $_SESSION['jscommweb'] = $jscommweb;
        }
        vRequest::setVar('html', $html);
    }

    public function getTotal($total, $CurrencyID) {
        if (!class_exists('CurrencyDisplay')) {
            require(VMPATH_ADMIN . DS . 'helpers' . DS . 'currencydisplay.php');
        }
        if (!class_exists('shopFunctionsF')) {
            require(VMPATH_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');
        }
        return vmPSPlugin::getAmountValueInCurrency($total, $CurrencyID);
    }

    public function getCountryCoede($CountryID) {
        return ShopFunctions::getCurrencyByID($CountryID, 'currency_code_3');
    }

    /**
     * @param null $msg
     */
    function redirectToCart($msg = NULL) {
        $app = JFactory::getApplication();
        $app->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&Itemid=' . vRequest::getInt('Itemid'), false), $msg, 'error');
    }

    /**
     * @param $virtuemart_paymentmethod_id
     * @param $paymentCurrencyId
     * @return bool|null
     */
    function plgVmgetPaymentCurrency($virtuemart_paymentmethod_id, &$paymentCurrencyId) {

        if (!($this->_currentMethod = $this->getVmPluginMethod($virtuemart_paymentmethod_id))) {
            return NULL; // Another method was selected, do nothing
        }
        if (!$this->selectedThisElement($this->_currentMethod->payment_element)) {
            return FALSE;
        }
        $this->getPaymentCurrency($this->_currentMethod);
        $paymentCurrencyId = $this->_currentMethod->payment_currency;
    }

    function plgVmOnUserPaymentCancel() {

        if (!class_exists('VirtueMartModelOrders')) {
            require(VMPATH_ADMIN . DS . 'models' . DS . 'orders.php');
        }

        $order_number = vRequest::getString('on', '');
        $virtuemart_paymentmethod_id = vRequest::getInt('pm', '');
        if (empty($order_number) or empty($virtuemart_paymentmethod_id) or ! $this->selectedThisByMethodId($virtuemart_paymentmethod_id)) {
            return NULL;
        }
        if (!($virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber($order_number))) {
            return NULL;
        }
        if (!($paymentTable = $this->getDataByOrderNumber($order_number))) {
            return NULL;
        }
        $this->handlePaymentUserCancel($virtuemart_order_id);

        unset($_SESSION['order_number']);
        unset($_SESSION['jscommweb']);
        return TRUE;
    }

    function _handlePaymentCancel($virtuemart_order_id, $msg, $allow_redirect = true) {
        if ($virtuemart_order_id) {
            if (!class_exists('VirtueMartModelOrders')) {
                require(VMPATH_ADMIN . DS . 'models' . DS . 'orders.php');
            }
            $modelOrder = VmModel::getModel('orders');
            $modelOrder->remove(array($virtuemart_order_id));
        }

        if ($allow_redirect) {
            $mainframe = JFactory::getApplication();
            $mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'), $msg, 'error');
            exit();
        }
        return true;
    }

    /**
     * @param $html
     * @return bool|null|string
     */
    function plgVmOnPaymentResponseReceived(&$html) {

        if (!class_exists('VirtueMartCart')) {
            require(VMPATH_SITE . DS . 'helpers' . DS . 'cart.php');
        }
        if (!class_exists('shopFunctionsF')) {
            require(VMPATH_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');
        }
        if (!class_exists('VirtueMartModelOrders')) {
            require(VMPATH_ADMIN . DS . 'models' . DS . 'orders.php');
        }
        vmLanguage::loadJLang('com_virtuemart_orders', TRUE);

        // the payment itself should send the parameter needed.
        $virtuemart_paymentmethod_id = vRequest::getInt('pm', 0);

        $order_number = vRequest::getString('on', 0);
        $vendorId = 0;
        if (!($this->_currentMethod = $this->getVmPluginMethod($virtuemart_paymentmethod_id))) {
            return NULL; // Another method was selected, do nothing
        }
        if (!$this->selectedThisElement($this->_currentMethod->payment_element)) {
            return NULL;
        }

        if (!($virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber($order_number))) {
            return NULL;
        }
        if (!($payments = $this->getDatasByOrderNumber($order_number))) {
            return '';
        }

        vmLanguage::loadJLang('com_virtuemart');
        $orderModel = VmModel::getModel('orders');
        $order = $orderModel->getOrder($virtuemart_order_id);

        $this->_currentMethod->payment_currency = $this->getPaymentCurrency($this->_currentMethod, $order['details']['BT']->payment_currency_id);
        $payment_name = $this->renderPluginName($this->_currentMethod);

        // to do: this
        $this->debugLog($payment, 'plgVmOnPaymentResponseReceived', 'debug', false);
        if (!class_exists('CurrencyDisplay')) {
            require(VMPATH_ADMIN . DS . 'helpers' . DS . 'currencydisplay.php');
        }
        //$currency = CurrencyDisplay::getInstance('', $order['details']['BT']->order_currency);
        $currency = CurrencyDisplay::getInstance('', $order['details']['BT']->payment_currency_id);

        $success = true;
        $payment_currency = $this->_currentMethod->commweb_payment_currency ? $this->_currentMethod->commweb_payment_currency : $method->payment_currency;
        $totalInPaymentCurrency = vmPSPlugin::getAmountInCurrency($order['details']['BT']->order_total, $payment_currency);

        $html = $this->renderByLayout('post_payment', array("success" => $success,
            "payment_name" => $payment_name,
            "payment" => $paypal_data,
            "order" => $order,
            'displayTotalInPaymentCurrency' => $totalInPaymentCurrency['display'],
            "currency" => $currency,
        ));

        //We delete the old stuff
        // get the correct cart / session
        $cart = VirtueMartCart::getCart();
        $cart->emptyCart();
        return TRUE;
    }

    function plgVmOnPaymentNotification() {

        if (!class_exists('VirtueMartCart')) {
            require(VMPATH_SITE . DS . 'helpers' . DS . 'cart.php');
        }
        if (!class_exists('shopFunctionsF')) {
            require(VMPATH_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');
        }
        if (!class_exists('VirtueMartModelOrders')) {
            require(VMPATH_ADMIN . DS . 'models' . DS . 'orders.php');
        }
        VmConfig::loadJLang('com_virtuemart_orders', TRUE);


        $successIndicator = $_SESSION['SuccessIndicator'];


        if (isset($_REQUEST['resultIndicator']) && $_REQUEST['resultIndicator'] == $successIndicator) {

            $merchant_id = $this->getMerchantId();
            $api_password = $this->getApiPassword();
            $merchant_name = $this->getMerchantName();
            $checkout_method = $this->getCheckoutMethod();
            $debug = $this->getDebugCommweb();
            $paymentmethod_id = vRequest::getString('pm', 0);

            $commweb = new VM_COMMWEB_HOSTED_API($merchant_id, $api_password, $merchant_name, $checkout_method, $debug, $paymentmethod_id);
            $order_detail_commweb = $commweb->getOrderCommwebDetail($_SESSION['id_for_commweb']);

            if ($commweb->getDebug())
                $commweb->log('commweb.log', date('Y-m-d H:i:s') . "\n Response from Complete callback of commweb: \n" . print_r($order_detail_commweb, true) . "\n");

            if ($order_detail_commweb['result'] == 'SUCCESS') {
                if ($commweb->getCommwebAllow3DSource()) {
                    if (isset($order_detail_commweb['transaction_3DSecure_authenticationStatus']) && $order_detail_commweb['transaction_3DSecure_authenticationStatus'] == 'AUTHENTICATION_SUCCESSFUL') {
                        $process_order = true;
                    } else {
                        $process_order = false;
                    }
                } else {
                    $process_order = true;
                }
                if ($process_order == true) {
                    $order_number = vRequest::getString('on', 0);
                    $virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber($order_number);
                    $orderModel = VmModel::getModel('orders');
                    $order = $orderModel->getOrder($virtuemart_order_id);
                    $order['customer_notified'] = 1;
                    $order['virtuemart_order_id'] = $virtuemart_order_id;
                    $order['order_status'] = $commweb->getStatusPendingCommweb();
                    $order['comments'] = JText::sprintf('Your order number [%s] was confirmed', $order_number) . '. ' . $anzvas_data;
                    $orderModel->updateStatusForOneOrder($virtuemart_order_id, $order, true);
                    $cart = VirtueMartCart::getCart();
                    $cart->emptyCart();
                    unset($_SESSION['order_number']);
                    unset($_SESSION['jscommweb']);
                    $url_success = $this->getSuccessUrl($order);
                    $app = JFactory::getApplication();
                    $app->redirect($url_success);
                } else {
                    $this->redirectToCart('Your transaction was unsuccessful, please check your details and try again(error account 3d). Please contact the server administrator');
                }
            } else {
                $this->redirectToCart('Your transaction was unsuccessful, please check your details and try again. Please contact the server administrator');
            }
        }
    }

    protected function renderPluginName($activeMethod) {
        $plugin_name = $this->_psType . '_name';
        $plugin_desc = $this->_psType . '_desc';
        $logo = '';
        $pluginName = $logo . '<span class="' . $this->_type . '_name">' . $activeMethod->$plugin_name . '</span>';
        if (!empty($activeMethod->$plugin_desc)) {
            $pluginName .= '<span class="' . $this->_type . '_description">' . $activeMethod->$plugin_desc . '</span>';
        }
        return $pluginName;
    }

    function plgVmOnShowOrderBEPayment($virtuemart_order_id, $payment_method_id) {

        if (!$this->selectedThisByMethodId($payment_method_id)) {
            return NULL; // Another method was selected, do nothing
        }
        if (!($this->_currentMethod = $this->getVmPluginMethod($payment_method_id))) {
            return FALSE;
        }
        if (!($paymentTable = $this->getDataByOrderId($virtuemart_order_id))) {
            return '';
        }

        $html = '<table class="adminlist table" >' . "\n";
        $html .= $this->getHtmlHeaderBE();
        $html .= ' <tr class="row1"><td><strong>' . vmText::_('VMPAYMENT_PAYPAL_DATE') . '</strong></td><td align="left"><strong>' . $paymentTable->created_on . '</strong></td></tr> ';
        $html .= $this->getHtmlRowBE('Payment name', $paymentTable->payment_name);
        if ($paymentTable->payment_order_total and $paymentTable->payment_order_total != 0.00) {
            $html .= $this->getHtmlRowBE('COM_VIRTUEMART_TOTAL', $this->getTotal(number_format($paymentTable->payment_order_total, 2, '.', ''), $paymentTable->payment_currency) . " " . shopFunctions::getCurrencyByID($paymentTable->payment_currency, 'currency_code_3'));
        }
        $html .= '</table>' . "\n";

        return $html;
    }

    /**
     * @param   int $virtuemart_order_id
     * @param string $order_number
     * @return mixed|string
     */
    private function _getPaypalInternalData($virtuemart_order_id, $order_number = '') {
        if (empty($order_number)) {
            $orderModel = VmModel::getModel('orders');
            $order_number = $orderModel->getOrderNumber($virtuemart_order_id);
        }
        $db = JFactory::getDBO();
        $q = 'SELECT * FROM `' . $this->_tablename . '` WHERE ';
        $q .= " `order_number` = '" . $order_number . "'";

        $db->setQuery($q);
        if (!($payments = $db->loadObjectList())) {
            // JError::raiseWarning(500, $db->getErrorMsg());
            return '';
        }
        return $payments;
    }

    protected function checkConditions($cart, $activeMethod, $cart_prices) {

        //Check method publication start
        if ($activeMethod->publishup) {
            $nowDate = JFactory::getDate();
            $publish_up = JFactory::getDate($activeMethod->publishup);
            if ($publish_up->toUnix() > $nowDate->toUnix()) {
                return FALSE;
            }
        }
        if ($activeMethod->publishdown) {
            $nowDate = JFactory::getDate();
            $publish_down = JFactory::getDate($activeMethod->publishdown);
            if ($publish_down->toUnix() <= $nowDate->toUnix()) {
                return FALSE;
            }
        }
        $this->convert_condition_amount($activeMethod);

        $address = $cart->getST();

        $amount = $this->getCartAmount($cart_prices);
        $amount_cond = ($amount >= $activeMethod->min_amount AND $amount <= $activeMethod->max_amount
                OR ( $activeMethod->min_amount <= $amount AND ( $activeMethod->max_amount == 0)));

        $countries = array();
        if (!empty($activeMethod->countries)) {
            if (!is_array($activeMethod->countries)) {
                $countries[0] = $activeMethod->countries;
            } else {
                $countries = $activeMethod->countries;
            }
        }
        // probably did not gave his BT:ST address
        if (!is_array($address)) {
            $address = array();
            $address['virtuemart_country_id'] = 0;
        }

        if (!isset($address['virtuemart_country_id'])) {
            $address['virtuemart_country_id'] = 0;
        }
        if (in_array($address['virtuemart_country_id'], $countries) || count($countries) == 0) {
            if ($amount_cond) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * @param $jplugin_id
     * @return bool|mixed
     */
    function plgVmOnStoreInstallPaymentPluginTable($jplugin_id) {
        if ($jplugin_id != $this->_jid) {
            return FALSE;
        }
        return $this->onStoreInstallPluginTable($jplugin_id);
    }

    public function plgVmOnSelectCheckPayment(VirtueMartCart $cart, &$msg) {
        if (!$this->selectedThisByMethodId($cart->virtuemart_paymentmethod_id)) {
            return null; // Another method was selected, do nothing
        }

        if (!($this->_currentMethod = $this->getVmPluginMethod($cart->virtuemart_paymentmethod_id))) {
            return FALSE;
        }
        return true;
    }

    public function plgVmOnCancelPayment(&$order, $old_order_status) {
        return NULL;
    }

    public function plgVmDisplayListFEPayment(VirtueMartCart $cart, $selected = 0, &$htmlIn) {
        return $this->displayListFE($cart, $selected, $htmlIn);
    }

    function plgVmOnCheckoutCheckDataPayment(VirtueMartCart $cart) {

        if (!$this->selectedThisByMethodId($cart->virtuemart_paymentmethod_id)) {
            return NULL; // Another method was selected, do nothing
        }

        if (!($this->_currentMethod = $this->getVmPluginMethod($cart->virtuemart_paymentmethod_id))) {
            return FALSE;
        }
        $cart->getCartPrices();
        return true;
    }

    public function plgVmOnSelectedCalculatePricePayment(VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name) {
        if (!($selectedMethod = $this->getVmPluginMethod($cart->virtuemart_paymentmethod_id))) {
            return FALSE;
        }
        return $this->onSelectedCalculatePrice($cart, $cart_prices, $cart_prices_name);
    }

    function plgVmOnCheckAutomaticSelectedPayment(VirtueMartCart $cart, array $cart_prices = array(), &$paymentCounter) {
        return $this->onCheckAutomaticSelected($cart, $cart_prices, $paymentCounter);
    }

    public function plgVmOnShowOrderFEPayment($virtuemart_order_id, $virtuemart_paymentmethod_id, &$payment_name) {
        $this->onShowOrderFE($virtuemart_order_id, $virtuemart_paymentmethod_id, $payment_name);
    }

    function plgVmonShowOrderPrintPayment($order_number, $method_id) {
        return $this->onShowOrderPrint($order_number, $method_id);
    }

    function plgVmDeclarePluginParamsPaymentVM3(&$data) {
        return $this->declarePluginParams('payment', $data);
    }

    function plgVmSetOnTablePluginParamsPayment($name, $id, &$table) {
        return $this->setOnTablePluginParams($name, $id, $table);
    }

    private function getSuccessUrl($order, $commweb_data_params) {
        return JURI::base() . "index.php?option=com_virtuemart&view=pluginresponse&task=pluginresponsereceived&pm=" . $order['details']['BT']->virtuemart_paymentmethod_id . '&on=' . $order['details']['BT']->order_number . "&Itemid=" . vRequest::getInt('Itemid') . '&lang=' . vRequest::getCmd('lang', '') . $commweb_data_params;
    }

    private function getNotificationUrl($order) {

        return JURI::base() . "index.php?option=com_virtuemart&view=pluginresponse&task=pluginnotification&pm=" . $order['details']['BT']->virtuemart_paymentmethod_id . '&on=' . $order['details']['BT']->order_number . "&Itemid=" . vRequest::getInt('Itemid') . '&lang=' . vRequest::getCmd('lang', '');
    }

    public function debugLog($message, $title = '', $type = 'message', $doDebug = true) {

        if (isset($this->_currentMethod) and ! $this->_currentMethod->log and $type != 'error') {
            //Do not log message messages if we are not in LOG mode
            return;
        }

        if ($type == 'error') {
            $this->sendEmailToVendorAndAdmins();
        }

        $this->logInfo($title . ': ' . print_r($message, true), $type, true);
    }

}

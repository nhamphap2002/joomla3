<?php

/**
 *
 * AnzVas payment plugin
 *
 * @author Fgc
 */
defined('_JEXEC') or die('Restricted access');
if (!class_exists('vmPSPlugin')) {
    require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
}

if (!class_exists('MasterCardUtilities')) {
    require(VMPATH_ROOT . DS . 'plugins' . DS . 'vmpayment' . DS . 'anzvas' . DS . 'helpers' . DS . 'MasterCardUtils.php');
}

class plgVmPaymentAnzvas extends vmPSPlugin {

    // URL of the API on MasterCard's VAS gateway server
    private $stigApiUrl = 'https://dcc.migs.mastercard.com.au/stig/stig.api';
    private $locale = 'en_AU';

    function __construct(& $subject, $config) {
        parent::__construct($subject, $config);

        $this->_loggable = true;
        $this->tableFields = array_keys($this->getTableSQLFields());
        $this->_tablepkey = 'id';
        $this->_tableId = 'id';
        $varsToPush = array(
            'company_id' => array('', 'char'),
            'shared_key' => array('', 'char'),
            'payment_currency' => array('', 'int')
        );

        $this->setConfigParameterable($this->_configTableFieldName, $varsToPush);
    }

    private function getSharedKey() {
        return $this->_vmpCtable->shared_key;
    }

    private function getCompanyId() {
        return $this->_vmpCtable->company_id;
    }

    public function getVmPluginCreateTableSQL() {

        return $this->createTableSQL('Payment ANZ VAS Table');
    }

    function getTableSQLFields() {

        $SQLfields = array(
            'id' => 'int(11) UNSIGNED NOT NULL AUTO_INCREMENT',
            'virtuemart_order_id' => 'int(1) UNSIGNED',
            'order_number' => 'char(64)',
            'virtuemart_paymentmethod_id' => 'mediumint(1) UNSIGNED',
            'payment_name' => 'varchar(5000)',
            'payment_order_total' => 'decimal(15,5) NOT NULL',
            'payment_currency' => 'smallint(1)',
            'cost_per_transaction' => 'decimal(10,2)',
            'cost_percent_total' => 'decimal(10,2)',
            'tax_id' => 'smallint(1)'
        );
        return $SQLfields;
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
        $dbValues['payment_currency'] = $order['details']['BT']->user_currency_id;
        $dbValues['payment_order_total'] = $order['details']['BT']->order_total;
        $dbValues['tax_id'] = $this->_currentMethod->tax_id;
        $this->storePSPluginInternalData($dbValues);
        VmConfig::loadJLang('com_virtuemart_orders', TRUE);
        $cart->_confirmDone = FALSE;
        $cart->_dataValidated = FALSE;
        $cart->setCartIntoSession();
        //process submit to ANZ
        $total_payment = vmPSPlugin::getAmountInCurrency($order['details']['BT']->order_total, $this->_currentMethod->payment_currency);
        $paymentAmount = number_format((float) $total_payment['value'], 2, '', '');
        $config = JFactory::getConfig();
        $currency = shopFunctions::getCurrencyByID($this->_currentMethod->payment_currency, 'currency_code_3');

        $description = $config->get('sitename');
        $transactionType = 'purchase';
        $messageReference = 'Pay for OrderID:' . $order['details']['BT']->order_number;
        $referenceNumber = $order['details']['BT']->order_number;
        //
        $locale = $this->locale;
        $companyId = $this->getCompanyId();
        $_SESSION['company_id'] = $companyId;
        $callbackUrl = $this->getNotificationUrl();
        $stigApiUrl = $this->stigApiUrl;
        $sharedKey = $this->getSharedKey();
        $_SESSION['shared_key'] = $sharedKey;

        $MasterCardUtilities = new MasterCardUtilities($locale, $companyId, $callbackUrl, $stigApiUrl, $sharedKey);
        $dom = $MasterCardUtilities->MakeTransactionRequest($transactionType, $referenceNumber, $messageReference, $currency, $paymentAmount, $description);

        $redirectToken = $dom->xpath('//ns3:message/ns3:transactionResponse/ns4:redirectToken');
        if (empty($redirectToken)) {
            $messageError = $dom->xpath('//ns3:message/ns3:errorDetails');
            $transactionError = $dom->xpath('//ns3:message/ns3:transactionResponse/ns4:transactionResponse/ns2:responseMessage');
            $msg = $messageError[0] . '. ' . $transactionError[0];
            $this->redirectToCart($msg);
        } else {
            $redirectUrl = $dom->xpath('//ns3:message/ns3:transactionResponse/ns4:redirectUrl');
            $transactionId = $dom->xpath('//ns3:message/ns3:transactionResponse/ns4:transactionId');
            TransactionStore::Add($redirectToken[0], $transactionId[0]);
            header('Location: ' . $redirectUrl[0] . '?r=' . $redirectToken[0]);
        }
        exit();
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
        VmConfig::loadJLang('com_virtuemart_orders', TRUE);

        // the payment itself should send the parameter needed.
        $virtuemart_paymentmethod_id = vRequest::getInt('pm', 0);
        $expresscheckout = vRequest::getVar('expresscheckout', '');
        if ($expresscheckout) {
            return;
        }
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
        $payment_name = $this->renderPluginName($this->_currentMethod);
        $payment = end($payments);

        VmConfig::loadJLang('com_virtuemart');
        $orderModel = VmModel::getModel('orders');
        $order = $orderModel->getOrder($virtuemart_order_id);
        vmdebug('plgVmOnPaymentResponseReceived', $payment);
        if (!class_exists('CurrencyDisplay')) {
            require(VMPATH_ADMIN . DS . 'helpers' . DS . 'currencydisplay.php');
        }
        $totalInPaymentCurrency = vmPSPlugin::getAmountInCurrency($order['details']['BT']->order_total, $this->_currentMethod->payment_currency);
        $currency = shopFunctions::getCurrencyByID($this->_currentMethod->payment_currency, 'currency_code_3');
        $success = true;
        $anzvas_data = array(
            'status' => $_REQUEST['status'],
            'order_number' => $_REQUEST['order_number'],
            'transaction_amount' => $_REQUEST['transaction_amount']
        );
        if (isset($_REQUEST['disclaimer'])) {
            $anzvas_data['disclaimer'] = $_REQUEST['disclaimer'];
        }
        if (isset($_REQUEST['transaction_currency'])) {
            $anzvas_data['transaction_currency'] = $_REQUEST['transaction_currency'];
        }
        if (isset($_REQUEST['exchange_rate'])) {
            $anzvas_data['exchange_rate'] = $_REQUEST['exchange_rate'];
        }
        if (isset($_REQUEST['total_amount_due'])) {
            $anzvas_data['total_amount_due'] = $_REQUEST['total_amount_due'];
        }
        if (isset($_REQUEST['currency'])) {
            $anzvas_data['currency'] = $_REQUEST['currency'];
        }
        if (isset($_REQUEST['dccCurrency'])) {
            $anzvas_data['dccCurrency'] = $_REQUEST['dccCurrency'];
        }
        $html = $this->renderByLayout('response', array("success" => $success,
            "payment_name" => $payment_name,
            "order" => $order,
            "anz_response_data" => $anzvas_data,
            "total" => $totalInPaymentCurrency['display'],
            "payment_currency" => $currency
        ));
        $cart = VirtueMartCart::getCart();
        $cart->emptyCart();
        return TRUE;
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

        $locale = $this->locale;
        $companyId = $_SESSION['company_id'];
        $callbackUrl = $this->getNotificationUrl();
        $stigApiUrl = $this->stigApiUrl;
        $sharedKey = $_SESSION['shared_key'];

        $MasterCardUtilities = new MasterCardUtilities($locale, $companyId, $callbackUrl, $stigApiUrl, $sharedKey);
        $dom = $MasterCardUtilities->MakeTransactionQuery($_REQUEST['r']);

        $resultCode = $dom->xpath('//ns3:message/ns3:transactionResponse/ns4:transactionResponse/ns2:responseCode');
        $result = $dom->xpath('//ns3:message/ns3:transactionResponse/ns4:transactionResponse/ns2:responseMessage');
        $receiptNo = $dom->xpath('//ns3:message/ns3:transactionResponse/ns4:referenceNumber');
        $amount = $dom->xpath('//ns3:message/ns3:transactionResponse/ns4:amount/ns2:amount');
        $exponent = $dom->xpath('//ns3:message/ns3:transactionResponse/ns4:amount/ns2:exponent');
        $currency = $dom->xpath('//ns3:message/ns3:transactionResponse/ns4:amount/ns2:currency');
        $transactionId = $dom->xpath('//ns3:message/ns3:transactionResponse/ns4:transactionId');

        if ($dom->xpath('//ns3:message/ns3:transactionResponse/ns4:dccInformation') != false) {
            $disclaimer = $dom->xpath('//ns3:message/ns3:transactionResponse/ns4:dccInformation/ns2:dccOfferDisclaimer');
            $dccAccepted = $dom->xpath('//ns3:message/ns3:transactionResponse/ns4:dccInformation/ns2:dccOfferAccepted');

            if ($dccAccepted[0] == 'true') {
                $dccAmount = $dom->xpath('//ns3:message/ns3:transactionResponse/ns4:dccInformation/ns2:convertedAmount/ns2:amount');
                $dccExponent = $dom->xpath('//ns3:message/ns3:transactionResponse/ns4:dccInformation/ns2:convertedAmount/ns2:exponent');
                $dccCurrency = $dom->xpath('//ns3:message/ns3:transactionResponse/ns4:dccInformation/ns2:convertedAmount/ns2:currency');
                $dccFxRate = $dom->xpath('//ns3:message/ns3:transactionResponse/ns4:dccInformation/ns2:conversionExchangeRate');
            }
        }
        if ($resultCode[0] == '000') {
            $anzvaz_response = array(
                'status' => $result[0],
                'order_number' => $receiptNo[0],
                'transaction_amount' => ($amount[0] / pow(10, $exponent[0])),
                'currency' => $currency[0]
            );
            $anzvas_data = 'Pay successful via ANZ VAS payment. Status:' . $result[0] . '. TransactionId:' . $transactionId[0] . ' .Transaction amount:' . $anzvaz_response['transaction_amount'] . ' ' . $currency[0];
            if ($dom->xpath('//ns3:message/ns3:transactionResponse/ns4:dccInformation') != false) {
                $anzvaz_response['disclaimer'] = $disclaimer[0];
                if ($dccAccepted[0] == 'true') {
                    $anzvaz_response['transaction_currency'] = $dccCurrency[0];
                    $anzvaz_response['exchange_rate'] = $dccFxRate[0];
                    $anzvaz_response['total_amount_due'] = ($dccAmount[0] / pow(10, $dccExponent[0]));
                    $anzvaz_response['dccCurrency'] = $dccCurrency[0];
                    $anzvas_data.=' . Transaction currency:' . $dccCurrency[0] . ' .Exchange rate:' . $dccFxRate[0] . ' .Total amount due:' . ($dccAmount[0] / pow(10, $dccExponent[0])) . ' ' . $dccCurrency[0];
                }
            }
            $order_number = $receiptNo[0];
            $virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber($order_number);
            $orderModel = VmModel::getModel('orders');
            $order = $orderModel->getOrder($virtuemart_order_id);
            $order['customer_notified'] = 1;
            $order['virtuemart_order_id'] = $virtuemart_order_id;
            $order['order_status'] = 'C';
            $order['comments'] = JText::sprintf('Your order number [%s] was confirmed', $order_number) . '. ' . $anzvas_data;
            $orderModel->updateStatusForOneOrder($virtuemart_order_id, $order, true);
            $cart = VirtueMartCart::getCart();
            $cart->emptyCart();
            $anzvas_data_params = '';
            foreach ($anzvaz_response as $k => $v) {
                $anzvas_data_params.='&' . $k . '=' . $v . '&';
            }
            $url_success = $this->getSuccessUrl($order, $anzvas_data_params);
            $app = JFactory::getApplication();
            $app->redirect($url_success);
        } else {
            $msg = $resultCode[0] . ': ' . $result[0];
            $order_number = $receiptNo[0];
            $virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber($order_number);
            $this->_handlePaymentCancel($virtuemart_order_id, $msg);
        }
    }

    protected function renderPluginName($activeMethod) {
        $plugin_name = $this->_psType . '_name';
        $plugin_desc = $this->_psType . '_desc';
        $logo = '<span><img style="width:120px;" align="middle" src="' . JUri::root() . 'plugins/vmpayment/anzvas/anzvas/assets/images/logo.jpg"  alt="" /></span> ';
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
        $html .= $this->getHtmlRowBE('Payment name', $paymentTable->payment_name);
        $html .= '</table>' . "\n";

        return $html;
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

    private function getSuccessUrl($order, $anzvas_data_params) {
        return JURI::base() . JROUTE::_("index.php?option=com_virtuemart&view=pluginresponse&task=pluginresponsereceived&pm=" . $order['details']['BT']->virtuemart_paymentmethod_id . '&on=' . $order['details']['BT']->order_number . "&Itemid=" . vRequest::getInt('Itemid') . '&lang=' . vRequest::getCmd('lang', '') . $anzvas_data_params, false);
    }

    private function getNotificationUrl() {

        return JURI::base() . JROUTE::_("index.php?option=com_virtuemart&view=pluginresponse&task=pluginnotification", false);
    }

}

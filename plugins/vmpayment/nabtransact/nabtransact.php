<?php

defined('_JEXEC') or die('Restricted access');

/**
 *
 * @package VirtueMart
 * @subpackage payment
 * @copyright Copyright (C) 2004-2008 soeren - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 * http://virtuemart.org
 */
if (!class_exists('Creditcard')) {
    require_once(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'creditcard.php');
}
if (!class_exists('vmPSPlugin')) {
    require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
}

class plgVmPaymentNabtransact extends vmPSPlugin {

    // instance of class
    private $_cc_cardname = '';
    private $_cc_type = '';
    private $_cc_number = '';
    private $_cc_cvv = '';
    private $_cc_expire_month = '';
    private $_cc_expire_year = '';
    private $_cc_valid = false;
    private $_errormessage = array();
    private $_log_file = '';

    function plgVmPaymentNabtransact(& $subject, $config) {
        parent::__construct($subject, $config);

        $this->_loggable = true;
        $this->tableFields = array_keys($this->getTableSQLFields());
        $this->_tablepkey = 'id';
        $this->_tableId = 'id';
        $varsToPush = array(
            'login_id' => array('', 'int'),
            'user' => array('', 'int'),
            'anti' => array('', 'int'),
            'pass' => array('', 'int'),
            'secure_post' => array('', 'int'),
            'testmod' => array('', 'int'),
            'secret' => array('', 'int'),
            'creditcards' => array('', 'int'),
            'payment_approved_status' => array('C', 'char'),
            'payment_declined_status' => array('X', 'char'),
            'payment_held_status' => array('P', 'char'),
            'cost_per_transaction' => array(0, 'int'),
            'cost_percent_total' => array(0, 'char'),
        );
        $this->_log_file = dirname(__FILE__) . '/nabtransactdirect.log';
        $this->setConfigParameterable($this->_configTableFieldName, $varsToPush);
    }

    public function getVmPluginCreateTableSQL() {

        return $this->createTableSQL('Payment Nab Transact 3d Table');
    }

    function getTableSQLFields() {

        $SQLfields = array(
            'id' => ' INT(11) unsigned NOT NULL AUTO_INCREMENT ',
            'virtuemart_order_id' => ' int(1) UNSIGNED DEFAULT NULL',
            'order_number' => ' char(32) DEFAULT NULL',
            'virtuemart_paymentmethod_id' => ' mediumint(1) UNSIGNED DEFAULT NULL',
            'payment_name' => 'varchar(5000)',
            'cost_per_transaction' => ' decimal(10,2) DEFAULT NULL ',
            'cost_percent_total' => ' decimal(10,2) DEFAULT NULL ',
            'nabresponse_raw' => ' varchar(512) DEFAULT NULL'
        );
        return $SQLfields;
    }

    function plgVmDisplayListFEPayment(VirtueMartCart $cart, $selected = 0, &$htmlIn) {
        JHTML::_('behavior.tooltip');

        if ($this->getPluginMethods($cart->vendorId) === 0) {
            if (empty($this->_name)) {
                $app = JFactory::getApplication();

                $app->enqueueMessage(JText::_('COM_VIRTUEMART_CART_NO_' . strtoupper($this->_psType)));

                return false;
            } else {
                return false;
            }
        }
        $method_name = $this->_psType . '_name';

        JHTML::script('vmcreditcard.js', 'components/com_virtuemart/assets/js/', false);
        JFactory::getLanguage()->load('com_virtuemart');
        vmJsApi::jCreditCard();
        $htmla = '';
        $html = array();
        foreach ($this->methods as $method) {
            if ($this->checkConditions($cart, $method, $cart->pricesUnformatted)) {
                $methodSalesPrice = $this->setCartPrices($cart, $cart->pricesUnformatted, $method);
                $method->$method_name = $this->renderPluginName($method);
                $html = $this->getPluginHtml($method, $selected, $methodSalesPrice);
                if ($selected == $method->virtuemart_paymentmethod_id) {
                    $this->_getNabIntoSession();
                } else {
                    $this->_cc_type = '';
                    $this->_cc_number = '';
                    $this->_cc_cvv = '';
                    $this->_cc_expire_month = '';
                    $this->_cc_expire_year = '';
                }

                $creditCards = $method->creditcards;

                $creditCardList = '';
                if ($creditCards) {
                    $creditCardList = ($this->_renderCreditCardList($creditCards, $this->_cc_type, $method->virtuemart_paymentmethod_id, false));
                }
                $sandbox_msg = "";
                if ($method->sandbox) {
                    $sandbox_msg .= '<br />' . JText::_('VMPAYMENT_NAB_SANDBOX_TEST_NUMBERS');
                }

                $cvv_images = $this->_displayCVVImages($method);
                $html .= '<br /><span class="vmpayment_cardinfo">' . JText::_('VMPAYMENT_NAB_COMPLETE_FORM') . $sandbox_msg . '
		    <table border="0" cellspacing="0" cellpadding="2" width="100%">
		    <tr valign="top">
		        <td nowrap width="10%" align="right">
		        	<label for="creditcardtype">' . JText::_('VMPAYMENT_NAB_CCTYPE') . '</label>
		        </td>
		        <td>' . $creditCardList .
                        '</td>
		    </tr>
		    <tr valign="top">
		        <td nowrap width="10%" align="right">
		        	<label for="cc_type">' . JText::_('VMPAYMENT_NAB_CCNAME') . '</label>
		        </td>
		        <td>
		        <input type="text" class="inputbox" id="cc_cardname_' . $method->virtuemart_paymentmethod_id . '" name="cc_cardname_' . $method->virtuemart_paymentmethod_id . '" value="' . $this->_cc_cardname . '"    autocomplete="off"   onchange="ccError=razCCerror(' . $method->virtuemart_paymentmethod_id . ');
		        <div id="cc_cardname' . $method->virtuemart_paymentmethod_id . '"></div>
		    </td>
		    </tr>
		    <tr valign="top">
		        <td nowrap width="10%" align="right">
		        	<label for="cc_type">' . JText::_('VMPAYMENT_NAB_CCNUM') . '</label>
		        </td>
		        <td>
		        <input type="text" class="inputbox" id="cc_number_' . $method->virtuemart_paymentmethod_id . '" name="cc_number_' . $method->virtuemart_paymentmethod_id . '" value="' . $this->_cc_number . '"    autocomplete="off"   onchange="ccError=razCCerror(' . $method->virtuemart_paymentmethod_id . ');
	CheckCreditCardNumber(this . value, ' . $method->virtuemart_paymentmethod_id . ');
	if (!ccError) {
	    this.value=\'\';}" />
		        <div id="cc_cardnumber_errormsg_' . $method->virtuemart_paymentmethod_id . '"></div>
		    </td>
		    </tr>
		    <tr valign="top">
		        <td nowrap width="10%" align="right">
		        	<label for="cc_cvv">' . JText::_('VMPAYMENT_NAB_CVV2') . '</label>
		        </td>
		        <td>
		            <input type="text" class="inputbox" id="cc_cvv_' . $method->virtuemart_paymentmethod_id . '" name="cc_cvv_' . $method->virtuemart_paymentmethod_id . '" maxlength="4" size="5" value="' . $this->_cc_cvv . '" autocomplete="off" />

			<span class="hasTip" title="' . JText::_('VMPAYMENT_NAB_WHATISCVV') . '::' . JText::sprintf("VMPAYMENT_NAB_WHATISCVV_TOOLTIP", $cvv_images) . ' ">' .
                        JText::_('VMPAYMENT_NAB_WHATISCVV') . '
			</span></td>
		    </tr>
		    <tr>
		        <td nowrap width="10%" align="right">' . JText::_('VMPAYMENT_NAB_EXDATE') . '</td>
		        <td> ';
                $html .= shopfunctions::listMonths('cc_expire_month_' . $method->virtuemart_paymentmethod_id, $this->_cc_expire_month);
                $html .= " / ";
                $html .= '
                    <script type="text/javascript">
                    //<![CDATA[
                      function changeDate(id, el)
                       {
                         var month = document.getElementById(\'cc_expire_month_\'+id); if(!CreditCardisExpiryDate(month.value,el.value, id))
                         {el.value=\'\';
                         month.value=\'\';}
                       }
                    //]]>
                    </script>';
                $html .= shopfunctions::listYears('cc_expire_year_' . $method->virtuemart_paymentmethod_id, $this->_cc_expire_year, null, null, " onchange=\"javascript:changeDate(" . $method->virtuemart_paymentmethod_id . ", this);\" ");
                $html .= '<div id="cc_expiredate_errormsg_' . $method->virtuemart_paymentmethod_id . '"></div>';
                $html .= '</td>  </tr>  	</table></span>';

                $htmla[] = $html;
            }
        }
        $htmlIn[] = $htmla;

        return true;
    }

    function _getNabIntoSession() {
        if (!class_exists('vmCrypt')) {
            require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmcrypt.php');
        }
        $session = JFactory::getSession();
        $nabSession = $session->get('nab', 0, 'vm');

        if (!empty($nabSession)) {
            $nabData = unserialize($nabSession);
            $this->_cc_type = $nabData->cc_type;
            $this->_cc_cardname = $nabData->cc_cardname;
            $this->_cc_number = $nabData->cc_number;
            $this->_cc_cvv = $nabData->cc_cvv;
            $this->_cc_expire_month = $nabData->cc_expire_month;
            $this->_cc_expire_year = $nabData->cc_expire_year;
            $this->_cc_valid = $nabData->cc_valid;
        }
    }

    protected function renderPluginName($plugin) {

        $return = '';
        $plugin_name = $this->_psType . '_name';
        $plugin_desc = $this->_psType . '_desc';
        $description = '';
        $logosFieldName = $this->_psType . '_logos';
        $logos = $plugin->$logosFieldName;
        if (!empty($logos)) {
            $return = $this->displayLogos($logos) . ' ';
        }
        if (!empty($plugin->$plugin_desc)) {
            $description = '<span class="' . $this->_type . '_description">' . $plugin->$plugin_desc . '</span>';
        }
        $pluginName = $return . '<span class="' . $this->_type . '_name">' . $plugin->$plugin_name . '</span>' . $description;
        return $pluginName;
    }

    function _renderCreditCardList($creditCards, $selected_cc_type, $paymentmethod_id, $multiple = false, $attrs = '') {

        $idA = $id = 'cc_type_' . $paymentmethod_id;
        if (!is_array($creditCards)) {
            $creditCards = (array) $creditCards;
        }
        foreach ($creditCards as $creditCard) {
            $options[] = JHTML::_('select.option', $creditCard, JText::_('VMPAYMENT_NAB_' . strtoupper($creditCard)));
        }
        if ($multiple) {
            $attrs = 'multiple="multiple"';
            $idA .= '[]';
        }
        return JHTML::_('select.genericlist', $options, $idA, $attrs, 'value', 'text', $selected_cc_type);
    }

    public function _displayCVVImages($method) {
        $cvv_images = $method->cvv_images;
        $img = '';
        if ($cvv_images) {
            $img = $this->displayLogos($cvv_images);
            $img = str_replace('"', "'", $img);
        }
        return $img;
    }

    function plgVmOnCheckoutCheckDataPayment(VirtueMartCart $cart) {

        if (!$this->selectedThisByMethodId($cart->virtuemart_paymentmethod_id)) {
            return null; // Another method was selected, do nothing
        }
        $this->_getNabIntoSession();

        return $this->_validate_creditcard_data(true);
    }

    function _validate_creditcard_data($enqueueMessage = true) {
        static $force = true;
        if (empty($this->_cc_number) and empty($this->_cc_cvv)) {
            return false;
        }
        $html = '';
        $this->_cc_valid = !empty($this->_cc_number) and ! empty($this->_cc_cvv) and ! empty($this->_cc_expire_month) and ! empty($this->_cc_expire_year);

        if (!empty($this->_cc_number) and ! Creditcard::validate_credit_card_number($this->_cc_type, $this->_cc_number)) {
            $this->_errormessage[] = 'VMPAYMENT_NAB_CARD_NUMBER_INVALID';
            $this->_cc_valid = FALSE;
        }

        if (!Creditcard::validate_credit_card_cvv($this->_cc_type, $this->_cc_cvv, true, $this->_cc_number)) {
            $this->_errormessage[] = 'VMPAYMENT_NAB_CARD_CVV_INVALID';
            $this->_cc_valid = FALSE;
        }
        if (!Creditcard::validate_credit_card_date($this->_cc_type, $this->_cc_expire_month, $this->_cc_expire_year)) {
            $this->_errormessage[] = 'VMPAYMENT_NAB_CARD_EXPIRATION_DATE_INVALID';
            $this->_cc_valid = FALSE;
        }
        if (!$this->_cc_valid) {
            foreach ($this->_errormessage as $msg) {
                $html .= vmText::_($msg) . "<br/>";
            }
        }
        if (!$this->_cc_valid && $enqueueMessage && $force) {
            $app = JFactory::getApplication();
            $app->enqueueMessage($html);
            $force = false;
        }

        return $this->_cc_valid;
    }

    function _setNabIntoSession() {

        $session = JFactory::getSession();
        $sessionNab = new stdClass();
        // card information
        $sessionNab->cc_type = $this->_cc_type;
        $sessionNab->cc_cardname = $this->_cc_cardname;
        $sessionNab->cc_number = $this->_cc_number;
        $sessionNab->cc_cvv = $this->_cc_cvv;
        $sessionNab->cc_expire_month = $this->_cc_expire_month;
        $sessionNab->cc_expire_year = $this->_cc_expire_year;
        $sessionNab->cc_valid = $this->_cc_valid;
        $session->set('nab', serialize($sessionNab), 'vm');
    }

    public function plgVmOnSelectCheckPayment(VirtueMartCart $cart) {

        if (!$this->selectedThisByMethodId($cart->virtuemart_paymentmethod_id)) {
            return $this->OnSelectCheck($cart); // Another method was selected, do nothing
        }

        $this->_cc_type = JRequest::getVar('cc_type_' . $cart->virtuemart_paymentmethod_id, '');
        $this->_cc_cardname = JRequest::getVar('cc_cardname_' . $cart->virtuemart_paymentmethod_id, '');
        $this->_cc_number = str_replace(" ", "", JRequest::getVar('cc_number_' . $cart->virtuemart_paymentmethod_id, ''));
        $this->_cc_cvv = JRequest::getVar('cc_cvv_' . $cart->virtuemart_paymentmethod_id, '');
        $this->_cc_expire_month = JRequest::getVar('cc_expire_month_' . $cart->virtuemart_paymentmethod_id, '');
        $this->_cc_expire_year = JRequest::getVar('cc_expire_year_' . $cart->virtuemart_paymentmethod_id, '');

        if (!$this->_validate_creditcard_data(true)) {
            return false; // returns string containing errors
        }
        $this->_setNabIntoSession();
        return true;
    }

    function _clearNabSession() {

        $session = JFactory::getSession();
        $session->clear('nab', 'vm');
    }

    function generateFingerprint($dataFingerprint) {

        $finger_print = sha1($dataFingerprint['EPS_MERCHANT'] . '|' . $dataFingerprint['EPS_PASSWORD'] . '|' . $dataFingerprint['EPS_REFERENCEID'] . '|' . $dataFingerprint['EPS_AMOUNT'] . '|' . $dataFingerprint['EPS_TIMESTAMP']);

        return $finger_print;
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

        $method = $this->_currentMethod;
        $this->getPaymentCurrency($method);
        $user = JFactory::getUser();
        $creditCards = $this->_cc_number;
        $month = $this->_cc_expire_month;
        $year = $this->_cc_expire_year;
        $cvv = $this->_cc_cvv;

        $login = $this->_vmpCtable->login_id;
        $pass = $this->_vmpCtable->pass;
        $anti = $this->_vmpCtable->anti;
        $testmod = $this->_vmpCtable->testmod;


        $amount = number_format($order['details']['BT']->order_total, 2, '', '');
        $ordernumber = $order['details']['BT']->order_number;
        $FirstName = $order['details']['BT']->first_name;
        $LastName = $order['details']['BT']->last_name;
        $email1 = $order['details']['BT']->email;
        $zip = $order['details']['BT']->zip;
        $city = $order['details']['BT']->city;
        $q = 'SELECT `country_name` FROM `#__virtuemart_countries` WHERE `virtuemart_country_id`="' . $order['details']['BT']->virtuemart_country_id . '" ';
        $db = JFactory::getDbo();
        $db->setQuery($q);
        $country = $db->loadResult();
        $Parameters["EPS_CARDNUMBER"] = $creditCards;
        $Parameters["EPS_MERCHANT"] = $login;
        $Parameters["EPS_PASSWORD"] = $pass;
        $Parameters["EPS_AMOUNT"] = $amount;
        $Parameters["EPS_REFERENCEID"] = $ordernumber;

        $Parameters["EPS_CARDTYPE"] = $this->_cc_type;
        $Parameters["EPS_FIRSTNAME"] = $FirstName;
        $Parameters["EPS_LASTNAME"] = $LastName;
        $Parameters["EPS_ZIPCODE"] = $zip;
        $Parameters["EPS_TOWN"] = $city;
        $Parameters["EPS_BILLINGCOUNTRY"] = $country;
        $Parameters["EPS_DELIVERYCOUNTRY"] = $country;
        $Parameters["EPS_EMAILADDRESS"] = $email1;

        $Parameters["EPS_EXPIRYMONTH"] = $month;
        $Parameters["EPS_EXPIRYYEAR"] = $year;
        $Parameters["EPS_TIMESTAMP"] = gmdate("YmdHis");
        $Parameters["EPS_TXNTYPE"] = 'PAYMENT';

        if ($anti == '1') {
            $Parameters["EPS_TXNTYPE"] = 'ANTIFRAUD';
            $Parameters["EPS_3DSECURE"] = 'true';
            $Parameters["3D_XID"] = substr(strtoupper(md5(time())), 0, 20);
            $Parameters["EPS_MERCHANTNUM"] = $user->id;
            $Parameters["EPS_IP"] = $_SERVER['REMOTE_ADDR'];
        }


        if ($testmod == '1') {
            $finurl = "https://transact.nab.com.au/test/directpost/genfingerprint";
            if ($anti == '1') {
                $posturl = "https://transact.nab.com.au/test/directpost/authorise3d";
            } else {
                $posturl = "https://transact.nab.com.au/test/directpost/authorise";
            }
        } else {
            $finurl = "https://transact.nab.com.au/live/directpost/genfingerprint";
            if ($anti == '1') {
                $posturl = "https://transact.nab.com.au/live/directpost/authorise3d";
            } else {
                $posturl = "https://transact.nab.com.au/live/directpost/authorise";
            }
        }

        $fintparams = array(
            "EPS_MERCHANT" => $login,
            "EPS_PASSWORD" => $pass,
            "EPS_AMOUNT" => $amount,
            "EPS_REFERENCEID" => $ordernumber,
            "EPS_TIMESTAMP" => gmdate("YmdHis")
        );
        $fingerprint = $this->generateFingerprint($fintparams);

        $Parameters["EPS_RESULTURL"] = JUri::base() . 'index.php?' . 'option=com_virtuemart&view=vmplg&task=notify&tmpl=component&pm=' . $order['details']['BT']->virtuemart_paymentmethod_id . '&hash=' . sha1($order['details']['BT']->order_number);
        $Parameters["EPS_AMOUNT"] = $amount;

        if ($anti == '1') {
            $str = "EPS_MERCHANT=" . urlencode($Parameters["EPS_MERCHANT"]) . '&' . "EPS_PASSWORD=" .
                    urlencode($Parameters["EPS_PASSWORD"]) . '&' . "EPS_AMOUNT=" .
                    urlencode($Parameters["EPS_AMOUNT"]) . '&' . "EPS_REFERENCEID=" .
                    urlencode($Parameters["EPS_REFERENCEID"]) . '&' . "EPS_TIMESTAMP=" .
                    urlencode($Parameters["EPS_TIMESTAMP"]) . '&' . "EPS_FINGERPRINT=" .
                    urlencode($fingerprint) . '&' . "EPS_RESULTURL=" . $Parameters["EPS_RESULTURL"] . '&' . "EPS_CARDNUMBER=" .
                    urlencode($creditCards) . '&' . "EPS_EXPIRYMONTH=" .
                    urlencode($month) . '&' . "EPS_EXPIRYYEAR=" .
                    urlencode($year) . '&' . "EPS_CCV=" .
                    urlencode($cvv) . '&' . "EPS_CARDTYPE=" .
                    urlencode($Parameters["EPS_CARDTYPE"]) . '&' . "EPS_TXNTYPE=" .
                    urlencode($Parameters["EPS_TXNTYPE"]) . '&' . "EPS_3DSECURE=" . 'true' . '&' . "3D_XID=" .
                    urlencode($Parameters["3D_XID"]) . '&' . "EPS_MERCHANTNUM=" .
                    urlencode($Parameters["EPS_MERCHANTNUM"]) . '&' . "EPS_IP=" .
                    urlencode($Parameters["EPS_IP"]) . '&' . "EPS_FIRSTNAME=" .
                    urlencode($Parameters["EPS_FIRSTNAME"]) . '&' . "EPS_LASTNAME=" .
                    urlencode($Parameters["EPS_LASTNAME"]) . '&' . "EPS_ZIPCODE=" .
                    urlencode($Parameters["EPS_ZIPCODE"]) . '&' . "EPS_TOWN=" .
                    urlencode($Parameters["EPS_TOWN"]) . '&' . "EPS_BILLINGCOUNTRY=" .
                    urlencode($Parameters["EPS_BILLINGCOUNTRY"]) . '&' . "EPS_DELIVERYCOUNTRY=" .
                    urlencode($Parameters["EPS_DELIVERYCOUNTRY"]) . '&' . "EPS_EMAILADDRESS=" .
                    urlencode($Parameters["EPS_EMAILADDRESS"]);
        } else {
            $str = "EPS_MERCHANT=" . urlencode($Parameters["EPS_MERCHANT"]) . '&' . "EPS_PASSWORD=" .
                    urlencode($Parameters["EPS_PASSWORD"]) . '&' . "EPS_AMOUNT=" .
                    urlencode($Parameters["EPS_AMOUNT"]) . '&' . "EPS_REFERENCEID=" .
                    urlencode($Parameters["EPS_REFERENCEID"]) . '&' . "EPS_TIMESTAMP=" .
                    urlencode($Parameters["EPS_TIMESTAMP"]) . '&' . "EPS_FINGERPRINT=" .
                    urlencode($fingerprint) . '&' . "EPS_RESULTURL=" . $Parameters["EPS_RESULTURL"] . '&' . "EPS_CARDNUMBER=" .
                    urlencode($creditCards) . '&' . "EPS_EXPIRYMONTH=" .
                    urlencode($month) . '&' . "EPS_EXPIRYYEAR=" .
                    urlencode($year) . '&' . "EPS_CCV=" .
                    urlencode($cvv) . '&' . "EPS_CARDTYPE=" .
                    urlencode($Parameters["EPS_CARDTYPE"]) . '&' . "EPS_TXNTYPE=" .
                    urlencode($Parameters["EPS_TXNTYPE"]) . '&' . "EPS_FIRSTNAME=" .
                    urlencode($Parameters["EPS_FIRSTNAME"]) . '&' . "EPS_LASTNAME=" .
                    urlencode($Parameters["EPS_LASTNAME"]) . '&' . "EPS_ZIPCODE=" .
                    urlencode($Parameters["EPS_ZIPCODE"]) . '&' . "EPS_TOWN=" .
                    urlencode($Parameters["EPS_TOWN"]) . '&' . "EPS_BILLINGCOUNTRY=" .
                    urlencode($Parameters["EPS_BILLINGCOUNTRY"]) . '&' . "EPS_DELIVERYCOUNTRY=" .
                    urlencode($Parameters["EPS_DELIVERYCOUNTRY"]) . '&' . "EPS_EMAILADDRESS=" .
                    urlencode($Parameters["EPS_EMAILADDRESS"]);
        }
        file_put_contents($this->_log_file, date('Y-m-d H:i:s') . "\n Request to NAB \n" . $str . "\n", FILE_APPEND);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $posturl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($result != '') {
            $this->redirectToCart($result);
        } else {
            //success transaction
            $db = JFactory::getDBO();
            require_once JPATH_BASE . "/configuration.php";
            $config = new JConfig;
            $dbprefix = $config->dbprefix;

            $q = 'SELECT * FROM `' . $dbprefix . 'virtuemart_payment_plg_nabtransact` '
                    . 'WHERE `virtuemart_order_id` = ' . $order['details']['BT']->virtuemart_order_id;
            $db->setQuery($q);
            $paymentTable = $db->loadObject();
            $nab_data = json_decode($paymentTable->nabresponse_raw, true);
            $restext = $nab_data["restext"];
            $txnid = $nab_data["txnid"];
            if ($restext == 'Approved') {
                $html = '<table width="85%" align="center" cellpadding="5" border="1">
					<tr class="shade">
						<td width="35%" align="right"><strong><i>Transaction ID: </i></strong></td>
						<td>' . $txnid . '</td>
					</tr>
					<tr>
						<td align="right"><strong><i>Reference ID : </i></strong></td>
						<td>' . $nab_data["refid"] . '</td>
					</tr>
					<tr>
						<td align="right"><strong><i>Status : </i></strong></td>
						<td>' . $restext . '</td>
					</tr>
					<tr>
						<td align="right"><strong><i>Date : </i></strong></td>
						<td>' . date("m/d/Y", strtotime($nab_data["settdate"])) . '</td>
					</tr>
			</table>';
                $this->_clearNabSession();
                $cart->emptyCart();
                vRequest::setVar('html', $html);
            } else {
                $this->redirectToCart('Payment declined, please try again or contact us. Message: ' . $restext . ". Transaction:" . $txnid);
            }
        }
        return TRUE;
    }

    /**
     * @param null $msg
     */
    function redirectToCart($msg = NULL) {
        $app = JFactory::getApplication();
        $app->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&Itemid=' . vRequest::getInt('Itemid'), false), $msg, 'error');
    }

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
        return TRUE;
    }

    function plgVmOnPaymentNotification() {
        $request_data = $_REQUEST;

        $nab_data = array(
            'refid' => $request_data['refid'],
            'rescode' => $request_data['rescode'],
            'restext' => $request_data['restext'],
            'txnid' => $request_data['txnid'],
            'settdate' => $request_data['settdate'],
            'sig' => $request_data['sig'],
            'pan' => $request_data['pan'],
            'expirydate' => $request_data['expirydate'],
            'callback_status_code' => $request_data['callback_status_code']
        );
        file_put_contents($this->_log_file, date('Y-m-d H:i:s') . "\n Response from NAB \n" . json_encode($request_data) . "\n", FILE_APPEND);
        $virtuemart_paymentmethod_id = JRequest::getInt('pm', 0);

        if (!($method = $this->getVmPluginMethod($virtuemart_paymentmethod_id))) {
            return null;
        }
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

        if (!isset($nab_data['refid'])) {
            return;
        }
        $order_number = $nab_data['refid'];
        $virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber($order_number);
        if (!$virtuemart_order_id) {
            return;
        }

        $payment = $this->getDataByOrderId($virtuemart_order_id);
        $method = $this->getVmPluginMethod($payment->virtuemart_paymentmethod_id);
        if (!$this->selectedThisElement($method->payment_element)) {
            return false;
        }
        $this->_storeNabInternalData($method, $nab_data, $virtuemart_order_id);
        if ($nab_data['restext'] == 'Approved' && $nab_data['sig'] != '' && $request_data['hash'] == sha1($order_number)) {
            $orderModel = VmModel::getModel('orders');
            $order = $orderModel->getOrder($virtuemart_order_id);
            $order['customer_notified'] = 1;
            $order['virtuemart_order_id'] = $virtuemart_order_id;
            $order['order_status'] = $method->payment_approved_status;
            $order['comments'] = JText::sprintf('Your order number [%s] was confirmed', $order_number) . '. Rescode:' . $nab_data['rescode'] . ', Status:' . $nab_data['restext'] . ', Transaction:' . $nab_data['txnid'];
            $orderModel->updateStatusForOneOrder($virtuemart_order_id, $order, true);
            $cart = VirtueMartCart::getCart();
            $cart->emptyCart();
        }
        exit();
    }

    function _storeNabInternalData($method, $nab_data, $virtuemart_order_id) {

        // get all know columns of the table
        $db = JFactory::getDBO();
        $query = 'SHOW COLUMNS FROM `' . $this->_tablename . '` ';
        $db->setQuery($query);
        $columns = $db->loadResultArray(0);
        foreach ($nab_data as $key => $value) {
            $table_key = 'nab_response_' . $key;
            if (@in_array($table_key, $columns)) {
                $response_fields[$table_key] = $value;
            }
        }
        $payment_name = $method->payment_name;
        $response_fields['order_number'] = $nab_data['refid'];
        $response_fields['payment_name'] = $payment_name;
        $response_fields['virtuemart_paymentmethod_id'] = $method->virtuemart_paymentmethod_id;
        $response_fields['nabresponse_raw'] = json_encode($nab_data);
        $response_fields['virtuemart_order_id'] = $virtuemart_order_id;
        $this->storePSPluginInternalData($response_fields, 'virtuemart_order_id', true);
    }

    function plgVmOnShowOrderBEPayment($virtuemart_order_id, $payment_method_id) {

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

    function plgVmOnStoreInstallPaymentPluginTable($jplugin_id) {
        if ($jplugin_id != $this->_jid) {
            return FALSE;
        }
        return $this->onStoreInstallPluginTable($jplugin_id);
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

    function plgVmSetOnTablePluginParamsPayment($name, $id, &$table) {
        return $this->setOnTablePluginParams($name, $id, $table);
    }

    function plgVmDeclarePluginParamsPaymentVM3(&$data) {
        return $this->declarePluginParams('payment', $data);
    }

    private function getNotificationUrl() {
        return JURI::base() . JROUTE::_("index.php?option=com_virtuemart&view=pluginresponse&task=pluginnotification", false);
    }

    private function getSuccessUrl($order, $anzvas_data_params) {
        return JURI::base() . JROUTE::_("index.php?option=com_virtuemart&view=pluginresponse&task=pluginresponsereceived&pm=" . $order['details']['BT']->virtuemart_paymentmethod_id . '&on=' . $order['details']['BT']->order_number . "&Itemid=" . vRequest::getInt('Itemid') . '&lang=' . vRequest::getCmd('lang', '') . $anzvas_data_params, false);
    }

}

<?php

if (!defined('ABSPATH')) {
    exit;
}

class COMMWEB_HOSTED_API {

    public $log;
    public $_live_url = "https://paymentgateway.commbank.com.au/api/nvp/version/36";
    public $_checkout_url_js = 'https://paymentgateway.commbank.com.au/checkout/version/36/checkout.js';
    public $option = null;

    function __construct($feed) {
        $this->_live_url = "https://paymentgateway.commbank.com.au/api/nvp/version/36";
        $this->_checkout_url_js = 'https://paymentgateway.commbank.com.au/checkout/version/36/checkout.js';
        $this->option = $feed;
    }

    public function log($message) {
        if ($message) {
            file_put_contents(dirname(dirname(dirname(__FILE__))) . "/log_payment.txt", print_r($message, true), FILE_APPEND);
        }
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log($message);
        }
        return true;
    }

    public function getSetting() {
        return $this->option;
    }

    public function getMerchantId() {
        $option = $this->getSetting();
        return isset($option['commweb_merchant_id']) ? $option['commweb_merchant_id'] : "";
    }

    public function getApiPassword() {
        $option = $this->getSetting();
        return isset($option['commweb_api_password']) ? $option['commweb_api_password'] : '';
    }

    //commweb_checkout_3d_source
    public function getCommwebAllow3DSource() {
        $option = $this->getSetting();
        return isset($option['commweb_checkout_3d_source']) ? $option['commweb_checkout_3d_source'] : '';
    }

    public function getCommwebTitle() {
        $option = $this->getSetting();
        return isset($option['commweb_title']) ? $option['commweb_title'] : '';
    }

    //commweb_checkout_method
    public function getCommwebCheckoutMethod() {
        $option = $this->getSetting();
        return isset($option['commweb_checkout_method']) ? $option['commweb_checkout_method'] : '';
    }

    public function getApiUsername() {
        $merchant_id = $this->getMerchantId();
        return 'merchant.' . $merchant_id;
    }

    public function getDebug() {
        $option = $this->getSetting();
        return isset($option['commweb_checkout_bug_log']) ? $option['commweb_checkout_bug_log'] : '';
    }

    /**
     * Process Payment
     * @param type $amount
     * @param type $id_for_commweb
     * @return boolean|string
     */
    public function getCheckoutSession($amount, $id_for_commweb,$entry_id) {
        if(!session_id()){
            session_start();
        }
        if (!$amount) {
            return false;
        }
        $amount = number_format($amount, 2, '.', '');
        $merchant = $this->getMerchantId();
        $apiPassword = $this->getApiPassword();
        $url = $this->_live_url;
        $fields = array(
            'apiOperation' => urlencode('CREATE_CHECKOUT_SESSION'),
            'apiPassword' => urlencode($apiPassword),
            'apiUsername' => urlencode($this->getApiUsername()),
            'merchant' => urlencode($merchant),
            'order.id' => urlencode($id_for_commweb),
            'order.amount' => urlencode($amount),
            'order.currency' => urlencode('AUD')
        );
        $fields_string = '';
        if ($this->getDebug() == 'yes') {
            $this->log('Checkout session request: ' . print_r($fields, true));
        }
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($result != '') {
            $arr_session_id = null;
            parse_str(html_entity_decode($result), $arr_session_id);
            if ($this->getDebug() == 'yes') {
                $this->log('Checkout session response: ' . print_r($arr_session_id, true));
            }
            if (isset($arr_session_id['result']) && $arr_session_id['result'] == 'ERROR') {
                $session_id = '';
            } else {
                $session_id = $arr_session_id['session_id'];
                $_SESSION['SuccessIndicator'] = $arr_session_id['successIndicator'];
                $_SESSION['CurrentOrderId'] = $entry_id;
            }
        } else {
            $this->log('Error: ' . print_r($error, true));
            $session_id = '';
        }
        return $session_id;
    }

    /**
     * Get Result Payment
     * @param type $id_for_commweb
     * @return type
     */
    public function getOrderCommwebDetail($id_for_commweb) {
        $url = $this->_live_url;
        $fields = array(
            'apiOperation' => urlencode('RETRIEVE_ORDER'),
            'apiPassword' => urlencode($this->getApiPassword()),
            'apiUsername' => urlencode($this->getApiUsername()),
            'merchant' => urlencode($this->getMerchantId()),
            'order.id' => urlencode($id_for_commweb)
        );
        $fields_string = '';
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = str_replace("%5B0%5D", '', $result);
        $output = null;
        parse_str(html_entity_decode($result), $output);
        if ($this->getDebug() == 'yes') {
            $this->log('Order detail from commweb: ' . print_r($output, true));
        }
        return $output;
    }

}

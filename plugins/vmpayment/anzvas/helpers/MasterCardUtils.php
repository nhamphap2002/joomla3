<?php

class MasterCardUtilities {

    public $locale;
    public $companyId;
    public $callbackUrl;
    public $stigApiUrl;
    public $sharedKey;

    function __construct($locale, $companyId, $callbackUrl, $stigApiUrl, $sharedKey) {
        $this->locale = $locale;
        $this->companyId = $companyId;
        $this->callbackUrl = $callbackUrl;
        $this->stigApiUrl = $stigApiUrl;
        $this->sharedKey = $sharedKey;
    }

    public function MakeTransactionRequest($transactionType, $referenceNumber, $messageReference, $currency, $paymentAmount, $description) {
        //Transaction Request XML
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->xmlStandalone = true;

        $root = $doc->createElementNS('http://dcc.migs.mastercard.com/stig/api/transaction/message', 'ns3:message');
        $doc->appendChild($root);
        $root->setAttribute('xmlns', 'http://dcc.migs.mastercard.com/stig/api/transaction/request');
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:ns2', 'http://dcc.migs.mastercard.com/stig/api/definitions');
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:ns4', 'http://dcc.migs.mastercard.com/stig/api/transaction/response');
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:ns5', 'http://dcc.migs.mastercard.com/stig/api/transaction/query');
        $root->setAttribute('version', '1.0');

        $tr = $doc->createElementNS('http://dcc.migs.mastercard.com/stig/api/transaction/message', 'ns3:transactionRequest');
        $root->appendChild($tr);

        $tr->setAttribute('locale', $this->locale);
        $tr->setAttribute('serverHostedTransaction', 'true');
        $tr->setAttribute('transType', $transactionType);

        $tr->appendChild($doc->createElement('companyId', $this->companyId));
        $tr->appendChild($doc->createElement('referenceNumber', $referenceNumber));
        $tr->appendChild($doc->createElement('messageReference', $messageReference));

        $amount = $doc->createElement('saleAmount');
        $tr->appendChild($amount);

        $amount->appendChild($doc->createElementNS('http://dcc.migs.mastercard.com/stig/api/definitions', 'ns2:currency', $currency));
        $amount->appendChild($doc->createElementNS('http://dcc.migs.mastercard.com/stig/api/definitions', 'ns2:amount', $paymentAmount));

        $tr->appendChild($doc->createElement('purchaseDescription', $description));
        $tr->appendChild($doc->createElement('redirectUrl', $this->callbackUrl));

        // Pass the message to STIG and return the response
        return $this->getStigResponse($doc->saveXML());
    }

    public function MakeTransactionQuery($token) {
        //Transaction Query XML
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->xmlStandalone = true;

        $root = $doc->createElementNS('http://dcc.migs.mastercard.com/stig/api/transaction/message', 'ns3:message');
        $doc->appendChild($root);
        $root->setAttribute('xmlns', 'http://dcc.migs.mastercard.com/stig/api/transaction/request');
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:ns2', 'http://dcc.migs.mastercard.com/stig/api/definitions');
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:ns4', 'http://dcc.migs.mastercard.com/stig/api/transaction/response');
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:ns5', 'http://dcc.migs.mastercard.com/stig/api/transaction/query');
        $root->setAttribute('version', '1.0');

        $tq = $doc->createElementNS('http://dcc.migs.mastercard.com/stig/api/transaction/message', 'ns3:transactionQuery');
        $root->appendChild($tq);

        $tq->appendChild($doc->createElementNS('http://dcc.migs.mastercard.com/stig/api/transaction/query', 'ns5:companyId', $this->companyId));
        $tq->appendChild($doc->createElementNS('http://dcc.migs.mastercard.com/stig/api/transaction/query', 'ns5:transactionId', TransactionStore::GetTransaction($token)));

        // Pass the message to STIG and return the response
        return $this->getStigResponse($doc->saveXML());
    }

    public function createDigest($xml) {
        $saltedMessage = $this->sharedKey . $xml;

        return base64_encode(hash('sha256', $saltedMessage, true));
    }

    // Sends an authenticated message to STIG and returns the XML response
    public function getStigResponse($message) {
        $request = curl_init($this->stigApiUrl);

        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLINFO_HEADER_OUT, true);
        curl_setopt($request, CURLOPT_TIMEOUT, 3600);
        curl_setopt($request, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($request, CURLOPT_POSTFIELDS, $message);

        curl_setopt($request, CURLOPT_HTTPHEADER, array(
            'Content-Type: text/xml; charset=UTF-8',
            'STIG-Digest: ' . $this->createDigest($message)
        ));

        $response = curl_exec($request);

        curl_close($request);

        // perform optional digest check on response here

        return simplexml_load_string($response);
    }

}

// Replace these methods with integration code to your database or shopping cart software
class TransactionStore {

    // Store the MasterCard transactionId and token with your transaction details
    public static function Add($token, $transactionId) {
        setcookie('transactionStore', base64_encode(serialize(array(strval($token) => intval($transactionId)))));
    }

    // Retrieve the right transaction based on the Mastercard token
    public static function GetTransaction($token) {
        $store = unserialize(base64_decode($_COOKIE['transactionStore']));
        return $store[$token];
    }

}

?>
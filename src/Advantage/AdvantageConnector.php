<?php

namespace WorldNewsGroup\Advantage;

use GuzzleHttp\Client;

class AdvantageConnector {
    private static $credentials;
    private static $endpoint;

    /**
     * Sets up the AdvantageConnector
     * 
     * @param String $api_key - the X-Api-Key to use
     * @param String $endpoint - the URL endpoint
     */
    public static function configure($api_key, $endpoint) {
        self::$credentials = $api_key;
        self::$endpoint = $endpoint;
    }

    public static function getSubscriptionsBydate($date = null, $type = 'created') {
        if( is_null($date) ) $date = date('Y-m-d');

        return self::internalAdvantageCall('/advantage/subscription/bydate/' . $date . '/' . $type);
    }

    public static function getCustomer(String $customer_number) {
        return self::internalAdvantageCall('/advantage/customer/' . $customer_number);
    }

    public static function getCustomerPassword(String $customer_number) {
        return self::internalAdvantageCall('/advantage/user/' . $customer_number);
    }

    public static function getSubscriptions(String $customer_number, String $publication_code = '*', $offset = 0, $count = 100, $active = false) {
        return self::internalAdvantageCall('/advantage/subscription/' . urlencode($publication_code) . '/' . $customer_number
            . '?offset=' . $offset . '&count=' . $count . '&active=' . ($active?'true':'false'));
    }

    public static function getCustomerByEmail(String $email) {
        return self::internalAdvantageCall('/advantage/user/email/' . $email);
    }

    public static function getSubscriptionsForCustomer($customer_number, $publication_code = 'WNG') {
        return self::internalAdvantageCall('/advantage/subscription/' . $publication_code . '/' . $customer_number);
    }

    public static function changeAddressCode($customer_number, $address_code) {
        $params = [
            'AddressCode'=>$address_code,
            'CustomerNumber'=>$customer_number
        ];

        return self::internalAdvantageCall('/advantage/subscription/adjust-bill-to', "POST", $params);
    }

    /**
     * Performs the internal call to the API layer
     * 
     * @param String $request
     * @param String $method - defaults to 'GET'
     * @param Array $params - defaults to an empty array
     * 
     * @return Array|\Exception
     */
    protected static function internalAdvantageCall(String $request, String $method = "GET", Array $params = []): Array|\Exception {
        if( empty(self::$credentials) || empty(self::$endpoint) ) {
            throw new \Exception('Please run AdvantageConnector::configure() first before attempting to make calls.');
        }

        $http = new Client();

        $result = $http->request($method, self::$endpoint . $request, [
            'headers'=>[
                'X-Api-Key'=>self::$credentials
            ],
            \GuzzleHttp\RequestOptions::JSON=>$params
        ]);

        return json_decode($result->getBody(), true);
    }
}
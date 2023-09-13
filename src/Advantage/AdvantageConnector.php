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

    /**
     * Returns subscriptions by a particular record date.  Type can be one of 
     *  - created: when subscription was created
     *  - expiration: when the subscription would expire
     *  - started: when the subscription term starts
     * 
     * @param String $date if null, today's date, else date in 'Y-m-d' format
     * @param String $type one of 'created', 'expiration' or 'started'
     * 
     * @return Array
     */
    public static function getSubscriptionsBydate($date = null, $type = 'created'): Array {
        if( is_null($date) ) $date = date('Y-m-d');

        return self::internalAdvantageCall('/advantage/subscription/bydate/' . $date . '/' . $type);
    }

    /**
     * Returns an Advantage customer record (raw) if found.
     * 
     * @param String $customer_number
     * @return Array
     */
    public static function getCustomer(String $customer_number): Array {
        return self::internalAdvantageCall('/advantage/customer/' . $customer_number);
    }

    /**
     * Returns a list of customers
     */
    public static function getCustomers($offset = 0, $count = 50, $order_by_direction = "ASC", $order_by_field = null) {
        $query = ['order_direction'=>$order_by_direction];
        if( !is_null($order_by_field) ) $query['order_by'] = $order_by_field;

        return self::internalAdvantageCall('/advantage/customers/' . $offset . '/' . $count . '?' . http_build_query($query));
    }

    /**
     * Returns an Advantage CVI user record by customer number
     */
    public static function getUser($customer_number) {
        return self::internalAdvantageCall('/advantage/user/' . $customer_number);
    }

    /**
     * Returns an Advantage CVI user record by email address
     */
    public static function getUserByEmail($email) {
        return self::internalAdvantageCall('/advantage/user/email/' . $email);
    }

    /**
     * Returns Advantage CVI user records
     */
    public static function getUsers($offset = 0, $count = 50, $order_by_direction = "ASC", $order_by_field = null) {
        $query = ['order_direction'=>$order_by_direction];
        if( !is_null($order_by_field) ) $query['order_by'] = $order_by_field;

        return self::internalAdvantageCall('/advantage/users/' . $count . '/' . $offset . '?' . http_build_query($query));
    }

    /**
     * Returns a user's plaintext password.  I suppose security wasn't a priority.
     */
    public static function getCustomerPassword(String $customer_number) {
        return self::internalAdvantageCall('/advantage/user/' . $customer_number);
    }

    /**
     * Returns subscriptions for a customer
     */
    public static function getSubscriptions(String $customer_number, String $publication_code = '*', $offset = 0, $count = 100, $active = false) {
        return self::internalAdvantageCall('/advantage/subscription/' . urlencode($publication_code) . '/' . $customer_number
            . '?offset=' . $offset . '&count=' . $count . '&active=' . ($active?'true':'false'));
    }

    /**
     * Returns a user by email.
     * 
     * @deprecated
     */
    public static function getCustomerByEmail(String $email) {
        return AdvantageConnector::getUserByEmail($email);
    }

    /**
     * Returns subscriptions for a customer
     */
    public static function getSubscriptionsForCustomer($customer_number, $publication_code = 'WNG') {
        return self::internalAdvantageCall('/advantage/subscription/' . $publication_code . '/' . $customer_number);
    }

    /**
     * Changes an address code.
     * No longer used, as that didn't work the way it was supposed to.
     * 
     * @deprecated
     */
    public static function changeAddressCode($customer_number, $address_code) {
        $params = [
            'AddressCode'=>$address_code,
            'CustomerNumber'=>$customer_number
        ];

        return self::internalAdvantageCall('/advantage/subscription/adjust-bill-to', "POST", $params);
    }

    /**
     * Get a coupon
     */
    public static function couponGet($name) {
        return self::internalAdvantageCall('/advantage/coupon/' . $name);
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

        $arr = json_decode((string)$result->getBody(), true);

        if( $arr === null ) {
            return [];
        }
        else {
            return $arr;
        }
    }
}
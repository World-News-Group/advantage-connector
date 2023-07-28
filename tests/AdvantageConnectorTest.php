<?php

require(__DIR__ . '/../vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use WorldNewsGroup\Advantage\AdvantageConnector;
use Dotenv\Dotenv;

final class AdvantageConnectorTest extends TestCase {
    protected function setup(): void {
        Dotenv::createMutable(__DIR__ . '/../')->load();
        AdvantageConnector::configure($_ENV['AC_KEY'], $_ENV['AC_ENDPOINT']);
    }

    public function testGetCustomer() {
        $result = AdvantageConnector::getCustomer($_ENV['TEST_CUSTOMER']);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('CTM_NBR', $result);
        $this->assertSame($result['CTM_NBR'], $_ENV['TEST_CUSTOMER']);
    }

    public function testGetCustomerGetSubscriptionsByDate() {
        $result = AdvantageConnector::getSubscriptionsByDate('2023-06-06');

        $this->assertIsArray($result);
        $this->assertGreaterThan(0, $result['count']);
        $this->assertLessThanOrEqual($result['count'], $result['returned']);
        $this->assertCount($result['returned'], $result['items']);
    }

    public function testGetCustomerPassword() {
        $result = AdvantageConnector::getCustomerPassword($_ENV['TEST_CUSTOMER']);

        $this->assertIsArray($result);
        $this->assertSame($result['CTM_NBR'], $_ENV['TEST_CUSTOMER']);
        $this->assertArrayHasKey('CVI_NBR', $result);
        $this->assertArrayHasKey('PASS_WD', $result);
    }

    public function testGetSubscriptions() {
        $result = AdvantageConnector::getSubscriptions($_ENV['TEST_CUSTOMER']);

        $this->assertIsArray($result);
//String $customer_number, String $publication_code = '*', $offset = 0, $count = 100, $active = false
    }

    public function testGetCustomerByEmail() {
        $result = AdvantageConnector::getCustomerPassword($_ENV['TEST_CUSTOMER']);

        //$byEmail = AdvantageConnector::getCustomerByEmail($result['AUTH_VAL']);
        $byEmail = AdvantageConnector::getUserByEmail($result['AUTH_VAL']);

        $this->assertSame($result, $byEmail);
    }

    public function testGetSubscriptionsForCustomer() {
        $result = AdvantageConnector::getSubscriptionsForCustomer($_ENV['TEST_CUSTOMER']);

        $this->assertEquals(count($result['items']), $result['returned']);
        $this->assertIsArray($result);
    }

    public function testGetUser() {
        $result = AdvantageConnector::getUser($_ENV['TEST_CUSTOMER']);

        $this->assertIsArray($result);
        $this->assertEquals($_ENV['TEST_CUSTOMER'], $result['CTM_NBR']);
    }

    public function testGetUsers() {
        $result = AdvantageConnector::getUsers();

        $this->assertIsArray($result);
        $this->assertEquals(count($result['items']), $result['returned']);

        $result = AdvantageConnector::getUsers(0, 0);

        $this->assertIsArray($result);
        $this->assertEquals(count($result['items']), $result['returned']);
        $this->assertEquals(0, $result['returned']);

    }

}
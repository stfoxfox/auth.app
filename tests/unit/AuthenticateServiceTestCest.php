<?php
namespace App\Tests;

use App\Entity\Client as DeviceClient;
use App\Tests\UnitTester;

class AuthenticateServiceTestCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    public function authenticate(UnitTester $I)
    {
        $auth_service = $I->getService('security.authentication.service');

        $email = 'example@exam555ple.com';
        $password = '123';
        $ip = '127.0.0.1';
        $application = $I->getDoctrine()->getRepository('App:App')->findOneBy(["name" => 'Public application']);
        $user = $I->getDoctrine()->getRepository('App:User')->findOneBy(['email' => $email]);

        if (!$user) {
            $response = $auth_service->authenticate($email, $password, $ip, $application, DeviceClient::TYPE_BROWSER, DeviceClient::TYPE_BROWSER);
            $I->assertTrue($response->getData() == 'User with this email already exists');
            $I->assertTrue($response->getCode() == 400);

            $password = "123456";

            $registration_service = $I->getService('security.registration.service');
            $first_name = 'Example First Name';
            $last_name = "Example Last Name";

            $response = $registration_service->registration(
                $password,
                $email,
                $first_name,
                $last_name,
                $ip,
                $application,
                DeviceClient::TYPE_BROWSER,
                DeviceClient::TYPE_BROWSER
            );

            $I->assertTrue(isset($response->getData()['clientUuid']));
            $I->assertTrue(isset($response->getData()['jwt']));
            $I->assertTrue(isset($response->getData()['userInfo']));
            $I->assertTrue(isset($response->getData()['userInfo']['userUuid']));

            $response = $auth_service->authenticate($email, $password, $ip, $application, DeviceClient::TYPE_BROWSER, DeviceClient::TYPE_BROWSER);
            $I->assertTrue(isset($response->getData()['clientUuid']));
            $I->assertTrue(isset($response->getData()['jwt']));
            $I->assertTrue(isset($response->getData()['userInfo']));
            $I->assertTrue(isset($response->getData()['userInfo']['userUuid']));
        } else {
            $response = $auth_service->authenticate($email, $password, $ip, $application, DeviceClient::TYPE_BROWSER, DeviceClient::TYPE_BROWSER);
            $I->assertTrue($response->getData() == 'Invalid username or password, try again');
            $I->assertTrue($response->getCode() == 403);
        }
    }

    public function createClient(UnitTester $I)
    {
        $ip = '127.0.0.1';
        $application = $I->getDoctrine()->getRepository('App:App')->findOneBy(["name" => 'Public application']);
        $auth_service = $I->getService('security.authentication.service');
        $response = $auth_service->createClient($ip, $application, DeviceClient::TYPE_BROWSER, DeviceClient::TYPE_BROWSER);
        $I->assertTrue($response instanceof DeviceClient);
    }

    public function setCookie(UnitTester $I)
    {
        $auth_service = $I->getService('security.authentication.service');
        $data = "example_string";
        $auth_service->setCookie($data);
        $I->assertFalse(isset($_COOKIE[$I->getContainer()->getParameter('authentication_cookie_name')]));    
    }

    public function removeCookie(UnitTester $I)
    {
        $auth_service = $I->getService('security.authentication.service');
        $data = "example_string";
        $auth_service->removeCookie($data);
        $I->assertFalse(isset($_COOKIE[$I->getContainer()->getParameter('authentication_cookie_name')]));    
    }
}

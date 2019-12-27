<?php
namespace App\Tests;

use App\Entity\Client as DeviceClient;
use App\Tests\UnitTester;

class RegistrationServiceTestCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    public function registration(UnitTester $I)
    {
        $registration_service = $I->getService('security.registration.service');
        
        $email = 'example@examaaaa555plse.com';
        $password = '1234556';
        $ip = '127.0.0.1';
        $first_name = 'Example First Name';
        $last_name = "Example Last Name";
        $application = $I->getDoctrine()->getRepository('App:App')->findOneBy(["name" => 'Public application']);

        $user = $I->getDoctrine()->getRepository('App:User')->findOneBy(['email' => $email]);

        if (!$user) {
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
        } else {
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

            $I->assertFalse(isset($response->getData()['clientUuid']));
            $I->assertFalse(isset($response->getData()['jwt']));
            $I->assertFalse(isset($response->getData()['userInfo']));
            $I->assertFalse(isset($response->getData()['userInfo']['userUuid']));

            $I->assertTrue($response->getData() == 'Invalid username or password, try again');
            $I->assertTrue($response->getCode() == 403);
        }

    }

    public function registrationConfirm(UnitTester $I){
        $registration_service = $I->getService('security.registration.service');

        $email = '123example@examaaaa555plse.com';
        $user = $I->getDoctrine()->getRepository('App:User')->findOneBy(['email' => $email]);
        
        if($user){
            $token = $user->getAuthenticationKey();
            $response = $registration_service->registrationConfirm($token);
            $I->assertTrue($response->getData() == null);
            $I->assertTrue($response->getCode() == 200);
        }else {
            $token = '123456';
            $response = $registration_service->registrationConfirm($token);
            $I->assertTrue($response['data'] == 'Пользователь не найден');
            $I->assertTrue($response['code'] == 403);
        }
    }
}

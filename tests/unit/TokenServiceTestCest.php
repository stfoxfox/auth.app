<?php
namespace App\Tests;

use App\Entity\Client as DeviceClient;
use App\Tests\UnitTester;

class TokenServiceTestCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    public function createAndUpdateJwtToken(UnitTester $I)
    {
        $token_service = $I->getService('security.token.service');
        $user_service = $I->getService('security.user.service');
        $auth_service = $I->getService('security.authentication.service');

        $ip = '127.0.0.1';
        $application = $I->getDoctrine()->getRepository('App:App')->findOneBy(["name" => 'Public application']);
        $client = $auth_service->createClient($ip, $application, DeviceClient::TYPE_BROWSER, DeviceClient::TYPE_BROWSER);
        $I->assertTrue($client instanceof DeviceClient);

        $email = 'example@example.co43234m';
        $password = '123';
        $first_name = "Example First Name";
        $last_name = "Example Last Name";

        $user = $I->getDoctrine()->getRepository('App:User')->findOneBy(['email' => $email]);
        if (!$user) {
            $user = $user_service->createUser($password, $email, $first_name, $last_name);
        }

        $jwt = $token_service->createJwt($user, $client);
        $I->assertFalse(empty($jwt));

        $response = $token_service->updateJwt($jwt, $client->getUuid());
        $I->assertTrue(isset($response->getData()['clientUuid']));
        $I->assertTrue(isset($response->getData()['jwt']));
        $I->assertTrue(isset($response->getData()['userInfo']));
        $I->assertTrue(isset($response->getData()['userInfo']['userUuid']));

        $response = $token_service->removeToken($response->getData()['jwt']);
        $I->assertTrue($response->getCode() == 200);

        $jwt = '123456';
        $response = $token_service->removeToken($jwt);
        $I->assertTrue($response->getData() == 'Не валидная сессия');
        $I->assertTrue($response->getCode() == 400);
    }
}

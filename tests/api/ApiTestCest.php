<?php
namespace App\Tests;

use App\Tests\ApiTester;
use App\Tests\Helper\Api;

class ApiTestCest
{
    /**
     * @var \App\Tests\ApiTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function registrationDeviceTest(ApiTester $I)
    {
        $I->sendPOST(
            $I->getContainer()->getParameter('mobile_api_endpoint'),
            '{"action":"registration-device","request":{"data":{"device_type": 1, "device_model": 2, "token": "fdjjskf873924"}},"authorisation":{"jwt_token":"","client_token":""}}'
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'error_msg' => 'registration-device',
            'error_code' => 0,
        ]);

        $I->sendPOST(
            $I->getContainer()->getParameter('mobile_api_endpoint'),
            '{"action":"registration-device","request":{"data":{"device_model": 2, "token": "fdjjskf873924"}},"authorisation":{"jwt_token":"","client_token":""}}'
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'error_msg' => 'не все данные',
            'error_code' => 3422,
        ]);

        $I->sendPOST(
            $I->getContainer()->getParameter('mobile_api_endpoint'),
            '{"action":"registration-device","request":{"data":{"device_type": 1, "token": "fdjjskf873924"}},"authorisation":{"jwt_token":"","client_token":""}}'
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'error_msg' => 'не все данные',
            'error_code' => 3422,
        ]);

        $I->sendPOST(
            $I->getContainer()->getParameter('mobile_api_endpoint'),
            '{"action":"registration-device","request":{"data":{"device_type": 1, "device_model": 2}},"authorisation":{"jwt_token":"","client_token":""}}'
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'error_msg' => 'не все данные',
            'error_code' => 3422,
        ]);

        $I->sendPOST(
            $I->getContainer()->getParameter('mobile_api_endpoint'),
            '{"action":"registration-device","request":{},"authorisation":{"jwt_token":"","client_token":""}}'
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'error_msg' => 'не все данные',
            'error_code' => 3422,
        ]);
    }

    public function registrationUser(ApiTester $I)
    {

        $I->sendPOST(
            $I->getContainer()->getParameter('mobile_api_endpoint'),
            '{"action":"registration-user","request":{"data":{"email": "example@example.com", "password": "examplepassword", "first_name": "foo", "second_name":"bar"}},"authorisation":{"jwt_token":"","client_token":""}}'
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();

        $I->sendPOST(
            $I->getContainer()->getParameter('mobile_api_endpoint'),
            '{"action":"registration-user","request":{"data":{"email": "example@example.com", "password": "examplepassword", "first_name": "foo", "second_name":"bar"}},"authorisation":{"jwt_token":"","client_token":""}}'
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'error_msg' => 'пользователь с таким email существует',
            'error_code' => 888,
        ]);

        $I->sendPOST(
            $I->getContainer()->getParameter('mobile_api_endpoint'),
            '{"action":"registration-user","request":{"data":{"password": "examplepassword", "first_name": "foo", "second_name":"bar"}},"authorisation":{"jwt_token":"","client_token":""}}'
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'error_msg' => 'не все данные',
            'error_code' => 3422,
        ]);

        $I->sendPOST(
            $I->getContainer()->getParameter('mobile_api_endpoint'),
            '{"action":"registration-user","request":{"data":{"email": "example@example.com", "first_name": "foo", "second_name":"bar"}},"authorisation":{"jwt_token":"","client_token":""}}'
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'error_msg' => 'не все данные',
            'error_code' => 3422,
        ]);

        $I->sendPOST(
            $I->getContainer()->getParameter('mobile_api_endpoint'),
            '{"action":"registration-user","request":{"data":{"email": "example@example.com", "password": "examplepassword", "second_name":"bar"}},"authorisation":{"jwt_token":"","client_token":""}}'
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'error_msg' => 'не все данные',
            'error_code' => 3422,
        ]);

        $I->sendPOST(
            $I->getContainer()->getParameter('mobile_api_endpoint'),
            '{"action":"registration-user","request":{"data":{"email": "example@example.com", "password": "examplepassword", "first_name": "foo"}},"authorisation":{"jwt_token":"","client_token":""}}'
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'error_msg' => 'не все данные',
            'error_code' => 3422,
        ]);

        $I->sendPOST(
            $I->getContainer()->getParameter('mobile_api_endpoint'),
            '{"action":"registration-user","request":{"data":{"email": "new_example@example.com", "password": "examplepassword", "first_name": "foo", "second_name":"bar", "social_id": "1893422354044313", "access_token": "EAACWtyAN3vsBAInBpeoxdNexWP7MCZAZAmNA3wYu9QR9PiN7jtaW23AaqqrrCtJ6sRPuSlrL97nnPSZBT0gZAwo09UZAjolkdJaVOGeBP333ZBm6nLBHj3PEDvAkkoReynZBehPl7hvrVU0uWhjYMdm1na3USZAsNV6WFozhyua3TAZDZD"}},"authorisation":{"jwt_token":"","client_token":""}}'
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
    }

    public function authUser(ApiTester $I)
    {
        $I->sendPOST(
            $I->getContainer()->getParameter('mobile_api_endpoint'),
            '{"action":"auth-user","request":{"data":{"email": "example@example.com", "password": "examplepassword"}},"authorisation":{"jwt_token":"","client_token":""}}'
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();

        $I->sendPOST(
            $I->getContainer()->getParameter('mobile_api_endpoint'),
            '{"action":"registration-user","request":{"data":{"password": "examplepassword", "first_name": "foo", "second_name":"bar"}},"authorisation":{"jwt_token":"","client_token":""}}'
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'error_msg' => 'не все данные',
            'error_code' => 3422,
        ]);

        $I->sendPOST(
            $I->getContainer()->getParameter('mobile_api_endpoint'),
            '{"action":"auth-user","request":{"data":{"email": "example@example.com"}},"authorisation":{"jwt_token":"","client_token":""}}'
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'error_msg' => 'не все данные',
            'error_code' => 3422,
        ]);
    }

    public function authFbUser(ApiTester $I)
    {
        $I->sendPOST(
            $I->getContainer()->getParameter('mobile_api_endpoint'),
            '{"action":"registration-user","request":{"data":{"social_id": "1893422354044313", "access_token": "EAACWtyAN3vsBAInBpeoxdNexWP7MCZAZAmNA3wYu9QR9PiN7jtaW23AaqqrrCtJ6sRPuSlrL97nnPSZBT0gZAwo09UZAjolkdJaVOGeBP333ZBm6nLBHj3PEDvAkkoReynZBehPl7hvrVU0uWhjYMdm1na3USZAsNV6WFozhyua3TAZDZD"}},"authorisation":{"jwt_token":"","client_token":""}}'
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();

        $I->sendPOST(
            $I->getContainer()->getParameter('mobile_api_endpoint'),
            '{"action":"registration-user","request":{"data":{"access_token": "EAACWtyAN3vsBAInBpeoxdNexWP7MCZAZAmNA3wYu9QR9PiN7jtaW23AaqqrrCtJ6sRPuSlrL97nnPSZBT0gZAwo09UZAjolkdJaVOGeBP333ZBm6nLBHj3PEDvAkkoReynZBehPl7hvrVU0uWhjYMdm1na3USZAsNV6WFozhyua3TAZDZD"}},"authorisation":{"jwt_token":"","client_token":""}}'
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'error_msg' => 'не все данные',
            'error_code' => 3422,
        ]);

        $I->sendPOST(
            $I->getContainer()->getParameter('mobile_api_endpoint'),
            '{"action":"registration-user","request":{"data":{"social_id": "1893422354044313"}},"authorisation":{"jwt_token":"","client_token":""}}'
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'error_msg' => 'не все данные',
            'error_code' => 3422,
        ]);
    }
}

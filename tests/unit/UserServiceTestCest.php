<?php
namespace App\Tests;
use App\Tests\UnitTester;
use App\Entity\User;
// use AcmePack\UnitTester;

class UserServiceTestCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    public function createUser(UnitTester $I)
    {
        $user_service = $I->getService('security.user.service');

        $email = 'example@example.co43234m';
        $password = '123';
        $first_name = "Example First Name";
        $last_name = "Example Last Name";

        $user = $I->getDoctrine()->getRepository('App:User')->findOneBy(['email' => $email]);
        if(!$user){
            $user = $user_service->createUser($password, $email, $first_name, $last_name);
            $I->assertTrue($user instanceof User);
        }
    }

    public function sendConfirmRegistrationMessage(UnitTester $I){
        $user_service = $I->getService('security.user.service');

        $token = "123456";
        $email = 'example@example.co43234m';
        
        $status = $user_service->sendConfirmRegistrationMessage($email, $token);
        $I->assertTrue((bool) $status);
    }

    public function sendMessageRecoverPassword(UnitTester $I)
    {
        $user_service = $I->getService('security.user.service');

        $email = 'example@example.com';

        $user = $I->getDoctrine()->getRepository('App:User')->findOneBy(['email' => $email]);
        if($user){
            $response = $user_service->sendMessageRecoverPassword($email);
            $I->assertTrue($response->getData() == 'A link has been sent to your e-mail, after which you can change the password');
            $I->assertTrue($response->getCode() == 200); 
        }
    }

    public function sendMessageChangeEmail(UnitTester $I){
        $user_service = $I->getService('security.user.service');

        $email = 'example@example.com';
        $new_email = 'newexample@example.com';
        $user = $I->getDoctrine()->getRepository('App:User')->findOneBy(['email' => $email]);

        if($user){
            $response = $user_service->sendMessageChangeEmail($user, $new_email);
            $I->assertTrue($response->getData() == null);
            $I->assertTrue($response->getCode() == 200);
        }   
    }

    public function setOrGetFromRedis(UnitTester $I){

        $key = 'example_key';
        $value = 'example_value';

        $user_service = $I->getService('security.user.service');
        $user_service->setRecordRedis($key, $value);
        
        $data = $user_service->getRecordRedis($key);
        $I->assertTrue($value == $data);

        $user_service->unsetRecordRedis($key);
        $data = $user_service->getRecordRedis($key);
        $I->assertTrue($data == null);
    }

    public function changePassword(UnitTester $I)
    {
        $user_service = $I->getService('security.user.service');
        
        $email = 'example@example.com';
        $password = '123456789';
        $confirm_password = $password;

        $user = $I->getDoctrine()->getRepository('App:User')->findOneBy(['email' => $email]);
        
        if($user){
            $response = $user_service->changePassword($user, $password, $confirm_password);
            $I->assertTrue($response->getCode() == 200);

            $password = '123';
            $confirm_password = $password;
            $response = $user_service->changePassword($user, $password, $confirm_password);
            $I->assertTrue($response->getData() == 'Password must be at least 6 characters long');
            $I->assertTrue($response->getCode() == 400);

            $password = '654321';
            $confirm_password = '123456';
            $response = $user_service->changePassword($user, $password, $confirm_password);
            $I->assertTrue($response->getData() == 'passwords-do-not-match');
            $I->assertTrue($response->getCode() == 400);
        }
    }
}

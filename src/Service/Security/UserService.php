<?php

namespace App\Service\Security;

use App\Classes\Security\SecurityNotifications;
use App\Classes\Security\SecurityResponseObject;
use App\Entity\OldHash;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;

/**
 * сервис для пользователей
 */
class UserService
{
    /**
     * @var Container $container
     */
    private $container;

    /**
     * @var $em EntityManager
     */
    private $em;

    /**
     * @param Container $container
     * @throws \Exception
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    /**
     * Создание пользователя
     * Создаём пользователя
     * Для него создаём запись в таблице old_hash
     * Возвращаем объект пользователя
     * @param string $password
     * @param string $email
     * @param string $name
     * @param string $surname
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     * @return User
     */
    public function createUser(string $password, string $email, string $name, string $surname)
    {
        $user = new User();
        $user->setStatusId(false);

        $user->setEmail($email);
        $tokenConfirmRegistration = $this->generateRandomString(17);
        $this->sendConfirmRegistrationMessage($email, $tokenConfirmRegistration);
        $user->setAuthenticationKey($tokenConfirmRegistration);

        $passwordHash = $this->container->get('security.encoder.service')->encrypt($password);
        $user->setPasswordHash($passwordHash);
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());

        $oldHash = new OldHash();
        $oldHash->setUser($user);
        $oldHash->setPasswordHash($passwordHash);
        $oldHash->setCreatedAt(new \DateTime());
        $oldHash->setUpdatedAt(new \DateTime());

        $this->em->persist($user);
        $this->em->flush();

        $this->em->persist($oldHash);
        $this->em->flush();

        return $user;
    }

    /**
     * Отправка email собщения о регистрации
     * формируем текст сообщения
     * отправляем сообщение
     * возвращаем статус отправки
     * @param string $email
     * @param string $tokenConfirmRegistration
     * @return int
     * @throws \Exception
     */
    public function sendConfirmRegistrationMessage(string $email, string $tokenConfirmRegistration): int
    {
        $domain = $this->container->getParameter('site_domain');
        $emailMessage = SecurityNotifications::MESSAGE_CONFIRM_REGISTRATION["en"] .
            "<a href='$domain/registration_confirm/$tokenConfirmRegistration'>
                $domain/registration_confirm/$tokenConfirmRegistration
            </a>";

        $message = (new \Swift_Message(SecurityNotifications::CONFIRM_REGISTRATION["en"]))
            ->setFrom($this->container->getParameter('from_email'))
            ->setTo($email)
            ->setBody($emailMessage, 'text/html');

        $status = $this->container->get('mailer')->send($message);

        return $status;
    }

    /**
     * Отправка email сообщения об изменении пароля
     * генерируем токен для изменения пароля
     * находим пользователя по email
     * записываем токен к пользователю
     * формируем сообщение для отправки
     * отправляем сообщение
     * Возвращаем статус отправки сообщения
     * @param string $email
     * @return SecurityResponseObject
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function sendMessageRecoverPassword(string $email)
    {
        $tokenResetPassword = $this->generateRandomString(17);
        $user = $this->em->getRepository('App:User')->findOneBy(["email" => $email]);
        if($user == null){
            return SecurityNotifications::getErrorResponse(SecurityNotifications::INCORRECT_EMAIL);
        }
        $user->setTokenResetPassword($tokenResetPassword);
        $this->em->flush($user);

        $domain = $this->container->getParameter('site_domain');

        $emailMessage = SecurityNotifications::EMAIL_BODY_RECOVER_PASSWORD["en"] .
            "<a href='$domain/recover_password/$tokenResetPassword'>
                $domain/recover_password/$tokenResetPassword
            </a>";

        $message = (new \Swift_Message(SecurityNotifications::EMAIL_TITLE_RECOVER_PASSWORD["en"]))
            ->setFrom($this->container->getParameter('from_email'))
            ->setTo($email)
            ->setBody($emailMessage, 'text/html');

        $status = $this->container->get('mailer')->send($message);

        if (!$status) {
            return SecurityNotifications::getErrorResponse(SecurityNotifications::UNKNOWN_ERROR);
        }

        return SecurityNotifications::getErrorResponse(SecurityNotifications::SEND_EMAIL_RECOVER);

    }

    /**
     * Изменение пароля пользователя
     * Находим пользователя по токену
     * удаляем токен для воосстановления паороля
     * шифруем пароль
     * Возвращаем статус
     * @param string $token
     * @param string $password
     * @return SecurityResponseObject
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function recoverPassword($token, $password): SecurityResponseObject
    {
        $user = $this->em->getRepository('App:User')->findOneBy(["tokenResetPassword" => $token]);
        if($user == null){
            return SecurityNotifications::getErrorResponse(['data' => 'user not found', 'code' => 55, 'location' => 123]);
        }
        $user->setTokenResetPassword($token);

        $passwordHash = $this->container->get('security.encoder.service')->encrypt($password);
        $user->setPasswordHash($passwordHash);
        $this->em->flush($user);


        return SecurityNotifications::getSuccessResponse(SecurityNotifications::RECOVER_PASSWORD['en']);
    }

    /**
     * отправка сообщения об изменении email
     * находим пользователя по email
     * если нет - возвращаем ошибку
     * гененируем токен
     * получаем старый email
     * записываем в redis старый и новый email
     * формируем сообщение на отправку
     * Отправляем сообщение
     * возвращаем статус отправки
     * @param User $user
     * @param string $newEmail
     * @return SecurityResponseObject
     * @throws \Exception
     */
    public function sendMessageChangeEmail(User $user, string $newEmail): SecurityResponseObject
    {
        $replyEmail = $this->em->getRepository('App:User')->findOneBy(["email" => $newEmail]);
        if ($replyEmail) {
            return SecurityNotifications::getErrorResponse(SecurityNotifications::EXIST_EMAIL_CHANGE);
        }

        $confirmationCode = $this->generateRandomString(17);
        $oldEmail = $user->getEmail();
        $this->setRecordRedis($confirmationCode, [
            "old_email" => $oldEmail,
            "new_email" => $newEmail,
        ]);

        $domain = $this->container->getParameter('site_domain');

        $emailMessage = "<a href='$domain/change_email/$confirmationCode'>
                $domain/change_email/$confirmationCode
            </a>";

        $message = (new \Swift_Message($this->container->get('translator')->trans('User email')))
            ->setFrom($this->container->getParameter('from_email'))
            ->setTo($oldEmail)
            ->setBody($emailMessage, 'text/plain');

        $status = $this->container->get('mailer')->send($message);

        if (!$status) {
            return SecurityNotifications::getErrorResponse(SecurityNotifications::UNKNOWN_ERROR_EMAIL_CHANGE);
        }

        return SecurityNotifications::getSuccessResponse();
    }

    /**
     * Изменение email
     * находим строку с токеном в redis
     * если нету - возвращаем ошибку
     * удаляем строку из redis
     * находим пользователя по старому email
     * устанавливаем новый
     * Возвращаем статус операции
     * @param $token
     * @return SecurityResponseObject
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function changeEmail($token): SecurityResponseObject
    {
        $data = $this->getRecordRedis($token);

        if (!$data) {
            return SecurityNotifications::getErrorResponse(SecurityNotifications::UNKNOWN_ERROR_EMAIL_CHANGE);
        }

        $this->unsetRecordRedis($token);

        $user = $this->em->getRepository('App:User')->findOneBy(["email" => $data->old_email]);
        $user->setEmail($data->new_email);
        $this->em->flush($user);

        return SecurityNotifications::getSuccessResponse($this->container->get('translator')->trans('Completed'));
    }

    /**
     * Запись строки в redis
     * @param string $key
     * @param string $data
     */
    public function setRecordRedis($key, $data)
    {
        $client = new \Predis\Client();
        $client->set($key, json_encode($data));
    }

    /**
     * Получение строки из redis
     * @param string $key
     * @return string
     */
    public function getRecordRedis($key)
    {
        $client = new \Predis\Client();
        return json_decode($client->get($key));
    }

    /**
     * Удаление строки из redis
     * @param string $key
     */
    public function unsetRecordRedis($key)
    {
        $client = new \Predis\Client();
        $client->del([$key]);
    }

    /**
     * Генерация случайной строки
     * @param int $count
     * @return string
     */
    public function generateRandomString(int $count)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randString = '';
        for ($i = 0; $i < $count; $i++) {
            $randString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randString;
    }

    /**
     * Получение пользователя по JWT tokeb
     * Получаем payload из JWT tokeb
     * проверяем время жизни токена
     * если меньше - возвращаем null
     * находим пользователя по userUUID в payload
     * Возвращаем польщователя
     * @param string $jwt
     * @return User
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException
     * @throws \Exception
     * "к
     */
    public function getUserByJwt($jwt)
    {
        $data = $this->container->get('security.token.service')->getPayloadFromJwt($jwt);

        if ($data['exp'] < time()) {
            return null;
        }

        return $this->em->getRepository('App:User')->find($data['userUuid']);
    }

    /**
     * Изменение пароля
     * проверяем password и confirm_password
     * если не совпадают - возвращаем ошибку
     * проверяем пароль на длинну
     * если меньше - возвращаем ощибку
     * шифруем пароль
     * записываем старый пароль в old_hash
     * записываем пользователю новый пароль
     * Возвращаем статус операции
     * @param User $user
     * @param string $password
     * @param string $confirmPassword
     * @return SecurityResponseObject
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function changePassword(User $user, string $password, string $confirmPassword): SecurityResponseObject
    {
        if ($password != $confirmPassword) {
            return SecurityNotifications::getErrorResponse(SecurityNotifications::PASSWORD_DO_NOT_MATCH);
        }

        if (strlen($password) < 6) {
            return SecurityNotifications::getErrorResponse(SecurityNotifications::CHARACTERS_LONG_6);
        }

        $passwordHash = $this->container->get('security.encoder.service')->encrypt($password);

        $oldHash = new OldHash();
        $oldHash->setUser($user);
        $oldHash->setPasswordHash($passwordHash);
        $oldHash->setCreatedAt(new \DateTime());
        $oldHash->setUpdatedAt(new \DateTime());
        $this->em->persist($oldHash);

        $user->setPasswordHash($passwordHash);

        $this->em->flush();

        return SecurityNotifications::getSuccessResponse($this->container->get('translator')->trans('Your password was successfully changed'));
    }

}

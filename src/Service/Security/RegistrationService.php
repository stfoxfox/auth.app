<?php

namespace App\Service\Security;

use App\Classes\Security\SecurityNotifications;
use App\Classes\Security\SecurityResponseObject;
use App\Entity\App;
use Symfony\Component\DependencyInjection\Container;

/**
 * сервис для регистрации пользователей
 */
class RegistrationService
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
     * регистрация пользователя
     * проверяем пароль на длинну
     * если меньше - выкидываем ошибку
     * проверяем пользователя на существование в БД
     * если нет - выбрасываем ошибку
     * создаём пользователя с помощью пароля, email, имя и фамилии
     * авторизовываем пользователя
     * возвращаем массив с clientUuid, JWT token и useruuid
     * @param string $password
     * @param string $email
     * @param string $name
     * @param string $surname
     * @param string $ip
     * @param App $application
     * @param int $deviceModel
     * @param int $deviceType
     * @return SecurityResponseObject
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function registration(
        string $password,
        string $email,
        string $name,
        string $surname,
        string $ip,
        App $application,
        int $deviceModel,
        int $deviceType): SecurityResponseObject {
        if (strlen($password) < (int) $this->container->getParameter('length_password')) {
            return SecurityNotifications::getErrorResponse(SecurityNotifications::LESS_PASSWORD);
        }

        if ($this->em->getRepository('App:User')->findOneBy(["email" => $email])) {
            return SecurityNotifications::getErrorResponse(SecurityNotifications::EXIST_EMAIL);
        }

        $this->container->get('security.user.service')->createUser($password, $email, $name, $surname);
        $this->em->flush();

        $response = $this->container->get('security.authentication.service')
            ->authenticate($email, $password, $ip, $application, $deviceModel, $deviceType);

        return $response;
    }

    /**
     * подтверждение регистрации
     * проверяем наличие пользователя в БД по токену
     * если нет - выкидываем ошибку
     * если есть - устанавливаем статус true
     * возвращаем true
     * @param $token
     * @return SecurityResponseObject|array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function registrationConfirm($token)
    {
        $user = $this->em->getRepository('App:User')->findOneBy(["authenticationKey" => $token]);
        if (!$user) {
            return SecurityNotifications::USER_NOT_FOUND;
        }

        $user->setStatusId(true);
        $this->em->flush($user);

        return SecurityNotifications::getSuccessResponse();
    }
}

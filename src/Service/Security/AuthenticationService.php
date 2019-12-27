<?php

namespace App\Service\Security;

use App\Classes\Security\SecurityNotifications;
use App\Classes\Security\SecurityResponseObject;
use App\Entity\App;
use App\Entity\Client;
use Facebook\Facebook;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;

/**
 * сервис для авторизации
 */
class AuthenticationService
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
     * AuthenticationService constructor.
     * @param Container $container
     * @throws \Exception
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    /**
     * Авторизация пользователя
     * проверяем пользователя на его наличие в БД по email
     * если есть такой пользователь - выбрасываем ошибку
     * проверяем пароль пользователя
     * если неправильный - выбрасываем ошибку
     * создаём клиента по IP, приложению, типу устройства и модели
     * создаём JWT токен для пользователя
     * возвращаем объект с clientUuid, JWT token и userUuid
     * @param string $email
     * @param string $password
     * @param string $ip
     * @param App $application
     * @param integer $deviceModel
     * @param integer $deviceType
     * @return SecurityResponseObject
     */
    public function authenticate(string $email, string $password, string $ip, App $application, $deviceModel, $deviceType): SecurityResponseObject
    {
        $user = $this->em->getRepository("App:User")->findOneBy(["email" => $email]);
        if (!$user) {
            return SecurityNotifications::getErrorResponse(SecurityNotifications::EXIST_EMAIL);
        }

        $hashPassword = $this->container->get('security.encoder.service')->encrypt($password);
        $hashPasswordFromDatabase = $user->getPasswordHash();
        if ($hashPassword !== $hashPasswordFromDatabase) {
            return SecurityNotifications::getErrorResponse(SecurityNotifications::INCORRECT_USER_OR_PASSWORD);
        }

        $client = $this->createClient($ip, $application, $deviceModel, $deviceType);
        $jwt = $this->container->get('security.token.service')->createJwt($user, $client);
        $this->container->get('security.token.service')->createToken($user, $client, $jwt);

        return SecurityNotifications::getSuccessResponse([
            "clientUuid" => $client->getUuid(),
            "jwt" => $jwt,
            "userInfo" => [
                "userUuid" => $user->getUuid(),
            ],
        ]);
    }

    /**
     * Создание клиента пользователя
     * проверяем на наличие такого клиента по IP, приложению, типу устройства и модели
     * Если его нету - создаём
     * Возвращаем объект клиента
     * @param $ip
     * @param $application
     * @param $deviceModel
     * @param $deviceType
     * @return Client
     * @throws \Exception
     */
    public function createClient($ip, App $application, $deviceModel, $deviceType): Client
    {
        $client = $this->em->getRepository('App:Client')->findOneBy([
            "ipAddress" => $ip,
            "application" => $application,
            "deviceType" => $deviceType,
            "deviceModel" => $deviceModel,
        ]);

        if (!$client) {
            $client = new Client();
            $client->setApplication($application);
            $client->setDeviceModel($deviceModel);
            $client->setDeviceType($deviceType);
            $client->setIpAddress($ip);
            $this->em->persist($client);
        }

        $client->setCreatedAt(new \DateTime());
        $client->setUpdatedAt(new \DateTime());
        $client->setTokenPushMessages(bin2hex(random_bytes(78)));

        $this->em->flush($client);

        return $client;
    }

    /**
     * Установка cookie
     * @param $data
     */
    public function setCookie($data)
    {
        setcookie(
            $this->container->getParameter('authentication_cookie_name'),
            json_encode($data),
            time() + (int) $this->container->getParameter('token_lifetime'),
            '/',
            $this->container->getParameter('cookie_domain')
        );
    }

    /**
     * удаление cookie
     */
    public function removeCookie()
    {
        setcookie(
            $this->container->getParameter('authentication_cookie_name'),
            "",
            -1,
            '/',
            $this->container->getParameter('cookie_domain')
        );
    }

    /**
     * получениие URL для авторизации на FB
     * @return string
     */
    public function getFbAuthUrl(Request $request)
    {
        $fb = new Facebook([
            'app_id' => $this->container->getParameter('fbid'),
            'app_secret' => $this->container->getParameter('fbsecret'),
            'default_graph_version' => 'v2.10',
        ]);

        $helper = $fb->getRedirectLoginHelper();

        $permissions = ['email'];
        $base_url = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        $loginUrl = $helper->getLoginUrl($base_url.'/fb-callback', $permissions);
        return $loginUrl;
    }

}

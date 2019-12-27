<?php

namespace App\Service\Security;

use App\Classes\Security\SecurityNotifications;
use App\Classes\Security\SecurityResponseObject;
use App\Entity\Client;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * сервис для генерации JWT token
 */
class TokenService
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
     * Создание JWT token
     * c помощью LexikJWTAuthenticationBundle создаём token
     * с payload userUUid, clientUuid и exp
     * Возвращаем JWT token
     * @param User $user
     * @param Client $client
     * @return string
     * @throws \Exception
     */
    public function createJwt(User $user, Client $client): string
    {
        return $this->container->get('lexik_jwt_authentication.encoder')->encode([
            "userUuid" => $user->getUuid(),
            "clientUuid" => $client->getUuid(),
            "exp" => time() + (int) $this->container->getParameter('token_lifetime'),
        ]);
    }

    /**
     * Обновление JWT token
     * Декодируем playload JWT token
     * проверяем clientUUID токена с переданным clientUUID
     * если не совпадают - выбрасываем ошибку
     * находим пользователя и клиент по clientUUID и userUUID из payload JWT token
     * на основе их создаём новый JWT token
     * удаляем прошлый токен
     * записываем значение нового токена в БД
     * Возвращаем ответ с clientUUID, JWT token и userUUID
     * @param string $jwt
     * @param uuid $clientUuid
     * @return SecurityResponseObject
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function updateJwt($jwt, $clientUuid)
    {
        $payload = $this->getPayloadFromJwt($jwt);

        if ($clientUuid != $payload['clientUuid']) {
            return SecurityNotifications::getErrorResponse(SecurityNotifications::INVALID_TOKEN);
        }

        $user = $this->em->getRepository('App:User')->find($payload['userUuid']);
        $client = $this->em->getRepository('App:Client')->find($payload['clientUuid']);

        $newJwt = $this->createJwt($user, $client);
        $this->removeToken($jwt);
        $this->createToken($user, $client, $newJwt);

        return SecurityNotifications::getSuccessResponse([
            "clientUuid" => $client->getUuid(),
            "jwt" => $newJwt,
            "userInfo" => [
                "userUuid" => $user->getUuid(),
            ],
        ]);

    }

    /**
     * Декодирование payload из JWT token
     * С помощью LexikJWTAuthenticationBundle декодируем JWT token
     * Возвращаем payload JWT token
     * @param $jwt
     * @return array
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException
     * @throws \Exception
     */
    public function getPayloadFromJwt($jwt)
    {
        return $this->container->get('lexik_jwt_authentication.encoder')->decode($jwt);
    }

    /**
     * Создание токена в БД
     * @param User $user
     * @param Client $client
     * @param string $jwt
     * @return Token $token
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createToken(User $user, Client $client, string $jwt)
    {
        $token = new \App\Entity\Token();
        $token->setUser($user);
        $token->setClient($client);
        $token->setJWTToken($jwt);
        $this->em->persist($token);
        $this->em->flush($token);

        return $token;
    }

    /**
     * Удаление токена из БД
     * проверям наличие JWT token в БД
     * если нет - выбрасываем ошибку
     * удаляем токен из БД
     * Возвращаем true
     * @param $jwt
     * @return SecurityResponseObject
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function removeToken($jwt): SecurityResponseObject
    {
        $token = $this->em->getRepository('App:Token')->findOneBy(["JWTToken" => $jwt]);

        if (!$token) {
            return SecurityNotifications::getErrorResponse(SecurityNotifications::INVALID_SESSION);
        }

        $this->em->remove($token);
        $this->em->flush($token);

        return SecurityNotifications::getSuccessResponse([
            "status" => "success",
        ]);
    }

    /**
     * Получение JWT token из cookie
     * получаем cookie
     * если нет - возвращаем false
     * декодируем cookie
     * возвращаем JWT token
     * @param ParameterBag $cookie
     * @return bool|string
     */
    public function getJwtFromCookie(ParameterBag $cookie)
    {
        $cookie_auth_json = $cookie->get($this->container->getParameter('authentication_cookie_name'));

        if (!$cookie_auth_json) {
            return false;
        }

        $cookie_auth = json_decode($cookie_auth_json);

        return $cookie_auth->jwt;
    }

}

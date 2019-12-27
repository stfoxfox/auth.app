<?php

namespace App\Service\Security;

use App\Classes\Security\SecurityNotifications;
use App\Classes\Security\SecurityResponseObject;
use Symfony\Component\DependencyInjection\Container;

/**
 * сервис ролей
 */
class RoleService
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
     * получение ролей
     * проверяем наличие приложения
     * если нет - выбрасываем ошибку
     * проверяем преденный ключи приложения и ключом приложения
     * если не совпадают - выбрасываем ошибку
     * получаем роли для приложения
     * возвращаем ответ с ролями для приложения
     * @param string $appId
     * @param string $apiKey
     * @return SecurityResponseObject
     */
    public function getRoles(string $appId, string $apiKey): SecurityResponseObject
    {
        $application = $this->em->getRepository('App:App')->find($appId);

        if (!$application) {
            return SecurityNotifications::getErrorResponse(SecurityNotifications::APPLICATION_NOT_FOUND);
        }

        if ($application->getKeyApplication() !== $apiKey) {
            return SecurityNotifications::getErrorResponse(SecurityNotifications::INCORRECT_INCOMING_DATA);
        }

        $roles = $this->em->getRepository('App:Role')->getRolesByApplication($application);

        return SecurityNotifications::getSuccessResponse($roles);
    }

}

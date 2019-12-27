<?php

namespace App\Service\Security;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;

/**
 * сервис для авторизации
 */
class AuthorizationService
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
     * AuthorizationService constructor.
     * @param Container $container
     * @throws \Exception
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine.orm.entity_manager');
    }
}

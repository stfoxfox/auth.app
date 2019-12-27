<?php

namespace App\Service\Security;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;

/**
 * сервис для шифорвания паролей
 */
class EncoderService
{
    /**
     * @var Container
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
     * Шифрование пароля
     * @param $data
     * @return string
     */
    public function encrypt($data)
    {
        $salt = $this->container->getParameter("security_salt");
        $level1 = hash('sha512', $data);
        $hashSalt = hash('sha512', $salt);
        $level2 = hash('sha512', $level1 . $hashSalt);
        $level3 = hash('sha512', $level1 . $level2);
        return $level3;
    }

}

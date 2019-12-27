<?php

namespace App\Repository;

use App\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Role|null find($id, $lockMode = null, $lockVersion = null)
 * @method Role|null findOneBy(array $criteria, array $orderBy = null)
 * @method Role[]    findAll()
 * @method Role[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Role::class);
    }

    /**
     * получение списка ролей и их прав по приложению
     * @param App\Entity\App $application
     */
    public function getRolesByApplication($application)
    {
        return $this->createQueryBuilder('role')
            ->addSelect('permissions')
            ->innerJoin('role.app', 'app')
            ->leftJoin('role.permissions', 'permissions')
            ->andWhere('app = :app')
            ->setParameter('app', $application)
            ->getQuery()
            ->getResult();
    }
}

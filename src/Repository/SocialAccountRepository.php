<?php

namespace App\Repository;

use App\Entity\SocialAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SocialAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method SocialAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method SocialAccount[]    findAll()
 * @method SocialAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SocialAccountRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SocialAccount::class);
    }
}

<?php

namespace App\Repository;

use App\Entity\OldHash;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method OldHash|null find($id, $lockMode = null, $lockVersion = null)
 * @method OldHash|null findOneBy(array $criteria, array $orderBy = null)
 * @method OldHash[]    findAll()
 * @method OldHash[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OldHashRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OldHash::class);
    }
}

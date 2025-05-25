<?php

namespace App\Repository;

use App\Entity\AppUserMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AppUserMember>
 */
class AppUserMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppUserMember::class);
    }

    /**
     * Trouve la sélection d'un utilisateur pour un splitter donné
     * @throws NonUniqueResultException
     */
    public function findOneByUserAndSplitter($appUser, $splitter): ?AppUserMember
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.appUser = :appUser')
            ->andWhere('s.splitter = :splitter')
            ->setParameter('appUser', $appUser)
            ->setParameter('splitter', $splitter->getId(), 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }
}

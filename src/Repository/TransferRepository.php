<?php

namespace App\Repository;

use App\Entity\Transfer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transfer>
 */
class TransferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transfer::class);
    }

    public function save(Transfer $transfer, bool $flush = false): void
    {
        $this->getEntityManager()->persist($transfer);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Transfer $transfer, bool $flush = false): void
    {
        $this->getEntityManager()->remove($transfer);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Récupère les transferts soft-deleted pour un splitter donné.
     */
    public function findSoftDeletedInSplitter(string $splitterId): array
    {
        $qb = $this->createQueryBuilder('t')
            ->select([
                't.id',
                't.amount',
                't.description',
                't.deletedAt',
                'fromMember.nickname AS fromMemberName',
                'toMember.nickname AS toMemberName',
            ])
            ->leftJoin('t.fromMember', 'fromMember')
            ->leftJoin('t.toMember', 'toMember')
            ->where('t.splitter = :splitterId')
            ->andWhere('t.deletedAt IS NOT NULL')
            ->setParameter('splitterId', $splitterId)
            ->orderBy('t.deletedAt', 'DESC');

        // Désactiver le filtre soft-delete pour récupérer les entités supprimées
        $this->getEntityManager()->getFilters()->disable('softdeleteable');

        $result = $qb->getQuery()->getResult();

        // Réactiver le filtre
        $this->getEntityManager()->getFilters()->enable('softdeleteable');

        return $result;
    }
}

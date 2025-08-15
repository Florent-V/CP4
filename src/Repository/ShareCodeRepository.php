<?php

namespace App\Repository;

use App\Entity\ShareCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;

/**
 * @extends ServiceEntityRepository<ShareCode>
 */
class ShareCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShareCode::class);
    }

    public function save(ShareCode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ShareCode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Recherche un code valide pour une entité spécifique
     */
    public function findValidCode(string $code, string $entityType, string $entityId): ?ShareCode
    {
        return $this->createQueryBuilder('sc')
            ->andWhere('sc.code = :code')
            ->andWhere('sc.entityType = :entityType')
            ->andWhere('sc.entityId = :entityId')
            ->andWhere('sc.isUsed = false')
            ->andWhere('sc.expiresAt > :now')
            ->setParameter('code', $code)
            ->setParameter('entityType', $entityType)
            ->setParameter('entityId', $entityId)
            ->setParameter('now', new DateTime())
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Recherche un code valide (sans vérifier l'entité spécifique)
     */
    public function findValidCodeByCodeOnly(string $code): ?ShareCode
    {
        return $this->createQueryBuilder('sc')
            ->andWhere('sc.code = :code')
            ->andWhere('sc.isUsed = false')
            ->andWhere('sc.expiresAt > :now')
            ->setParameter('code', $code)
            ->setParameter('now', new DateTime())
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Vérifie si un code existe déjà (pour éviter les doublons)
     */
    public function existsByCode(string $code): bool
    {
        return $this->createQueryBuilder('sc')
            ->select('COUNT(sc.id)')
            ->andWhere('sc.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    /**
     * Nettoie les codes expirés (à utiliser avec une commande ou un cron)
     */
    public function deleteExpiredCodes(): int
    {
        return $this->createQueryBuilder('sc')
            ->delete()
            ->andWhere('sc.expiresAt < :now')
            ->setParameter('now', new DateTime())
            ->getQuery()
            ->execute();
    }

    /**
     * Trouve tous les codes actifs pour une entité donnée
     */
    public function findActiveCodesForEntity(string $entityType, string $entityId): array
    {
        return $this->createQueryBuilder('sc')
            ->andWhere('sc.entityType = :entityType')
            ->andWhere('sc.entityId = :entityId')
            ->andWhere('sc.isUsed = false')
            ->andWhere('sc.expiresAt > :now')
            ->setParameter('entityType', $entityType)
            ->setParameter('entityId', $entityId)
            ->setParameter('now', new DateTime())
            ->orderBy('sc.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Invalide tous les codes existants pour une entité (utile avant de créer un nouveau code)
     */
    public function invalidateCodesForEntity(string $entityType, string $entityId, ?string $type = null): int
    {
        $qb = $this->createQueryBuilder('sc')
            ->update()
            ->set('sc.isUsed', 'true')
            ->set('sc.usedAt', ':now')
            ->andWhere('sc.entityType = :entityType')
            ->andWhere('sc.entityId = :entityId')
            ->andWhere('sc.isUsed = false')
            ->setParameter('entityType', $entityType)
            ->setParameter('entityId', $entityId)
            ->setParameter('now', new DateTime());

        // Filtrer par type si spécifié
        if ($type !== null) {
            $qb->andWhere('sc.type = :type')
               ->setParameter('type', $type);
        }

        return $qb->getQuery()->execute();
    }
}

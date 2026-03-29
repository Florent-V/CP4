<?php

namespace App\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Gedmo\Loggable\Entity\LogEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LogEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogEntry::class);
    }

    /**
     * Récupérer les logs d'une entité spécifique.
     *
     * @param string $entityClass La classe de l'entité (ex : Splitter::class)
     * @param string $objectId L'ID de l'objet concerné
     * @return LogEntry[] Liste des logs
     */
    public function findLogsByEntity(string $entityClass, string $objectId): array
    {
        return $this->findBy(
            ['objectClass' => $entityClass, 'objectId' => $objectId],
            ['loggedAt' => 'DESC']
        );
    }

    public function findLogsWithUserInfoBySplitterId(string $splitterId): array
    {
        $qb = $this->createQueryBuilder('log')
            ->leftJoin('App\Entity\User', 'user', 'WITH', 'log.username = user.email')
            ->andWhere('log.objectId = :splitterId')
            ->setParameter('splitterId', $splitterId)
            ->orderBy('log.loggedAt', 'DESC')
            ->select([
                'log.action AS action',
                'log.version AS version',
                'log.loggedAt AS loggedAt',
                'log.data AS data',
                'user.email AS userEmail',
                'user.firstName AS firstName',
                'user.lastName AS lastName',
            ]);

        return $qb->getQuery()->getResult();
    }

    /**
     * Récupérer les logs pour plusieurs entités via leurs IDs.
     *
     * @param string $entityClass La classe de l'entité (ex : Expense::class)
     * @param array $objectIds Les IDs des objets concernés
     * @return LogEntry[] Liste des logs
     */
    public function findLogsForMultipleEntities(string $entityClass, array $objectIds): array
    {
        return $this->createQueryBuilder('log')
            ->leftJoin('App\Entity\User', 'user', 'WITH', 'log.username = user.email')
            ->leftJoin('App\Entity\Expense', 'expense', 'WITH', 'log.objectId = expense.id')
            ->where('log.objectClass = :entityClass')
            ->andWhere('log.objectId IN (:objectIds)')
            ->setParameter('entityClass', $entityClass)
            ->setParameter('objectIds', $objectIds)
            ->orderBy('log.loggedAt', 'DESC')
            ->select([
                'log.action AS action',
                'log.version AS version',
                'log.loggedAt AS loggedAt',
                'log.data AS data',
                'log.username AS username',
                'user.email AS userEmail',
                'user.firstName AS firstName',
                'user.lastName AS lastName',
                'expense.name AS expenseName',
                'expense.amount AS expenseAmount',
                'expense.devise AS expenseDevise',
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupérer les logs pour plusieurs transferts via leurs IDs.
     *
     * @param string $entityClass La classe de l'entité (Transfer::class)
     * @param array $objectIds Les IDs des objets concernés
     * @return array Liste des logs
     */
    public function findLogsForMultipleTransfers(string $entityClass, array $objectIds): array
    {
        return $this->createQueryBuilder('log')
            ->leftJoin('App\Entity\User', 'user', 'WITH', 'log.username = user.email')
            ->leftJoin('App\Entity\Transfer', 'transfer', 'WITH', 'log.objectId = transfer.id')
            ->leftJoin('transfer.fromMember', 'fromMember')
            ->leftJoin('transfer.toMember', 'toMember')
            ->where('log.objectClass = :entityClass')
            ->andWhere('log.objectId IN (:objectIds)')
            ->setParameter('entityClass', $entityClass)
            ->setParameter('objectIds', $objectIds)
            ->orderBy('log.loggedAt', 'DESC')
            ->select([
                'log.action AS action',
                'log.version AS version',
                'log.loggedAt AS loggedAt',
                'log.data AS data',
                'log.username AS username',
                'user.email AS userEmail',
                'user.firstName AS firstName',
                'user.lastName AS lastName',
                'transfer.amount AS transferAmount',
                'transfer.description AS transferDescription',
                'fromMember.nickname AS fromMemberName',
                'toMember.nickname AS toMemberName',
            ])
            ->getQuery()
            ->getResult();
    }
}

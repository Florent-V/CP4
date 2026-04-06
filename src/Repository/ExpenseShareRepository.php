<?php

namespace App\Repository;

use App\Entity\ExpenseShare;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExpenseShare>
 *
 * @method ExpenseShare|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExpenseShare|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExpenseShare[]    findAll()
 * @method ExpenseShare[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpenseShareRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExpenseShare::class);
    }

    public function save(ExpenseShare $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ExpenseShare $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}

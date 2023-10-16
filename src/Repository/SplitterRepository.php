<?php

namespace App\Repository;

use App\Entity\Splitter;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Splitter>
 *
 * @method Splitter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Splitter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Splitter[]    findAll()
 * @method Splitter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SplitterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Splitter::class);
    }

    public function save(Splitter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Splitter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findUserSplit(User $user, string $search = ''): Query
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.ownedBy = :user OR :user MEMBER OF s.members');

        if ($search) {
            $qb->andWhere($qb->expr()->like('s.name', ':search'))
                ->setParameter('search', '%' . $search . '%');
        }
        $qb->setParameter('user', $user);

        return $qb->getQuery();
    }

    public function findSplitter(string $search = ''): Query
    {
        $qb = $this->createQueryBuilder('s')
            ->orderBy('s.id', 'ASC');

        if ($search) {
            $qb->where($qb->expr()->orX(
                $qb->expr()->like('s.name', ':search'),
                $qb->expr()->like('s.description', ':search'),
            ))
                ->setParameter('search', '%' . $search . '%');
        }
        return $qb->getQuery();
    }

    public function calculateSum(string $id): array
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.uniqueId = :id')
            ->setParameter('id', $id)
            ->getQuery();

        return $qb->getResult();
    }

//    /**
//     * @return Splitter[] Returns an array of Splitter objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Splitter
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

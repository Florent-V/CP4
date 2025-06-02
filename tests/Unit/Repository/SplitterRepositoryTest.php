<?php

namespace App\Tests\Unit\Repository;

use App\Entity\AppUser;
use App\Entity\Member;
use App\Entity\Splitter;
use App\Repository\SplitterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class SplitterRepositoryTest extends TestCase
{
    private $managerRegistryMock;
    private $entityManagerMock;
    private $queryBuilderMock;
    private $queryMock;
    private $exprMock;
    private SplitterRepository $repository;

    protected function setUp(): void
    {
        $this->managerRegistryMock = $this->createMock(ManagerRegistry::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->queryBuilderMock = $this->createMock(QueryBuilder::class);
        $this->queryMock = $this->createMock(Query::class);
        $this->exprMock = $this->createMock(Expr::class);

        $this->managerRegistryMock->method('getManagerForClass')->willReturn($this->entityManagerMock);
        $this->entityManagerMock->method('createQueryBuilder')->willReturn($this->queryBuilderMock);
        $this->queryBuilderMock->method('select')->willReturnSelf();
        $this->queryBuilderMock->method('from')->willReturnSelf();
        $this->queryBuilderMock->method('where')->willReturnSelf();
        $this->queryBuilderMock->method('andWhere')->willReturnSelf();
        $this->queryBuilderMock->method('orWhere')->willReturnSelf();
        $this->queryBuilderMock->method('setParameter')->willReturnSelf();
        $this->queryBuilderMock->method('orderBy')->willReturnSelf();
        $this->queryBuilderMock->method('getQuery')->willReturn($this->queryMock);
        $this->queryBuilderMock->method('expr')->willReturn($this->exprMock);

        // Important: The repository constructor expects ManagerRegistry.
        // The ServiceEntityRepository uses this registry to get the EM.
        // We also need to ensure the createQueryBuilder('s') alias 's' is used.
        $this->entityManagerMock
            ->expects($this->any())
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilderMock);

        $this->queryBuilderMock
            ->expects($this->any())
            ->method($this->anything()) // Allow any method to be called on QB
            ->willReturnSelf(); // Return self for chaining

        $this->queryBuilderMock
             ->expects($this->any())
             ->method('getQuery')
             ->willReturn($this->queryMock);


        $this->repository = new SplitterRepository($this->managerRegistryMock);
    }

    public function testFindUserSplitNoMembers(): void
    {
        $members = new ArrayCollection();

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('where')
            ->with('1=0')
            ->willReturnSelf();

        $resultQuery = $this->repository->findUserSplit($members);
        $this->assertSame($this->queryMock, $resultQuery);
    }

    public function testFindUserSplitWithMembers(): void
    {
        $member1 = $this->createMock(Member::class);
        $member2 = $this->createMock(Member::class);
        $members = new ArrayCollection([$member1, $member2]);

        $this->queryBuilderMock
            ->expects($this->exactly(2))
            ->method('orWhere')
            ->withConsecutive(
                [':member0 MEMBER OF s.members'],
                [':member1 MEMBER OF s.members']
            )
            ->willReturnSelf();
        $this->queryBuilderMock
            ->expects($this->exactly(2))
            ->method('setParameter')
            ->withConsecutive(
                ['member0', $member1],
                ['member1', $member2]
            )
            ->willReturnSelf();

        $resultQuery = $this->repository->findUserSplit($members);
        $this->assertSame($this->queryMock, $resultQuery);
    }

    public function testFindAppUserSplitNoSearch(): void
    {
        $appUser = $this->createMock(AppUser::class);

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('where')
            ->with(':appUser MEMBER OF s.favoritedByUsers')
            ->willReturnSelf();
        $this->queryBuilderMock
            ->expects($this->exactly(2)) // Once for where, once for orWhere
            ->method('setParameter')
            ->with('appUser', $appUser)
            ->willReturnSelf();
        $this->queryBuilderMock
            ->expects($this->once())
            ->method('orWhere')
            ->with(':appUser = s.owner')
            ->willReturnSelf();
        $this->queryBuilderMock
            ->expects($this->never()) // No search term, so no andWhere for search
            ->method('andWhere');


        $resultQuery = $this->repository->findAppUserSplit($appUser, '');
        $this->assertSame($this->queryMock, $resultQuery);
    }

    public function testFindAppUserSplitWithSearch(): void
    {
        $appUser = $this->createMock(AppUser::class);
        $searchTerm = 'test search';

        $this->queryBuilderMock->expects($this->once())->method('where')->with(':appUser MEMBER OF s.favoritedByUsers')->willReturnSelf();
        $this->queryBuilderMock->expects($this->once())->method('orWhere')->with(':appUser = s.owner')->willReturnSelf();
        // setParameter for appUser will be called twice due to the two conditions above.
        // setParameter for search will be called once.
        $this->queryBuilderMock->expects($this->exactly(3))->method('setParameter')
            ->withConsecutive(
                ['appUser', $appUser],
                ['appUser', $appUser],
                ['search', '%' . $searchTerm . '%']
            )->willReturnSelf();


        $this->exprMock->expects($this->exactly(2))->method('like')
            ->withConsecutive(
                ['s.name', ':search'],
                ['s.description', ':search']
            )->willReturn('mocked_like_condition'); // Return a dummy string condition

        $this->exprMock->expects($this->once())->method('orX')
            ->with('mocked_like_condition', 'mocked_like_condition')
            ->willReturn('mocked_orx_condition');

        $this->queryBuilderMock->expects($this->once())->method('andWhere')
            ->with('mocked_orx_condition')
            ->willReturnSelf();


        $resultQuery = $this->repository->findAppUserSplit($appUser, $searchTerm);
        $this->assertSame($this->queryMock, $resultQuery);
    }

    public function testFindSplitterNoSearch(): void
    {
        $this->queryBuilderMock->expects($this->once())->method('orderBy')->with('s.id', 'ASC')->willReturnSelf();
        $this->queryBuilderMock->expects($this->never())->method('where'); // No search, no where

        $resultQuery = $this->repository->findSplitter('');
        $this->assertSame($this->queryMock, $resultQuery);
    }

    public function testFindSplitterWithSearch(): void
    {
        $searchTerm = 'find me';
        $this->queryBuilderMock->expects($this->once())->method('orderBy')->with('s.id', 'ASC')->willReturnSelf();

        $this->exprMock->expects($this->exactly(2))->method('like')
            ->withConsecutive(
                ['s.name', ':search'],
                ['s.description', ':search']
            )->willReturn('mocked_like_condition');

        $this->exprMock->expects($this->once())->method('orX')
            ->with('mocked_like_condition', 'mocked_like_condition')
            ->willReturn('mocked_orx_condition');

        $this->queryBuilderMock->expects($this->once())->method('where')
            ->with('mocked_orx_condition')
            ->willReturnSelf();
        $this->queryBuilderMock->expects($this->once())->method('setParameter')
            ->with('search', '%' . $searchTerm . '%')
            ->willReturnSelf();

        $resultQuery = $this->repository->findSplitter($searchTerm);
        $this->assertSame($this->queryMock, $resultQuery);
    }

    public function testCalculateSum(): void
    {
        $uniqueId = 'test-uuid-123';
        $expectedResult = [['sum_val' => 100]]; // Example structure

        $this->queryBuilderMock
            ->expects($this->once())
            ->method('andWhere') // In the actual code it's andWhere, not where for this method
            ->with('s.uniqueId = :id')
            ->willReturnSelf();
        $this->queryBuilderMock
            ->expects($this->once())
            ->method('setParameter')
            ->with('id', $uniqueId)
            ->willReturnSelf();

        $this->queryMock
            ->expects($this->once())
            ->method('getResult')
            ->willReturn($expectedResult);

        $actualResult = $this->repository->calculateSum($uniqueId);
        $this->assertEquals($expectedResult, $actualResult);
    }
}

<?php

namespace App\Tests\Unit\Service;

use App\Entity\Expense;
use App\Entity\Member;
use App\Entity\Splitter;
use App\Repository\MemberRepository;
use App\Service\BalanceCalculator;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class BalanceCalculatorTest extends TestCase
{
    private MemberRepository $memberRepositoryMock;
    private BalanceCalculator $balanceCalculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->memberRepositoryMock = $this->createMock(MemberRepository::class);
        $this->balanceCalculator = new BalanceCalculator($this->memberRepositoryMock);
    }

    public function testCalculateIndividualBalanceNoExpenses(): void
    {
        $splitter = $this->createMock(Splitter::class);
        $member1 = $this->createMock(Member::class);
        $member2 = $this->createMock(Member::class);

        $member1->method('getId')->willReturn(1);
        $member2->method('getId')->willReturn(2);

        $members = new ArrayCollection([$member1, $member2]);
        $splitter->method('getMembers')->willReturn($members);
        $splitter->method('getExpenses')->willReturn(new ArrayCollection());

        $balances = $this->balanceCalculator->calculateIndividualBalance($splitter);

        $this->assertCount(2, $balances);
        $this->assertEquals(0, $balances[1]);
        $this->assertEquals(0, $balances[2]);
    }

    public function testCalculateIndividualBalanceWithExpenses(): void
    {
        $splitter = $this->createMock(Splitter::class);
        $member1 = $this->createMock(Member::class);
        $member2 = $this->createMock(Member::class);
        $member3 = $this->createMock(Member::class);

        $member1->method('getId')->willReturn(1);
        $member2->method('getId')->willReturn(2);
        $member3->method('getId')->willReturn(3);

        $members = new ArrayCollection([$member1, $member2, $member3]);
        $splitter->method('getMembers')->willReturn($members);

        $expense1 = $this->createMock(Expense::class);
        $expense1->method('getAmount')->willReturn(30.0);
        $expense1->method('getPaidBy')->willReturn($member1); // Member 1 paid 30
        $expense1->method('getBeneficiaries')->willReturn(new ArrayCollection([$member1, $member2, $member3])); // For all 3

        $expense2 = $this->createMock(Expense::class);
        $expense2->method('getAmount')->willReturn(20.0);
        $expense2->method('getPaidBy')->willReturn($member2); // Member 2 paid 20
        $expense2->method('getBeneficiaries')->willReturn(new ArrayCollection([$member2, $member3])); // For member 2 and 3

        $splitter->method('getExpenses')->willReturn(new ArrayCollection([$expense1, $expense2]));

        $balances = $this->balanceCalculator->calculateIndividualBalance($splitter);

        // Expected balances:
        // Member 1: Paid 30. Share of Exp1 (10). Net: +20
        // Member 2: Paid 20. Share of Exp1 (10) + Share of Exp2 (10). Net: 0
        // Member 3: Paid 0. Share of Exp1 (10) + Share of Exp2 (10). Net: -20
        // The method calculates the deviation from the average of ( (30-10) + (20-10-10) + (-10-10) ) / 3 = (20 + 0 - 20)/3 = 0
        // So balances should be:
        // M1: 30 (paid) - 10 (exp1 share) = 20
        // M2: 20 (paid) - 10 (exp1 share) - 10 (exp2 share) = 0
        // M3: 0 (paid) - 10 (exp1 share) - 10 (exp2 share) = -20
        // The code then does: $balancePerId[$id] = $amount - $average; where $average is total/count. Total is 0 here.
        // So the values should be 20, 0, -20.

        $this->assertCount(3, $balances);
        $this->assertEquals(20.0, $balances[1]);
        $this->assertEquals(0.0, $balances[2]);
        $this->assertEquals(-20.0, $balances[3]);
    }

    public function testCalculateTransferSimpleCase(): void
    {
        // Balances: Member 1 owes 10, Member 2 is owed 10
        $balancePerId = [1 => -10.0, 2 => 10.0];

        $member1 = $this->createMock(Member::class);
        $member1->method('getId')->willReturn(1);
        $member2 = $this->createMock(Member::class);
        $member2->method('getId')->willReturn(2);

        $this->memberRepositoryMock->method('findOneBy')
            ->willReturnMap([
                [['id' => 1], null, $member1],
                [['id' => 2], null, $member2],
            ]);

        $transfers = $this->balanceCalculator->calculateTransfer($balancePerId);

        $this->assertCount(1, $transfers);
        $this->assertSame($member1, $transfers[0]['from']);
        $this->assertSame($member2, $transfers[0]['to']);
        $this->assertEquals(10.0, $transfers[0]['amount']);
    }

    public function testCalculateTransferComplexCase(): void
    {
        // Balances: M1: -20, M2: -10, M3: +5, M4: +25
        // Expected: M1 pays M4 20. M2 pays M4 5. M2 pays M3 5. (or other valid set)
        // The algorithm sorts by balance: M1 (-20), M2 (-10), M3 (5), M4 (25)
        // 1. M1 (-20) vs M4 (25). Delta = 20. M1 pays M4 20.
        //    Balances: M1 (0), M2 (-10), M3 (5), M4 (5)
        //    Transfers: [M1 -> M4, 20]
        // 2. M2 (-10) vs M4 (5). Delta = 5. M2 pays M4 5.
        //    Balances: M1 (0), M2 (-5), M3 (5), M4 (0)
        //    Transfers: [M1 -> M4, 20], [M2 -> M4, 5]
        // 3. M2 (-5) vs M3 (5). Delta = 5. M2 pays M3 5.
        //    Balances: M1 (0), M2 (0), M3 (0), M4 (0)
        //    Transfers: [M1 -> M4, 20], [M2 -> M4, 5], [M2 -> M3, 5]
        $balancePerId = [1 => -20.0, 2 => -10.0, 3 => 5.0, 4 => 25.0];

        $member1 = $this->createMock(Member::class); $member1->method('getId')->willReturn(1);
        $member2 = $this->createMock(Member::class); $member2->method('getId')->willReturn(2);
        $member3 = $this->createMock(Member::class); $member3->method('getId')->willReturn(3);
        $member4 = $this->createMock(Member::class); $member4->method('getId')->willReturn(4);

        $this->memberRepositoryMock->method('findOneBy')
            ->willReturnMap([
                [['id' => 1], null, $member1],
                [['id' => 2], null, $member2],
                [['id' => 3], null, $member3],
                [['id' => 4], null, $member4],
            ]);

        $transfers = $this->balanceCalculator->calculateTransfer($balancePerId);

        $this->assertCount(3, $transfers);

        // Check transfer 1: M1 -> M4, 20
        $this->assertTrue($this->findTransfer($transfers, $member1, $member4, 20.0));
        // Check transfer 2: M2 -> M4, 5
        $this->assertTrue($this->findTransfer($transfers, $member2, $member4, 5.0));
        // Check transfer 3: M2 -> M3, 5
        $this->assertTrue($this->findTransfer($transfers, $member2, $member3, 5.0));
    }

    // Helper function to find a specific transfer in the results
    private function findTransfer(array $transfers, Member $from, Member $to, float $amount): bool
    {
        foreach ($transfers as $transfer) {
            if ($transfer['from'] === $from && $transfer['to'] === $to && $transfer['amount'] === $amount) {
                return true;
            }
        }
        return false;
    }
}

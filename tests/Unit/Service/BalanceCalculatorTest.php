<?php

namespace App\Tests\Unit\Service;

use App\Entity\Expense;
use App\Entity\Member;
use App\Entity\Splitter;
use App\Entity\Transfer;
use App\Repository\MemberRepository;
use App\Service\BalanceCalculator;
use PHPUnit\Framework\TestCase;

class BalanceCalculatorTest extends TestCase
{
    private MemberRepository $memberRepository;
    private BalanceCalculator $balanceCalculator;

    protected function setUp(): void
    {
        $this->memberRepository = $this->createMock(MemberRepository::class);
        $this->balanceCalculator = new BalanceCalculator($this->memberRepository);
    }

    public function testCalculateIndividualBalanceWithoutTransfers(): void
    {
        // Create a splitter with 3 members
        $splitter = new Splitter();
        $member1 = $this->createMember(1, 'Alice');
        $member2 = $this->createMember(2, 'Bob');
        $member3 = $this->createMember(3, 'Charlie');

        $splitter->addMember($member1);
        $splitter->addMember($member2);
        $splitter->addMember($member3);

        // Alice pays 30€ for everyone
        $expense1 = new Expense();
        $expense1->setAmount(30);
        $expense1->setPaidBy($member1);
        $expense1->addBeneficiary($member1);
        $expense1->addBeneficiary($member2);
        $expense1->addBeneficiary($member3);
        $splitter->addExpense($expense1);

        // Calculate balance
        $balances = $this->balanceCalculator->calculateIndividualBalance($splitter);

        // Alice paid 30 but only owes 10, so balance is +20
        // Bob and Charlie each owe 10, so balance is -10 each
        $this->assertEquals(20, $balances[1], '', 0.01);
        $this->assertEquals(-10, $balances[2], '', 0.01);
        $this->assertEquals(-10, $balances[3], '', 0.01);
    }

    public function testCalculateIndividualBalanceWithTransfers(): void
    {
        // Create a splitter with 3 members
        $splitter = new Splitter();
        $member1 = $this->createMember(1, 'Alice');
        $member2 = $this->createMember(2, 'Bob');
        $member3 = $this->createMember(3, 'Charlie');

        $splitter->addMember($member1);
        $splitter->addMember($member2);
        $splitter->addMember($member3);

        // Alice pays 30€ for everyone
        $expense1 = new Expense();
        $expense1->setAmount(30);
        $expense1->setPaidBy($member1);
        $expense1->addBeneficiary($member1);
        $expense1->addBeneficiary($member2);
        $expense1->addBeneficiary($member3);
        $splitter->addExpense($expense1);

        // Bob transfers 10€ to Alice (Bob pays his debt)
        $transfer1 = new Transfer();
        $transfer1->setAmount(10);
        $transfer1->setFromMember($member2);
        $transfer1->setToMember($member1);
        $transfer1->setMadeAt(new \DateTime());
        $splitter->addTransfer($transfer1);

        // Calculate balance
        $balances = $this->balanceCalculator->calculateIndividualBalance($splitter);

        // After transfer:
        // Alice: was +20, receives 10, so now +10
        // Bob: was -10, gives 10, so now 0
        // Charlie: still -10
        $this->assertEquals(10, $balances[1], '', 0.01);
        $this->assertEquals(0, $balances[2], '', 0.01);
        $this->assertEquals(-10, $balances[3], '', 0.01);
    }

    public function testCalculateIndividualBalanceWithMultipleTransfers(): void
    {
        // Create a splitter with 3 members
        $splitter = new Splitter();
        $member1 = $this->createMember(1, 'Alice');
        $member2 = $this->createMember(2, 'Bob');
        $member3 = $this->createMember(3, 'Charlie');

        $splitter->addMember($member1);
        $splitter->addMember($member2);
        $splitter->addMember($member3);

        // Alice pays 60€ for everyone
        $expense1 = new Expense();
        $expense1->setAmount(60);
        $expense1->setPaidBy($member1);
        $expense1->addBeneficiary($member1);
        $expense1->addBeneficiary($member2);
        $expense1->addBeneficiary($member3);
        $splitter->addExpense($expense1);

        // Bob transfers 20€ to Alice
        $transfer1 = new Transfer();
        $transfer1->setAmount(20);
        $transfer1->setFromMember($member2);
        $transfer1->setToMember($member1);
        $transfer1->setMadeAt(new \DateTime());
        $splitter->addTransfer($transfer1);

        // Charlie transfers 20€ to Alice
        $transfer2 = new Transfer();
        $transfer2->setAmount(20);
        $transfer2->setFromMember($member3);
        $transfer2->setToMember($member1);
        $transfer2->setMadeAt(new \DateTime());
        $splitter->addTransfer($transfer2);

        // Calculate balance
        $balances = $this->balanceCalculator->calculateIndividualBalance($splitter);

        // After transfers:
        // Alice: was +40, receives 20+20=40, so now 0
        // Bob: was -20, gives 20, so now 0
        // Charlie: was -20, gives 20, so now 0
        $this->assertEquals(0, $balances[1], '', 0.01);
        $this->assertEquals(0, $balances[2], '', 0.01);
        $this->assertEquals(0, $balances[3], '', 0.01);
    }

    private function createMember(int $id, string $nickname): Member
    {
        $member = new Member();

        // Use reflection to set the private id property
        $reflection = new \ReflectionClass($member);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($member, $id);

        $member->setNickname($nickname);

        return $member;
    }
}

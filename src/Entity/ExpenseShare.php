<?php

namespace App\Entity;

use App\Repository\ExpenseShareRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ExpenseShareRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_expense_share_member', columns: ['expense_id', 'member_id'])]
class ExpenseShare
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Expense::class, inversedBy: 'shares')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Expense $expense = null;

    #[ORM\ManyToOne(targetEntity: Member::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Member $member = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero]
    private ?float $share = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExpense(): ?Expense
    {
        return $this->expense;
    }

    public function setExpense(?Expense $expense): static
    {
        $this->expense = $expense;

        return $this;
    }

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(?Member $member): static
    {
        $this->member = $member;

        return $this;
    }

    public function getShare(): ?float
    {
        return $this->share;
    }

    public function setShare(float $share): static
    {
        $this->share = $share;

        return $this;
    }
}

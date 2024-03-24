<?php

namespace App\Entity;

use App\Repository\MemberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MemberRepository::class)]
#[ORM\Table(name: '`member`')]
class Member
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    private ?string $nickname = null;

    #[ORM\ManyToOne(inversedBy: 'members')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Splitter $splitter = null;

    #[ORM\ManyToOne(inversedBy: 'members')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\Column]
    private ?bool $editor = false;

    #[ORM\OneToOne(mappedBy: 'owner', cascade: ['persist', 'remove'])]
    private ?Splitter $owned = null;

    #[ORM\OneToMany(mappedBy: 'addedBy', targetEntity: Expense::class, orphanRemoval: true)]
    private Collection $addedExpenses;

    #[ORM\OneToMany(mappedBy: 'paidBy', targetEntity: Expense::class, orphanRemoval: true)]
    private Collection $paidExpenses;

    #[ORM\ManyToMany(targetEntity: Expense::class, mappedBy: 'beneficiaries')]
    private Collection $expenses;

    public function __construct()
    {
        $this->addedExpenses = new ArrayCollection();
        $this->paidExpenses = new ArrayCollection();
        $this->expenses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): static
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getSplitter(): ?Splitter
    {
        return $this->splitter;
    }

    public function setSplitter(?Splitter $splitter): static
    {
        $this->splitter = $splitter;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function isEditor(): ?bool
    {
        return $this->editor;
    }

    public function setEditor(bool $editor): static
    {
        $this->editor = $editor;

        return $this;
    }

    public function getOwned(): ?Splitter
    {
        return $this->owned;
    }

    public function setOwned(Splitter $owned): static
    {
        // set the owning side of the relation if necessary
        if ($owned->getOwner() !== $this) {
            $owned->setOwner($this);
        }

        $this->owned = $owned;

        return $this;
    }

    /**
     * @return Collection<int, Expense>
     */
    public function getAddedExpenses(): Collection
    {
        return $this->addedExpenses;
    }

    public function addAddedExpense(Expense $addedExpense): static
    {
        if (!$this->addedExpenses->contains($addedExpense)) {
            $this->addedExpenses->add($addedExpense);
            $addedExpense->setAddedBy($this);
        }

        return $this;
    }

    public function removeAddedExpense(Expense $addedExpense): static
    {
        if ($this->addedExpenses->removeElement($addedExpense)) {
            // set the owning side to null (unless already changed)
            if ($addedExpense->getAddedBy() === $this) {
                $addedExpense->setAddedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Expense>
     */
    public function getPaidExpenses(): Collection
    {
        return $this->paidExpenses;
    }

    public function addPaidExpense(Expense $paidExpense): static
    {
        if (!$this->paidExpenses->contains($paidExpense)) {
            $this->paidExpenses->add($paidExpense);
            $paidExpense->setPaidBy($this);
        }

        return $this;
    }

    public function removePaidExpense(Expense $paidExpense): static
    {
        if ($this->paidExpenses->removeElement($paidExpense)) {
            // set the owning side to null (unless already changed)
            if ($paidExpense->getPaidBy() === $this) {
                $paidExpense->setPaidBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Expense>
     */
    public function getExpenses(): Collection
    {
        return $this->expenses;
    }

    public function addExpense(Expense $expense): static
    {
        if (!$this->expenses->contains($expense)) {
            $this->expenses->add($expense);
            $expense->addBeneficiary($this);
        }

        return $this;
    }

    public function removeExpense(Expense $expense): static
    {
        if ($this->expenses->removeElement($expense)) {
            $expense->removeBeneficiary($this);
        }

        return $this;
    }
}

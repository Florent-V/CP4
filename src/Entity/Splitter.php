<?php

namespace App\Entity;

use App\Repository\SplitterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SplitterRepository::class)]
class Splitter
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'splitters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SplitterCategory $category = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\OneToMany(
        mappedBy: 'splitter',
        targetEntity: Expense::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $expenses;

    #[ORM\Column(length: 255)]
    private ?string $uniqueId = null;

    #[ORM\OneToMany(
        mappedBy: 'splitter',
        targetEntity: Member::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $members;

    #[ORM\OneToOne(inversedBy: 'owned')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Member $owner = null;


    public function __construct()
    {
        $this->expenses = new ArrayCollection();
        $this->members = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCategory(): ?SplitterCategory
    {
        return $this->category;
    }

    public function setCategory(?SplitterCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Expense>
     */
    public function getExpenses(): Collection
    {
        return $this->expenses;
    }

    public function addExpense(Expense $expense): self
    {
        if (!$this->expenses->contains($expense)) {
            $this->expenses->add($expense);
            $expense->setSplitter($this);
        }

        return $this;
    }

    public function removeExpense(Expense $expense): self
    {
        if ($this->expenses->removeElement($expense)) {
            // set the owning side to null (unless already changed)
            if ($expense->getSplitter() === $this) {
                $expense->setSplitter(null);
            }
        }

        return $this;
    }

    public function getUniqueId(): ?string
    {
        return $this->uniqueId;
    }

    public function setUniqueId(string $uniqueId): self
    {
        $this->uniqueId = $uniqueId;

        return $this;
    }

    /**
     * @return Collection<int, Member>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(Member $member): static
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
            $member->setSplitter($this);
        }

        return $this;
    }

    public function removeMember(Member $member): static
    {
        if ($this->members->removeElement($member)) {
            // set the owning side to null (unless already changed)
            if ($member->getSplitter() === $this) {
                $member->setSplitter(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?Member
    {
        return $this->owner;
    }

    public function setOwner(Member $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function unsetOwner(): static
    {
        $this->owner = null;

        return $this;
    }
}

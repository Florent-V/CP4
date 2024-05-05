<?php

namespace App\Entity;

use App\Repository\ExpenseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ExpenseRepository::class)]
class Expense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $madeAt = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture = null;

    #[ORM\Column(length: 5)]
    private ?string $devise = null;

    #[ORM\ManyToOne(inversedBy: 'expenses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Splitter $splitter = null;

    #[ORM\ManyToOne(inversedBy: 'expenses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ExpenseCategory $category = null;

    #[ORM\ManyToOne(inversedBy: 'addedExpenses')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Member $addedBy = null;

    #[ORM\ManyToOne(inversedBy: 'paidExpenses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Member $paidBy = null;

    #[ORM\ManyToMany(targetEntity: Member::class, inversedBy: 'expenses')]
    #[Assert\Count(
        min: 1,
        minMessage: 'Vous devez avoir au moins un bénéficiare pour la dépense.'
    )]
    private Collection $beneficiaries;

    public function __construct()
    {
        $this->beneficiaries = new ArrayCollection();
    }

    public function getId(): ?int
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

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getMadeAt(): ?\DateTime
    {
        return $this->madeAt;
    }

    public function setMadeAt(\DateTime $madeAt): self
    {
        $this->madeAt = $madeAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getDevise(): ?string
    {
        return $this->devise;
    }

    public function setDevise(string $devise): self
    {
        $this->devise = $devise;

        return $this;
    }

    public function getSplitter(): ?Splitter
    {
        return $this->splitter;
    }

    public function setSplitter(?Splitter $splitter): self
    {
        $this->splitter = $splitter;

        return $this;
    }

    public function getCategory(): ?ExpenseCategory
    {
        return $this->category;
    }

    public function setCategory(?ExpenseCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getAddedBy(): ?Member
    {
        return $this->addedBy;
    }

    public function setAddedBy(?Member $addedBy): static
    {
        $this->addedBy = $addedBy;

        return $this;
    }

    public function getPaidBy(): ?Member
    {
        return $this->paidBy;
    }

    public function setPaidBy(?Member $paidBy): static
    {
        $this->paidBy = $paidBy;

        return $this;
    }

    /**
     * @return Collection<int, Member>
     */
    public function getBeneficiaries(): Collection
    {
        return $this->beneficiaries;
    }

    public function addBeneficiary(Member $beneficiary): static
    {
        if (!$this->beneficiaries->contains($beneficiary)) {
            $this->beneficiaries->add($beneficiary);
        }

        return $this;
    }

    public function removeBeneficiary(Member $beneficiary): static
    {
        $this->beneficiaries->removeElement($beneficiary);

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Entity\Trait\BlameableEntity;
use App\Enum\SplitType;
use App\Repository\ExpenseRepository;
use App\Validator\ValidExpenseShares;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Attribute as Vich;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Mapping\Annotation\SoftDeleteable;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: ExpenseRepository::class)]
#[Gedmo\Loggable]
#[SoftDeleteable]
#[Vich\Uploadable]
#[ValidExpenseShares]
class Expense
{
    use TimestampableEntity;
    use BlameableEntity;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Gedmo\Versioned]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $madeAt = null;

    #[ORM\Column]
    #[Gedmo\Versioned]
    private ?float $amount = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture = null;

    #[Vich\UploadableField(mapping: 'expense_picture', fileNameProperty: 'picture')]
    #[Assert\File(
        maxSize: '10M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'],
    )]
    private ?File $pictureFile = null;

    #[ORM\Column(length: 5)]
    private ?string $devise = null;

    #[ORM\ManyToOne(inversedBy: 'expenses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Splitter $splitter = null;

    #[ORM\ManyToOne(inversedBy: 'expenses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ExpenseCategory $category = null;

    #[ORM\ManyToOne(inversedBy: 'paidExpenses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Member $paidBy = null;

    #[ORM\ManyToMany(targetEntity: Member::class, inversedBy: 'expenses', fetch: 'EAGER')]
    #[Assert\Count(
        min: 1,
        minMessage: 'Vous devez avoir au moins un bénéficiare pour la dépense.'
    )]
    private Collection $beneficiaries;

    #[ORM\ManyToOne(inversedBy: 'expenses')]
    private ?AppUser $addedBy = null;

    #[ORM\Column(length: 20, enumType: SplitType::class)]
    private SplitType $splitType = SplitType::EQUAL;

    #[ORM\OneToMany(
        targetEntity: ExpenseShare::class,
        mappedBy: 'expense',
        cascade: ['persist', 'remove'],
        fetch: 'EAGER',
        orphanRemoval: true
    )]
    private Collection $shares;

    public function __construct()
    {
        $this->beneficiaries = new ArrayCollection();
        $this->shares = new ArrayCollection();
        $this->setCreatedAt(new DateTime());
        $this->setUpdatedAt(new DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPictureFile(): ?File
    {
        return $this->pictureFile;
    }

    public function setPictureFile(?File $pictureFile): Expense
    {
        $this->pictureFile = $pictureFile;
        if ($pictureFile) {
            $this->updatedAt = new DateTime('now');
        }
        return $this;
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

    public function getAddedBy(): ?AppUser
    {
        return $this->addedBy;
    }

    public function setAddedBy(?AppUser $addedBy): static
    {
        $this->addedBy = $addedBy;

        return $this;
    }

    public function getSplitType(): SplitType
    {
        return $this->splitType;
    }

    public function setSplitType(SplitType $splitType): static
    {
        $this->splitType = $splitType;

        return $this;
    }

    /**
     * @return Collection<int, ExpenseShare>
     */
    public function getShares(): Collection
    {
        return $this->shares;
    }

    public function addShare(ExpenseShare $share): static
    {
        if (!$this->shares->contains($share)) {
            $this->shares->add($share);
            $share->setExpense($this);
        }

        return $this;
    }

    public function removeShare(ExpenseShare $share): static
    {
        if ($this->shares->removeElement($share)) {
            if ($share->getExpense() === $this) {
                $share->setExpense(null);
            }
        }

        return $this;
    }

    public function getShareForMember(Member $member): ?ExpenseShare
    {
        foreach ($this->shares as $share) {
            if ($share->getMember() === $member) {
                return $share;
            }
        }

        return null;
    }
}

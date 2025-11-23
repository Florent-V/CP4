<?php

namespace App\Entity;

use App\Entity\Trait\BlameableEntity;
use App\Repository\TransferRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Mapping\Annotation\SoftDeleteable;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: TransferRepository::class)]
#[Gedmo\Loggable]
#[SoftDeleteable]
class Transfer
{
    use TimestampableEntity;
    use BlameableEntity;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Gedmo\Versioned]
    #[Assert\NotBlank(message: 'Le montant est obligatoire')]
    #[Assert\Positive(message: 'Le montant doit être positif')]
    private ?float $amount = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: 'La date est obligatoire')]
    private ?\DateTime $madeAt = null;

    #[ORM\ManyToOne(inversedBy: 'transfers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Le splitter est obligatoire')]
    private ?Splitter $splitter = null;

    #[ORM\ManyToOne(inversedBy: 'transfersGiven')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Le membre qui donne l\'argent est obligatoire')]
    private ?Member $fromMember = null;

    #[ORM\ManyToOne(inversedBy: 'transfersReceived')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Le membre qui reçoit l\'argent est obligatoire')]
    private ?Member $toMember = null;

    #[ORM\ManyToOne(inversedBy: 'transfers')]
    private ?AppUser $addedBy = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    public function __construct()
    {
        $this->setCreatedAt(new DateTime());
        $this->setUpdatedAt(new DateTime());
        $this->setMadeAt(new DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMadeAt(): ?\DateTime
    {
        return $this->madeAt;
    }

    public function setMadeAt(\DateTime $madeAt): self
    {
        $this->madeAt = $madeAt;

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

    public function getFromMember(): ?Member
    {
        return $this->fromMember;
    }

    public function setFromMember(?Member $fromMember): self
    {
        $this->fromMember = $fromMember;

        return $this;
    }

    public function getToMember(): ?Member
    {
        return $this->toMember;
    }

    public function setToMember(?Member $toMember): self
    {
        $this->toMember = $toMember;

        return $this;
    }

    public function getAddedBy(): ?AppUser
    {
        return $this->addedBy;
    }

    public function setAddedBy(?AppUser $addedBy): self
    {
        $this->addedBy = $addedBy;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Entity\Trait\BlameableEntity;
use App\Repository\ShareCodeRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: ShareCodeRepository::class)]
#[ORM\Table(name: 'share_code')]
#[ORM\Index(name: 'idx_share_code', columns: ['code'])]
#[ORM\Index(name: 'idx_entity', columns: ['entity_type', 'entity_id'])]
#[Gedmo\Loggable]
class ShareCode
{
    use TimestampableEntity;
    use BlameableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 20, options: ['default' => 'code'])]
    #[Assert\Choice(choices: ['code', 'link'], message: 'Type invalide')]
    private string $type = 'code';

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $entityType = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $entityId = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $expiresAt = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isUsed = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $usedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $usedBy = null;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->expiresAt = new DateTime('+1 hour');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getEntityType(): ?string
    {
        return $this->entityType;
    }

    public function setEntityType(string $entityType): static
    {
        $this->entityType = $entityType;
        return $this;
    }

    public function getEntityId(): ?string
    {
        return $this->entityId;
    }

    public function setEntityId(string $entityId): static
    {
        $this->entityId = $entityId;
        return $this;
    }

    public function getExpiresAt(): ?DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(DateTimeInterface $expiresAt): static
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    public function isUsed(): bool
    {
        return $this->isUsed;
    }

    public function setIsUsed(bool $isUsed): static
    {
        $this->isUsed = $isUsed;
        return $this;
    }

    public function getUsedAt(): ?DateTimeInterface
    {
        return $this->usedAt;
    }

    public function setUsedAt(?DateTimeInterface $usedAt): static
    {
        $this->usedAt = $usedAt;
        return $this;
    }

    public function getUsedBy(): ?string
    {
        return $this->usedBy;
    }

    public function setUsedBy(?string $usedBy): static
    {
        $this->usedBy = $usedBy;
        return $this;
    }

    public function isExpired(): bool
    {
        return new DateTime() > $this->expiresAt;
    }

    public function isValid(): bool
    {
        return !$this->isUsed() && !$this->isExpired();
    }

    public function markAsUsed(?string $usedBy = null): static
    {
        $this->isUsed = true;
        $this->usedAt = new DateTime();
        $this->usedBy = $usedBy;
        return $this;
    }
}

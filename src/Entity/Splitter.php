<?php

namespace App\Entity;

use App\Entity\Trait\BlameableEntity;
use App\Interface\ShareableEntityInterface;
use App\Repository\SplitterRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Mapping\Annotation\SoftDeleteable;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @SuppressWarnings("PHPMD.TooManyPublicMethods")
 */
#[ORM\Entity(repositoryClass: SplitterRepository::class)]
#[Gedmo\Loggable]
#[SoftDeleteable]
class Splitter implements ShareableEntityInterface
{
    use TimestampableEntity;
    use BlameableEntity;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[ORM\Column(length: 50)]
    #[Gedmo\Versioned]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 5,
        max: 50,
        minMessage: 'Le nom doit faire au moins {{ limit }} caractères',
        maxMessage: 'Le nom ne doit pas dépasser {{ limit }} caractères',
    )]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'splitters')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Vous devez sélectionner une catégorie')]
    #[Assert\Valid]
    private ?SplitterCategory $category = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Gedmo\Versioned]
    private ?string $description = null;

    #[ORM\OneToMany(
        targetEntity: Expense::class,
        mappedBy: 'splitter',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $expenses;

    #[ORM\Column(length: 255)]
    private ?string $uniqueId = null;

    #[ORM\OneToMany(
        targetEntity: Member::class,
        mappedBy: 'splitter',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    #[Assert\Count(
        min: 1,
        minMessage: 'Vous devez avoir au moins un membre dans votre Splitter.'
    )]
    private Collection $members;

    #[ORM\ManyToMany(targetEntity: AppUser::class, mappedBy: 'favoriteSplitters')]
    private Collection $favoritedByUsers;

    #[ORM\ManyToOne(inversedBy: 'ownedSplitters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AppUser $owner = null;

    #[ORM\OneToMany(
        targetEntity: Transfer::class,
        mappedBy: 'splitter',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $transfers;

    public function __construct()
    {
        $this->expenses = new ArrayCollection();
        $this->members = new ArrayCollection();
        $this->favoritedByUsers = new ArrayCollection();
        $this->transfers = new ArrayCollection();
        $this->setCreatedAt(new DateTime());
        $this->setUpdatedAt(new DateTime());
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

    /**
     * @return Collection<int, AppUser>
     */
    public function getFavoritedByUsers(): Collection
    {
        return $this->favoritedByUsers;
    }

    public function addFavoritedByUser(AppUser $favoritedByUser): static
    {
        if (!$this->favoritedByUsers->contains($favoritedByUser)) {
            $this->favoritedByUsers->add($favoritedByUser);
            $favoritedByUser->addFavoriteSplitter($this);
        }

        return $this;
    }

    public function removeFavoritedByUser(AppUser $favoritedByUser): static
    {
        if ($this->favoritedByUsers->removeElement($favoritedByUser)) {
            $favoritedByUser->removeFavoriteSplitter($this);
        }

        return $this;
    }

    public function getOwner(): ?AppUser
    {
        return $this->owner;
    }

    public function setOwner(?AppUser $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getShareableType(): string
    {
        return self::class;
    }

    public function getDisplayName(): string
    {
        return $this->name ?? 'Splitter';
    }

    public function canBeAccessedBy(mixed $user): bool
    {
        return true;
    }

    public function canBeSharedBy(UserInterface $user): bool
    {
        // Seul le propriétaire ou un membre peut partager le splitter
        if ($user instanceof User && $user->getAppUser()) {
            return $this->getOwner() === $user->getAppUser() ||
                   $user->getAppUser()->getFavoriteSplitters()->contains($this);
        }
        return false;
    }

    public function grantAccessTo(mixed $user): void
    {
        // Ajouter l'utilisateur aux favoris pour qu'il ait accès au splitter
        if ($user instanceof User && $user->getAppUser()) {
            $this->addFavoritedByUser($user->getAppUser());
        }
    }

    /**
     * @return Collection<int, Transfer>
     */
    public function getTransfers(): Collection
    {
        return $this->transfers;
    }

    public function addTransfer(Transfer $transfer): self
    {
        if (!$this->transfers->contains($transfer)) {
            $this->transfers->add($transfer);
            $transfer->setSplitter($this);
        }

        return $this;
    }

    public function removeTransfer(Transfer $transfer): self
    {
        if ($this->transfers->removeElement($transfer)) {
            // set the owning side to null (unless already changed)
            if ($transfer->getSplitter() === $this) {
                $transfer->setSplitter(null);
            }
        }

        return $this;
    }
}

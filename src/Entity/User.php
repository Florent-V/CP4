<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    private ?string $pseudo = null;

    #[ORM\Column(length: 100)]
    private ?string $firstName = null;

    #[ORM\Column(length: 100)]
    private ?string $lastName = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $isVerified = false;

    #[ORM\Column(length: 20)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture = null;

    #[ORM\OneToMany(mappedBy: 'ownedBy', targetEntity: Splitter::class, orphanRemoval: true)]
    private Collection $splittersOwned;

    #[ORM\ManyToMany(targetEntity: Splitter::class, mappedBy: 'members')]
    private Collection $splitters;

    #[ORM\OneToMany(mappedBy: 'addedBy', targetEntity: ExpenseCategory::class, orphanRemoval: true)]
    private Collection $categories;

    public function __construct()
    {
        $this->splittersOwned = new ArrayCollection();
        $this->splitters = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

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

    /**
     * @return Collection<int, Splitter>
     */
    public function getSplittersOwned(): Collection
    {
        return $this->splittersOwned;
    }

    public function addSplittersOwned(Splitter $splittersOwned): self
    {
        if (!$this->splittersOwned->contains($splittersOwned)) {
            $this->splittersOwned->add($splittersOwned);
            $splittersOwned->setOwnedBy($this);
        }

        return $this;
    }

    public function removeSplittersOwned(Splitter $splittersOwned): self
    {
        if ($this->splittersOwned->removeElement($splittersOwned)) {
            // set the owning side to null (unless already changed)
            if ($splittersOwned->getOwnedBy() === $this) {
                $splittersOwned->setOwnedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Splitter>
     */
    public function getSplitters(): Collection
    {
        return $this->splitters;
    }

    public function addSplitter(Splitter $splitter): self
    {
        if (!$this->splitters->contains($splitter)) {
            $this->splitters->add($splitter);
            $splitter->addMember($this);
        }

        return $this;
    }

    public function removeSplitter(Splitter $splitter): self
    {
        if ($this->splitters->removeElement($splitter)) {
            $splitter->removeMember($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, ExpenseCategory>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(ExpenseCategory $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setAddedBy($this);
        }

        return $this;
    }

    public function removeCategory(ExpenseCategory $category): self
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getAddedBy() === $this) {
                $category->setAddedBy(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\OneToMany(mappedBy: 'creator', targetEntity: Group::class)]
    private Collection $creatorOf;

    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'members')]
    private Collection $memberOf;

    public function __construct()
    {
        $this->creatorOf = new ArrayCollection();
        $this->memberOf = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, Group>
     */
    public function getCreatorOf(): Collection
    {
        return $this->creatorOf;
    }

    public function addCreatorOf(Group $creatorOf): self
    {
        if (!$this->creatorOf->contains($creatorOf)) {
            $this->creatorOf->add($creatorOf);
            $creatorOf->setCreator($this);
        }

        return $this;
    }

    public function removeCreatorOf(Group $creatorOf): self
    {
        if ($this->creatorOf->removeElement($creatorOf)) {
            // set the owning side to null (unless already changed)
            if ($creatorOf->getCreator() === $this) {
                $creatorOf->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getMemberOf(): Collection
    {
        return $this->memberOf;
    }

    public function addMemberOf(Group $memberOf): self
    {
        if (!$this->memberOf->contains($memberOf)) {
            $this->memberOf->add($memberOf);
            $memberOf->addMember($this);
        }

        return $this;
    }

    public function removeMemberOf(Group $memberOf): self
    {
        if ($this->memberOf->removeElement($memberOf)) {
            $memberOf->removeMember($this);
        }

        return $this;
    }
}

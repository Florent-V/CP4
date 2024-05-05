<?php

namespace App\Entity;

use App\Repository\AppUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AppUserRepository::class)]
class AppUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'appUser', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToMany(targetEntity: Splitter::class, inversedBy: 'favoritedByUsers')]
    private Collection $favoriteSplitters;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Splitter::class, orphanRemoval: true)]
    private Collection $ownedSplitters;

    public function __construct()
    {
        $this->favoriteSplitters = new ArrayCollection();
        $this->ownedSplitters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Splitter>
     */
    public function getFavoriteSplitters(): Collection
    {
        return $this->favoriteSplitters;
    }

    public function addFavoriteSplitter(Splitter $favoriteSplitter): static
    {
        if (!$this->favoriteSplitters->contains($favoriteSplitter)) {
            $this->favoriteSplitters->add($favoriteSplitter);
        }

        return $this;
    }

    public function removeFavoriteSplitter(Splitter $favoriteSplitter): static
    {
        $this->favoriteSplitters->removeElement($favoriteSplitter);

        return $this;
    }

    /**
     * @return Collection<int, Splitter>
     */
    public function getOwnedSplitters(): Collection
    {
        return $this->ownedSplitters;
    }

    public function addOwnedSplitter(Splitter $ownedSplitter): static
    {
        if (!$this->ownedSplitters->contains($ownedSplitter)) {
            $this->ownedSplitters->add($ownedSplitter);
            $ownedSplitter->setOwner($this);
        }

        return $this;
    }

    public function removeOwnedSplitter(Splitter $ownedSplitter): static
    {
        if ($this->ownedSplitters->removeElement($ownedSplitter)) {
            // set the owning side to null (unless already changed)
            if ($ownedSplitter->getOwner() === $this) {
                $ownedSplitter->setOwner(null);
            }
        }

        return $this;
    }
}

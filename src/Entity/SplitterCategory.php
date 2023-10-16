<?php

namespace App\Entity;

use App\Repository\SplitterCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SplitterCategoryRepository::class)]
class SplitterCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Splitter::class, orphanRemoval: true)]
    private Collection $splitters;

    public function __construct()
    {
        $this->splitters = new ArrayCollection();
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
            $splitter->setCategory($this);
        }

        return $this;
    }

    public function removeSplitter(Splitter $splitter): self
    {
        if ($this->splitters->removeElement($splitter)) {
            // set the owning side to null (unless already changed)
            if ($splitter->getCategory() === $this) {
                $splitter->setCategory(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\ToDoListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ToDoListRepository::class)]
class ToDoList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'toDoList', targetEntity: ToDoItem::class, orphanRemoval: true)]
    private Collection $toDoItems;

    public function __construct()
    {
        $this->toDoItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, ToDoItem>
     */
    public function getToDoItems(): Collection
    {
        return $this->toDoItems;
    }

    public function addToDoItem(ToDoItem $toDoItem): static
    {
        if (!$this->toDoItems->contains($toDoItem)) {
            $this->toDoItems->add($toDoItem);
            $toDoItem->setToDoList($this);
        }

        return $this;
    }

    public function removeToDoItem(ToDoItem $toDoItem): static
    {
        if ($this->toDoItems->removeElement($toDoItem)) {
            // set the owning side to null (unless already changed)
            if ($toDoItem->getToDoList() === $this) {
                $toDoItem->setToDoList(null);
            }
        }

        return $this;
    }
}

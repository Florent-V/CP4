<?php

namespace App\Twig\Form;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class DeleteForm
{
    public string $entity;
    public string $id;
    public ?string $id2 = null;
    public string $title;

    public function getRoute(): string
    {
        return match ($this->entity) {
            'splitter' => 'app_splitter_delete',
            'expense' => 'app_expense_delete',
            default => '',
        };
    }

    public function getId(): string
    {
        return $this->id;
    }
    public function getTitle(): string
    {
        return $this->title;
    }

    public function getId2(): string
    {
        return $this->id2;
    }

    public function setId2(string $id2): void
    {
        $this->id2 = $id2;
    }
}

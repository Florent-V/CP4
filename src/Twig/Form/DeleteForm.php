<?php

namespace App\Twig\Form;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class DeleteForm
{
    public string $entity;
    public string $id;
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
}

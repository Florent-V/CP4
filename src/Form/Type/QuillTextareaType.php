<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuillTextareaType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => [
                'class' => 'input-hidden-accessible',
                'data-quillTextareaType-target' => 'input',
            ],
        ]);
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }
}

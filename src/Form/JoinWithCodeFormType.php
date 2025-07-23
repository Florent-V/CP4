<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class JoinWithCodeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'Code de partage',
                'attr' => [
                    'placeholder' => 'Entrez le code à 6 chiffres',
                    'class' => 'form-control text-center',
                    'maxlength' => 6,
                    'pattern' => '[0-9]{6}',
                    'inputmode' => 'numeric',
                    'autocomplete' => 'off',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez saisir un code']),
                    new Assert\Length(6),
                    new Assert\Regex([
                        'pattern' => '/^\d{6}$/',
                        'message' => 'Le code doit contenir uniquement des chiffres'
                    ])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rejoindre',
                'attr' => ['class' => 'btn btn-primary']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
